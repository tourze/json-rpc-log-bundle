<?php

namespace Tourze\JsonRPCLogBundle\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;
use Symfony\Contracts\Service\ResetInterface;
use Tourze\BacktraceHelper\Backtrace;
use Tourze\BacktraceHelper\ExceptionPrinter;
use Tourze\DoctrineAsyncBundle\Service\DoctrineService;
use Tourze\DoctrineHelper\ReflectionHelper;
use Tourze\JsonRPC\Core\Event\MethodExecuteFailureEvent;
use Tourze\JsonRPC\Core\Event\MethodExecuteSuccessEvent;
use Tourze\JsonRPC\Core\Event\OnExceptionEvent;
use Tourze\JsonRPC\Core\Event\RequestStartEvent;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Exception\JsonRpcException;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\JsonRPCLogBundle\Entity\RequestLog;
use Tourze\JsonRPCLogBundle\Procedure\LogFormatProcedure;
use Yiisoft\Json\Json;
use Yiisoft\Strings\StringHelper;

/**
 * 监听JsonRPC Server响应接口，并定制一些处理逻辑
 */
#[AutoconfigureTag('as-coroutine')]
class LogSubscriber implements ResetInterface
{
    private Stopwatch $stopwatch;

    private ?StopwatchEvent $event = null;

    public function __construct(
        private readonly LoggerInterface $procedureLogger,
        private readonly DoctrineService $doctrineService,
    ) {
        $this->stopwatch = new Stopwatch();
    }

    public function reset(): void
    {
        $this->event = null;
        $this->stopwatch->reset();
    }

    #[AsEventListener]
    public function onRequestStart(RequestStartEvent $event): void
    {
        $this->event = $this->stopwatch->start('json-rpc-logger');
    }

    #[AsEventListener(priority: 99)]
    public function onSuccess(MethodExecuteSuccessEvent $event): void
    {
        try {
            $this->procedureLogger->info('JsonRPC执行成功', [
                'result' => StringHelper::truncateMiddle(
                    Json::encode($event->getResult()),
                    intval($_ENV['JSON_RPC_LOG_RESULT_LENGTH'] ?? 1000),
                ),
                'duration' => $event->getEndTime()->diffInMicroseconds($event->getStartTime(), true),
            ]);
        } catch (\Throwable $exception) {
            $this->procedureLogger->warning('JsonRPC执行成功，但序列化日志失败', [
                'exception' => $exception,
            ]);
        }

        $method = $event->getMethod();
        [$logResult, $logRequest, $logResponse] = $this->getLogConfig(ReflectionHelper::getClassReflection($event->getMethod()), $event);
        if (!$logResult) {
            return;
        }

        $log = new RequestLog();
        if ($logRequest) {
            $log->setRequest(Json::encode([
                'id' => $event->getJsonRpcRequest()->getId(),
                'jsonrpc' => $event->getJsonRpcRequest()->getJsonrpc(),
                'method' => $event->getJsonRpcRequest()->getMethod(),
                'params' => $event->getJsonRpcRequest()->getParams()->toArray(),
            ]));
        }

        if ($logResponse) {
            $log->setResponse(Json::encode($event->getResult()));
        }

        if ($this->event) {
            $log->setStopwatchDuration($this->event->getDuration());
            $log->setStopwatchResult(strval($this->event));
        }

        $log->setApiName($method::class);
        if ($method instanceof LogFormatProcedure) {
            $log->setDescription($method->generateFormattedLogText($event->getJsonRpcRequest()));
        }

        try {
            $this->doctrineService->asyncInsert($log);
        } catch (\Throwable $exception) {
            $this->procedureLogger->error('记录JsonRPC日志时发生错误', [
                'exception' => $exception,
                'log' => $log,
            ]);
        }
    }

    #[AsEventListener(priority: 99)]
    public function onFailure(MethodExecuteFailureEvent $event): void
    {
        if ($event->getException() instanceof ApiException) {
            $this->procedureLogger->warning('JsonRPC执行时发生预期错误：' . $event->getException()->getMessage(), [
                'exception' => $event->getException(),
            ]);
        } elseif ($event->getException() instanceof \AssertionError) {
            $this->procedureLogger->warning('JsonRPC执行时发生断言错误：' . $event->getException()->getMessage(), [
                'exception' => $event->getException(),
            ]);
        } else {
            $this->procedureLogger->error('JsonRPC执行时发生未知错误：' . $event->getException()->getMessage(), [
                'exception' => $event->getException(),
                'backtrace' => Backtrace::create()->toString(),
            ]);
        }

        $method = $event->getMethod();
        [$logResult, $logRequest, $logResponse] = $this->getLogConfig(ReflectionHelper::getClassReflection($method), $event);
        if (!$logResult) {
            return;
        }

        $log = new RequestLog();
        if ($logRequest) {
            $log->setRequest(Json::encode([
                'id' => $event->getJsonRpcRequest()->getId(),
                'jsonrpc' => $event->getJsonRpcRequest()->getJsonrpc(),
                'method' => $event->getJsonRpcRequest()->getMethod(),
                'params' => $event->getJsonRpcRequest()->getParams()->toArray(),
            ]));
        }

        $log->setResponse($event->getException()->getMessage());
        $log->setException(ExceptionPrinter::exception($event->getException()));

        if ($this->event) {
            $log->setStopwatchDuration($this->event->getDuration());
            $log->setStopwatchResult(strval($this->event));
        }

        $log->setApiName($method::class);
        if ($method instanceof LogFormatProcedure) {
            $log->setDescription($method->generateFormattedLogText($event->getJsonRpcRequest()));
        }

        try {
            $this->doctrineService->asyncInsert($log);
        } catch (\Throwable $exception) {
            $this->procedureLogger->error('记录JsonRPC请求日志时发生错误', [
                'exception' => $exception,
            ]);
        }
    }

    #[AsEventListener]
    public function onException(OnExceptionEvent $event): void
    {
        if ('dev' === $_ENV['APP_ENV']) {
            if (!$event->getException() instanceof JsonRpcException) {
                throw $event->getException();
            }
        }

        $this->procedureLogger->error("JsonRPC执行{$event->getFromJsonRpcRequest()?->getMethod()}时发生异常", [
            'exception' => $event->getException(),
        ]);

        // TODO 思考下，这种接口是否应该记录到数据库
    }

    private function getLogConfig(\ReflectionClass $reflectionClass, MethodExecuteFailureEvent|MethodExecuteSuccessEvent $event): array
    {
        // 如果发生了不是预期的异常，那么我们任何情况都记录日志
        if ($event instanceof MethodExecuteFailureEvent && !($event->getException() instanceof ApiException)) {
            return [
                true, // 是否记录日志
                true, // 是否记录request
                true, // 是否记录response
            ];
        }

        $instance = $reflectionClass->getAttributes(Log::class);
        if (empty($instance)) {
            return [
                false, // 是否记录日志
                false, // 是否记录request
                false, // 是否记录response
            ];
        }
        $instance = $instance[0]->newInstance();
        /* @var Log|null $instance */

        return [
            true, // 是否记录日志
            $instance->request, // 是否记录request
            $instance->response, // 是否记录response
        ];
    }
}
