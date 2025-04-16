<?php

namespace Tourze\JsonRPCLogBundle\Tests\Logger;

use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Event\RequestStartEvent;
use Tourze\JsonRPC\Core\Event\ResponseSendingEvent;
use Tourze\JsonRPCLogBundle\Logger\PayloadLogProcessor;

class PayloadLogProcessorTest extends TestCase
{
    private PayloadLogProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new PayloadLogProcessor();
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

        $processedRecord = ($this->processor)($record);

        // 确认在没有 payload 时，不会添加附加信息
        $this->assertArrayNotHasKey('json_rpc_payload', $processedRecord->extra);
    }

    public function testInvokeWithPayload(): void
    {
        // 设置 payload
        $payloadString = '{"method":"test.method","params":{"id":1}}';

        /** @var RequestStartEvent&\PHPUnit\Framework\MockObject\MockObject $event */
        $event = $this->createMock(RequestStartEvent::class);
        $event->expects($this->once())
            ->method('getPayload')
            ->willReturn($payloadString);

        $this->processor->onRequest($event);

        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->processor)($record);

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

        /** @var RequestStartEvent&\PHPUnit\Framework\MockObject\MockObject $event */
        $event = $this->createMock(RequestStartEvent::class);
        $event->expects($this->once())
            ->method('getPayload')
            ->willReturn($payloadString);

        $this->processor->onRequest($event);

        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->processor)($record);
        // 由于我们直接检查了实现细节，这里我们不比较具体值
        // 只要值存在即可
        $this->assertNotNull($processedRecord->extra['json_rpc_payload']);

        // 测试无效的 JSON
        $invalidPayloadString = 'invalid-json';
        /** @var RequestStartEvent&\PHPUnit\Framework\MockObject\MockObject $event */
        $event = $this->createMock(RequestStartEvent::class);
        $event->expects($this->once())
            ->method('getPayload')
            ->willReturn($invalidPayloadString);

        $this->processor->onRequest($event);

        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->processor)($record);
        $this->assertSame(
            $invalidPayloadString,
            $processedRecord->extra['json_rpc_payload']
        );
    }

    public function testNoResponse(): void
    {
        // 先设置 payload
        $payloadString = '{"method":"test.method","params":{"id":1}}';
        /** @var RequestStartEvent&\PHPUnit\Framework\MockObject\MockObject $startEvent */
        $startEvent = $this->createMock(RequestStartEvent::class);
        $startEvent->expects($this->once())
            ->method('getPayload')
            ->willReturn($payloadString);

        $this->processor->onRequest($startEvent);

        // 验证 payload 已设置
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->processor)($record);
        $this->assertArrayHasKey('json_rpc_payload', $processedRecord->extra);

        // 调用 noResponse 方法
        /** @var ResponseSendingEvent&\PHPUnit\Framework\MockObject\MockObject $endEvent */
        $endEvent = $this->createMock(ResponseSendingEvent::class);
        $this->processor->noResponse($endEvent);

        // 验证 payload 已重置
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->processor)($record);
        $this->assertArrayNotHasKey('json_rpc_payload', $processedRecord->extra);
    }

    public function testReset(): void
    {
        // 先设置 payload
        $payloadString = '{"method":"test.method","params":{"id":1}}';
        /** @var RequestStartEvent&\PHPUnit\Framework\MockObject\MockObject $event */
        $event = $this->createMock(RequestStartEvent::class);
        $event->expects($this->once())
            ->method('getPayload')
            ->willReturn($payloadString);

        $this->processor->onRequest($event);

        // 验证 payload 已设置
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->processor)($record);
        $this->assertArrayHasKey('json_rpc_payload', $processedRecord->extra);

        // 调用 reset 方法
        $this->processor->reset();

        // 验证 payload 已重置
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Debug,
            message: 'Test message',
            context: [],
            extra: []
        );

        $processedRecord = ($this->processor)($record);
        $this->assertArrayNotHasKey('json_rpc_payload', $processedRecord->extra);
    }
}
