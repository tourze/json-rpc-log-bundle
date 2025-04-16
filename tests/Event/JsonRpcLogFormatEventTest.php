<?php

namespace Tourze\JsonRPCLogBundle\Tests\Event;

use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCLogBundle\Entity\RequestLog;
use Tourze\JsonRPCLogBundle\Event\JsonRpcLogFormatEvent;

class JsonRpcLogFormatEventTest extends TestCase
{
    private JsonRpcLogFormatEvent $event;

    protected function setUp(): void
    {
        $this->event = new JsonRpcLogFormatEvent();
    }

    public function testLogGetterAndSetter(): void
    {
        // 创建模拟 RequestLog 对象
        /** @var RequestLog&\PHPUnit\Framework\MockObject\MockObject $log */
        $log = $this->createMock(RequestLog::class);

        // 测试设置和获取
        $this->event->setLog($log);
        $this->assertSame($log, $this->event->getLog());
    }

    public function testResultGetterAndSetter(): void
    {
        $testResult = 'Test Result';

        // 测试设置和获取
        $this->event->setResult($testResult);
        $this->assertSame($testResult, $this->event->getResult());

        // 测试空字符串
        $this->event->setResult('');
        $this->assertSame('', $this->event->getResult());
    }

    public function testRequestGetterAndSetter(): void
    {
        $testRequest = [
            'method' => 'test.method',
            'params' => ['id' => 1]
        ];

        // 测试设置和获取
        $this->event->setRequest($testRequest);
        $this->assertSame($testRequest, $this->event->getRequest());

        // 测试空数组
        $this->event->setRequest([]);
        $this->assertSame([], $this->event->getRequest());
    }

    public function testEventImmutability(): void
    {
        // 检查事件是否是不可变的（即修改一个属性不会影响其他属性）
        /** @var RequestLog&\PHPUnit\Framework\MockObject\MockObject $log */
        $log = $this->createMock(RequestLog::class);
        $result = 'Test Result';
        $request = ['method' => 'test.method'];

        $this->event->setLog($log);
        $this->event->setResult($result);
        $this->event->setRequest($request);

        // 修改一个属性后，其他属性应该保持不变
        $newResult = 'New Result';
        $this->event->setResult($newResult);

        $this->assertSame($log, $this->event->getLog());
        $this->assertSame($newResult, $this->event->getResult());
        $this->assertSame($request, $this->event->getRequest());
    }
}
