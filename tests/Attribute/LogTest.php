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

    public function testAttributeWithAllCombinations(): void
    {
        // 测试所有可能的参数组合
        $combinations = [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];

        foreach ($combinations as [$request, $response]) {
            $log = new Log(request: $request, response: $response);
            $this->assertEquals($request, $log->request, "Request参数不匹配: {$request}");
            $this->assertEquals($response, $log->response, "Response参数不匹配: {$response}");
        }
    }

    public function testReadonlyProperties(): void
    {
        $log = new Log(request: true, response: false);
        
        // 验证属性是只读的
        $reflection = new \ReflectionClass($log);
        
        $requestProperty = $reflection->getProperty('request');
        $this->assertTrue($requestProperty->isReadOnly(), 'request属性应该是readonly');
        
        $responseProperty = $reflection->getProperty('response');
        $this->assertTrue($responseProperty->isReadOnly(), 'response属性应该是readonly');
    }

    public function testAttributeOnDifferentTargets(): void
    {
        // 测试属性只能应用于类（TARGET_CLASS）
        $reflection = new \ReflectionClass(Log::class);
        $attributes = $reflection->getAttributes(\Attribute::class);
        
        $this->assertCount(1, $attributes);
        $attribute = $attributes[0]->newInstance();
        
        // 验证只能应用于类
        $this->assertEquals(\Attribute::TARGET_CLASS, $attribute->flags);
    }

    public function testPropertyTypes(): void
    {
        $log = new Log();
        
        // 验证属性类型
        $this->assertIsBool($log->request, 'request属性必须是boolean类型');
        $this->assertIsBool($log->response, 'response属性必须是boolean类型');
    }

    public function testAttributeReflection(): void
    {
        // 测试通过反射获取属性详情
        $reflection = new \ReflectionClass(Log::class);
        
        // 验证类是否为属性类
        $this->assertTrue($reflection->hasMethod('__construct'), 'Log类应该有构造函数');
        
        // 检查构造函数参数
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(2, $parameters);
        
        // 验证参数名称和默认值
        $this->assertEquals('request', $parameters[0]->getName());
        $this->assertTrue($parameters[0]->getDefaultValue());
        
        $this->assertEquals('response', $parameters[1]->getName());
        $this->assertTrue($parameters[1]->getDefaultValue());
    }

    public function testNamedParameters(): void
    {
        // 测试命名参数的各种组合
        $log1 = new Log(request: true);
        $this->assertTrue($log1->request);
        $this->assertTrue($log1->response); // 默认值

        $log2 = new Log(response: false);
        $this->assertTrue($log2->request); // 默认值
        $this->assertFalse($log2->response);

        $log3 = new Log(response: false, request: true);
        $this->assertTrue($log3->request);
        $this->assertFalse($log3->response);
    }

    public function testAttributeInstantiation(): void
    {
        // 测试属性实例化过程
        $testClass = new #[Log] class {};
        
        $reflection = new \ReflectionClass($testClass);
        $attributes = $reflection->getAttributes(Log::class);
        
        $this->assertCount(1, $attributes);
        
        $attributeReflection = $attributes[0];
        $this->assertEquals(Log::class, $attributeReflection->getName());
        
        // 测试获取参数
        $arguments = $attributeReflection->getArguments();
        $this->assertEmpty($arguments); // 没有显式参数，使用默认值
        
        // 实例化并测试默认值
        $instance = $attributeReflection->newInstance();
        $this->assertTrue($instance->request);
        $this->assertTrue($instance->response);
    }

    public function testAttributeRepeatabilityRestriction(): void
    {
        // 测试Log属性的重复性限制
        // 由于Log属性没有设置IS_REPEATABLE标志，理论上不应该允许重复
        // 但实际行为可能因PHP版本而异，我们检查属性的flags
        
        $reflection = new \ReflectionClass(Log::class);
        $attributes = $reflection->getAttributes(\Attribute::class);
        
        if (!empty($attributes)) {
            $attributeInstance = $attributes[0]->newInstance();
            // 验证没有IS_REPEATABLE标志
            $this->assertEquals(\Attribute::TARGET_CLASS, $attributeInstance->flags);
            $this->assertNotEquals(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE, $attributeInstance->flags);
        }
        
        // 如果可以创建测试类，说明PHP允许了重复（某些版本的行为）
        // 我们只测试基本功能
        $this->assertTrue(true);
    }
}
