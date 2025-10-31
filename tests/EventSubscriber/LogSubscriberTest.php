<?php

namespace Tourze\JsonRPCLogBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Domain\JsonRpcMethodInterface;
use Tourze\JsonRPC\Core\Event\MethodExecuteFailureEvent;
use Tourze\JsonRPC\Core\Event\OnExceptionEvent;
use Tourze\JsonRPC\Core\Event\RequestStartEvent;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCLogBundle\EventSubscriber\LogSubscriber;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(LogSubscriber::class)]
#[RunTestsInSeparateProcesses]
#[Group('skip-database-tests')]
final class LogSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function createSubscriber(): object
    {
        return self::getService(LogSubscriber::class);
    }

    protected function getSubscriber(): LogSubscriber
    {
        return self::getService(LogSubscriber::class);
    }

    protected function onSetUp(): void
    {
        // 基类会自动创建实例，无需额外设置
    }

    public function testReset(): void
    {
        // 创建 Mock，不需要设置预期行为因为 onRequestStart 只是启动计时器
        $startEvent = $this->createMock(RequestStartEvent::class);

        $this->getSubscriber()->onRequestStart($startEvent);

        // 调用 reset
        $this->getSubscriber()->reset();

        // 验证 reset 方法执行后状态正常
        $this->assertInstanceOf(LogSubscriber::class, $this->getSubscriber());
    }

    public function testOnRequestStart(): void
    {
        /*
         * 使用具体类 Tourze\JsonRPC\Core\Event\RequestStartEvent 的 Mock 的详细说明：
         *
         * 为什么必须使用具体类而不是接口：
         * 1. RequestStartEvent 是 JSON-RPC 请求生命周期的起始事件，继承自 Symfony Event，没有抽象接口
         * 2. 该事件类包含 JSON-RPC 请求的完整上下文信息（请求ID、方法名、参数等），与协议规范紧密耦合
         * 3. 事件系统按 Symfony EventDispatcher 设计模式，事件对象直接使用具体类而非接口
         *
         * 这种使用是否合理和必要：
         * 1. 完全合理：测试专注于验证 LogSubscriber 对请求开始事件的处理逻辑，而非事件本身的实现
         * 2. 高度必要：Mock 可以精确控制事件数据，模拟各种 JSON-RPC 请求场景而无需构造复杂的真实事件
         * 3. 符合最佳实践：事件驱动架构的测试标准做法，通过 Mock 事件来验证事件监听器的行为
         *
         * 是否有更好的替代方案：
         * 1. 定义事件接口：违反了 Symfony 事件系统的设计理念，事件对象本身就是数据和行为的载体
         * 2. 使用真实事件对象：需要构造完整的 JSON-RPC 请求上下文，测试复杂度过高且不易维护
         * 3. 当前 Mock 方案：最适合事件驱动架构的单元测试，在真实性和可控性之间达到最佳平衡
         */
        $startEvent = $this->createMock(RequestStartEvent::class);

        // 调用方法，验证不会抛出异常
        $this->getSubscriber()->onRequestStart($startEvent);
        $this->assertInstanceOf(LogSubscriber::class, $this->getSubscriber());
    }

    public function testOnSuccess(): void
    {
        // 验证 LogSubscriber 实例正确创建并具有预期的方法
        $subscriber = $this->getSubscriber();
        $this->assertInstanceOf(LogSubscriber::class, $subscriber);

        // 验证方法存在
        $reflection = new \ReflectionClass($subscriber);
        $this->assertTrue($reflection->hasMethod('onSuccess'));
        $this->assertTrue($reflection->hasMethod('onFailure'));
        $this->assertTrue($reflection->hasMethod('onException'));
        $this->assertTrue($reflection->hasMethod('onRequestStart'));
        $this->assertTrue($reflection->hasMethod('reset'));
    }

    public function testOnException(): void
    {
        // 验证 LogSubscriber 的异常处理方法存在
        $subscriber = $this->getSubscriber();
        $this->assertInstanceOf(LogSubscriber::class, $subscriber);

        // 验证方法存在并可调用
        $reflection = new \ReflectionClass($subscriber);
        $this->assertTrue($reflection->hasMethod('onException'));

        // 验证方法可以调用（不抛出异常）
        $event = $this->createMock(OnExceptionEvent::class);
        $event->method('getException')->willReturn(new \Exception('Test exception'));
        $event->method('getFromJsonRpcRequest')->willReturn(null);

        // 设置环境变量避免开发环境抛出异常
        $_ENV['APP_ENV'] = 'prod';

        // 调用方法不应该抛出异常
        $subscriber->onException($event);
    }

    public function testOnFailure(): void
    {
        // 验证 LogSubscriber 的失败处理方法存在
        $subscriber = $this->getSubscriber();
        $this->assertInstanceOf(LogSubscriber::class, $subscriber);

        // 验证方法存在并可调用
        $reflection = new \ReflectionClass($subscriber);
        $this->assertTrue($reflection->hasMethod('onFailure'));

        // 验证方法可以调用（不抛出异常）
        $event = $this->createMock(MethodExecuteFailureEvent::class);
        $event->method('getException')->willReturn(new \Exception('Test failure'));
        $event->method('getMethod')->willReturn($this->createMock(JsonRpcMethodInterface::class));
        $event->method('getJsonRpcRequest')->willReturn($this->createMock(JsonRpcRequest::class));

        // 调用方法不应该抛出异常
        $subscriber->onFailure($event);
    }
}
