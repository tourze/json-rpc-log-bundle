<?php

namespace Tourze\JsonRPCLogBundle\Logger;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Contracts\Service\ResetInterface;
use Tourze\JsonRPC\Core\Event\RequestStartEvent;
use Tourze\JsonRPC\Core\Event\ResponseSendingEvent;
use Yiisoft\Json\Json;

/**
 * 如果当前正在执行 JsonRPC 方法，我们就将 Payload 打到日志那一行，以减少我们调试日志时的重复查询逻辑
 */
#[AutoconfigureTag(name: 'monolog.processor')]
#[AutoconfigureTag(name: 'as-coroutine')]
class PayloadLogProcessor implements ProcessorInterface, ResetInterface
{
    private ?string $payload = null;

    public function __invoke(LogRecord $record): LogRecord
    {
        if (null !== $this->payload) {
            $record->extra['json_rpc_payload'] = $this->payload;
        }

        return $record;
    }

    /**
     * 越大，优先级越高
     */
    #[AsEventListener(priority: 2048)]
    public function onRequest(RequestStartEvent $event): void
    {
        $this->payload = $event->getPayload();
        try {
            // 尝试转为JSON，那样子日志比较好查
            $this->payload = Json::decode($this->payload);
        } catch (\Throwable $exception) {
        }
    }

    /**
     * 结束时尽可能晚处理
     */
    #[AsEventListener(priority: -2048)]
    public function noResponse(ResponseSendingEvent $event): void
    {
        $this->payload = null;
    }

    public function reset(): void
    {
        $this->payload = null;
    }
}
