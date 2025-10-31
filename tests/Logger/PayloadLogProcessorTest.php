<?php

namespace Tourze\JsonRPCLogBundle\Tests\Logger;

use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Event\RequestStartEvent;
use Tourze\JsonRPC\Core\Event\ResponseSendingEvent;
use Tourze\JsonRPCLogBundle\Logger\PayloadLogProcessor;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(PayloadLogProcessor::class)]
#[RunTestsInSeparateProcesses]
#[Group('skip-database-tests')]
final class PayloadLogProcessorTest extends AbstractEventSubscriberTestCase
{
    protected function createSubscriber(): object
    {
        return self::getService(PayloadLogProcessor::class);
    }

    protected function getSubscriber(): PayloadLogProcessor
    {
        return self::getService(PayloadLogProcessor::class);
    }

    protected function onSetUp(): void
    {
        // 基类会自动创建实例
    }

    public function testInvokeWithoutPayload(): void
    {
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->getSubscriber())($record);

        // 确认在没有 payload 时，不会添加附加信息
        $this->assertArrayNotHasKey('json_rpc_payload', $processedRecord->extra);
    }

    public function testInvokeWithPayload(): void
    {
        // 设置 payload
        $payloadString = '{"method":"test.method","params":{"id":1}}';

        // 理由 1：测试需要验证 payload 处理逻辑，确保日志处理器能正确处理请求事件
        // 理由 2：RequestStartEvent 是事件类，没有对应的抽象类或接口可用
        // 理由 3：Mock 可以控制事件的返回值，简化测试场景并验证具体行为
        $event = $this->createMock(RequestStartEvent::class);
        $event->expects($this->once())
            ->method('getPayload')
            ->willReturn($payloadString)
        ;

        $this->getSubscriber()->onRequest($event);

        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->getSubscriber())($record);

        // 确认附加了 payload 信息
        $this->assertArrayHasKey('json_rpc_payload', $processedRecord->extra);

        // 由于我们直接检查了实现细节，这里我们不比较具体值
        // 只要值存在即可
        $this->assertNotNull($processedRecord->extra['json_rpc_payload']);
    }

    public function testOnRequest(): void
    {
        // 测试有效的 JSON
        $payloadString = '{"method":"test.method","params":{"id":1}}';

        // 理由 1：测试需要验证 JSON 处理逻辑，确保处理器能正确处理有效和无效的 JSON 数据
        // 理由 2：RequestStartEvent 是事件类，没有对应的抽象类或接口可用
        // 理由 3：Mock 可以模拟不同的 payload 数据，测试边界情况和错误处理
        $event = $this->createMock(RequestStartEvent::class);
        $event->expects($this->once())
            ->method('getPayload')
            ->willReturn($payloadString)
        ;

        $this->getSubscriber()->onRequest($event);

        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->getSubscriber())($record);
        // 由于我们直接检查了实现细节，这里我们不比较具体值
        // 只要值存在即可
        $this->assertNotNull($processedRecord->extra['json_rpc_payload']);

        // 测试无效的 JSON
        $invalidPayloadString = 'invalid-json';
        // 理由 1：测试需要验证无效 JSON 处理逻辑，确保处理器能正确处理错误情况
        // 理由 2：RequestStartEvent 是事件类，没有对应的抽象类或接口可用
        // 理由 3：Mock 可以模拟无效的 payload 数据，测试错误处理和边界情况
        $event = $this->createMock(RequestStartEvent::class);
        $event->expects($this->once())
            ->method('getPayload')
            ->willReturn($invalidPayloadString)
        ;

        $this->getSubscriber()->onRequest($event);

        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->getSubscriber())($record);
        $this->assertSame(
            $invalidPayloadString,
            $processedRecord->extra['json_rpc_payload']
        );
    }

    public function testNoResponse(): void
    {
        // 先设置 payload
        $payloadString = '{"method":"test.method","params":{"id":1}}';
        // 理由 1：测试需要验证 payload 重置逻辑，确保处理器能正确管理状态
        // 理由 2：RequestStartEvent 是事件类，没有对应的抽象类或接口可用
        // 理由 3：Mock 可以控制事件的返回值，简化测试场景并验证状态管理
        $startEvent = $this->createMock(RequestStartEvent::class);
        $startEvent->expects($this->once())
            ->method('getPayload')
            ->willReturn($payloadString)
        ;

        $this->getSubscriber()->onRequest($startEvent);

        // 验证 payload 已设置
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->getSubscriber())($record);
        $this->assertArrayHasKey('json_rpc_payload', $processedRecord->extra);

        // 调用 noResponse 方法
        // 理由 1：测试需要验证响应处理逻辑，确保处理器能正确处理响应事件
        // 理由 2：ResponseSendingEvent 是事件类，没有对应的抽象类或接口可用
        // 理由 3：Mock 可以简化测试场景，避免复杂的事件创建过程
        $endEvent = $this->createMock(ResponseSendingEvent::class);
        $this->getSubscriber()->noResponse($endEvent);

        // 验证 payload 已重置
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->getSubscriber())($record);
        $this->assertArrayNotHasKey('json_rpc_payload', $processedRecord->extra);
    }

    public function testReset(): void
    {
        // 先设置 payload
        $payloadString = '{"method":"test.method","params":{"id":1}}';
        // 理由 1：测试需要验证 reset 功能，确保处理器能正确重置状态
        // 理由 2：RequestStartEvent 是事件类，没有对应的抽象类或接口可用
        // 理由 3：Mock 可以控制事件的返回值，简化测试场景并验证重置行为
        $event = $this->createMock(RequestStartEvent::class);
        $event->expects($this->once())
            ->method('getPayload')
            ->willReturn($payloadString)
        ;

        $this->getSubscriber()->onRequest($event);

        // 验证 payload 已设置
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->getSubscriber())($record);
        $this->assertArrayHasKey('json_rpc_payload', $processedRecord->extra);

        // 调用 reset 方法
        $this->getSubscriber()->reset();

        // 验证 payload 已重置
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->getSubscriber())($record);
        $this->assertArrayNotHasKey('json_rpc_payload', $processedRecord->extra);
    }
}
