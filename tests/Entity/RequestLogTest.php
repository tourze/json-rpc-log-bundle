<?php

namespace Tourze\JsonRPCLogBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPCLogBundle\Entity\RequestLog;
use Tourze\JsonRPCLogBundle\Event\JsonRpcLogFormatEvent;
use Yiisoft\Json\Json;

class RequestLogTest extends TestCase
{
    public function testGetterAndSetter(): void
    {
        $requestLog = new RequestLog();

        // 测试 Request 相关方法
        $requestData = Json::encode(['test' => 'data']);
        $requestLog->setRequest($requestData);
        $this->assertSame($requestData, $requestLog->getRequest());

        // 测试 Response 相关方法
        $responseData = Json::encode(['response' => 'data']);
        $requestLog->setResponse($responseData);
        $this->assertSame($responseData, $requestLog->getResponse());

        // 测试 Exception 相关方法
        $exceptionData = 'Test exception';
        $requestLog->setException($exceptionData);
        $this->assertSame($exceptionData, $requestLog->getException());

        // 测试 ServerIp 相关方法
        $serverIp = '127.0.0.1';
        $requestLog->setServerIp($serverIp);
        $this->assertSame($serverIp, $requestLog->getServerIp());

        // 测试 StopwatchResult 相关方法
        $stopwatchResult = 'Test result';
        $requestLog->setStopwatchResult($stopwatchResult);
        $this->assertSame($stopwatchResult, $requestLog->getStopwatchResult());

        // 测试 StopwatchDuration 相关方法
        $stopwatchDuration = '10.5';
        $requestLog->setStopwatchDuration($stopwatchDuration);
        $this->assertSame($stopwatchDuration, $requestLog->getStopwatchDuration());

        // 测试 ApiName 相关方法
        $apiName = 'Test.Api';
        $requestLog->setApiName($apiName);
        $this->assertSame($apiName, $requestLog->getApiName());

        // 测试 CreatedFromIp 相关方法
        $createdFromIp = '192.168.1.1';
        $requestLog->setCreatedFromIp($createdFromIp);
        $this->assertSame($createdFromIp, $requestLog->getCreatedFromIp());

        // 测试 CreatedFromUa 相关方法
        $createdFromUa = 'Test UA';
        $requestLog->setCreatedFromUa($createdFromUa);
        $this->assertSame($createdFromUa, $requestLog->getCreatedFromUa());

        // 测试 CreateTime 相关方法
        $createTime = new \DateTime();
        $requestLog->setCreateTime($createTime);
        $this->assertSame($createTime, $requestLog->getCreateTime());

        // 测试 CreatedBy 相关方法
        $createdBy = 'Test User';
        $requestLog->setCreatedBy($createdBy);
        $this->assertSame($createdBy, $requestLog->getCreatedBy());
    }

    public function testRenderTrackUser(): void
    {
        $requestLog = new RequestLog();

        // 用户为空的情况
        $this->assertSame('', $requestLog->renderTrackUser());

        // 设置用户
        $createdBy = 'Test User';
        $requestLog->setCreatedBy($createdBy);
        $this->assertSame($createdBy, $requestLog->renderTrackUser());
    }

    public function testRenderStatus(): void
    {
        $requestLog = new RequestLog();

        // 无异常情况
        $this->assertSame('成功', $requestLog->renderStatus());

        // 有异常情况
        $requestLog->setException('Test Exception');
        $this->assertSame('异常', $requestLog->renderStatus());
    }

    public function testRenderHumanizeMessage(): void
    {
        $requestLog = new RequestLog();
        $requestData = Json::encode([
            'method' => 'test.method',
            'params' => ['id' => 1]
        ]);
        $requestLog->setRequest($requestData);

        // 创建模拟的 EventDispatcher
        /** @var EventDispatcherInterface&\PHPUnit\Framework\MockObject\MockObject $eventDispatcher */
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        // 配置模拟对象的预期行为
        $eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function (JsonRpcLogFormatEvent $event) {
                // 验证事件对象
                $this->assertInstanceOf(JsonRpcLogFormatEvent::class, $event);
                $this->assertSame('test.method', $event->getResult());

                // 修改结果以便我们可以验证
                $event->setResult('Formatted: test.method');
                return $event;
            });

        // 调用并验证结果
        $result = $requestLog->renderHumanizeMessage($eventDispatcher);
        $this->assertSame('Formatted: test.method', $result);
    }
}
