<?php

namespace Tourze\JsonRPCLogBundle\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCLogBundle\Attribute\Log;

class LogTest extends TestCase
{
    public function testConstructWithDefaults(): void
    {
        $log = new Log();

        // 测试默认值
        $this->assertTrue($log->request);
        $this->assertTrue($log->response);
    }

    public function testConstructWithCustomValues(): void
    {
        $log = new Log(request: false, response: false);

        // 测试自定义值
        $this->assertFalse($log->request);
        $this->assertFalse($log->response);

        $log = new Log(request: true, response: false);
        $this->assertTrue($log->request);
        $this->assertFalse($log->response);

        $log = new Log(request: false, response: true);
        $this->assertFalse($log->request);
        $this->assertTrue($log->response);
    }

    public function testAttributeUsage(): void
    {
        // 定义一个使用 Log 属性的临时类 - 正确地将属性应用于类
        $testClass = new #[Log(request: false, response: true)] class {
            public function dummyMethod(): void
            {
            }
        };

        // 获取类反射
        $reflection = new \ReflectionClass($testClass);

        // 获取类的属性，而不是方法的属性
        $attributes = $reflection->getAttributes(Log::class);

        // 验证属性是否存在
        $this->assertCount(1, $attributes);

        // 实例化属性
        $logAttribute = $attributes[0]->newInstance();

        // 验证属性值
        $this->assertFalse($logAttribute->request);
        $this->assertTrue($logAttribute->response);
    }
}
