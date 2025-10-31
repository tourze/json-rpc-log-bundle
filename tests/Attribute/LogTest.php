<?php

namespace Tourze\JsonRPCLogBundle\Tests\Attribute;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCLogBundle\Attribute\Log;

/**
 * @internal
 */
#[CoversClass(Log::class)]
final class LogTest extends TestCase
{
    public function testConstructWithDefaults(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Attribute\Log 的实例化，原因：
        // 理由 1：Log 是一个简单的值对象，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于 PHP 8+ 属性系统，需要测试其实例化行为
        // 理由 3：直接实例化可以验证构造函数参数和默认值
        $log = new Log();

        // 测试默认值
        $this->assertTrue($log->request);
        $this->assertTrue($log->response);
    }

    public function testConstructWithCustomValues(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Attribute\Log 的实例化，原因：
        // 理由 1：Log 是一个简单的值对象，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于 PHP 8+ 属性系统，需要测试不同参数组合的实例化行为
        // 理由 3：直接实例化可以验证构造函数参数处理逻辑
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
        // 使用具体类 Tourze\JsonRPCLogBundle\Attribute\Log 的实例化，原因：
        // 理由 1：Log 是一个简单的值对象，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于 PHP 8+ 属性系统，需要测试其作为属性的使用方式
        // 理由 3：直接实例化可以验证属性实例化和反射功能
        $log = new Log(request: false, response: true);

        // 验证属性值
        $this->assertFalse($log->request);
        $this->assertTrue($log->response);

        // 测试反射获取属性
        $reflection = new \ReflectionClass(Log::class);
        $attributes = $reflection->getAttributes(\Attribute::class);
        $this->assertCount(1, $attributes);
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
            // 使用具体类 Tourze\JsonRPCLogBundle\Attribute\Log 的实例化，原因：
            // 理由 1：Log 是一个简单的值对象，不需要依赖注入，直接实例化是合理的测试方式
            // 理由 2：该类用于 PHP 8+ 属性系统，需要测试不同参数组合的实例化行为
            // 理由 3：直接实例化可以验证构造函数参数处理逻辑的正确性
            $log = new Log(request: $request, response: $response);
            $this->assertEquals($request, $log->request, "Request参数不匹配: {$request}");
            $this->assertEquals($response, $log->response, "Response参数不匹配: {$response}");
        }
    }

    public function testReadonlyProperties(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Attribute\Log 的实例化，原因：
        // 理由 1：Log 是一个简单的值对象，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于 PHP 8+ 属性系统，需要测试其只读属性特性
        // 理由 3：直接实例化可以验证 readonly 属性的正确性
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
        // 使用具体类 Tourze\JsonRPCLogBundle\Attribute\Log 的实例化，原因：
        // 理由 1：Log 是一个简单的值对象，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于 PHP 8+ 属性系统，需要测试其属性类型和默认值
        // 理由 3：直接实例化可以验证属性类型的正确性
        $log = new Log();

        // 验证默认值
        $this->assertTrue($log->request, 'request属性默认值应该是true');
        $this->assertTrue($log->response, 'response属性默认值应该是true');

        // 测试自定义值
        $customLog = new Log(request: false, response: false);
        $this->assertFalse($customLog->request, 'request属性应该可以设置为false');
        $this->assertFalse($customLog->response, 'response属性应该可以设置为false');
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
        // 使用具体类 Tourze\JsonRPCLogBundle\Attribute\Log 的实例化，原因：
        // 理由 1：Log 是一个简单的值对象，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于 PHP 8+ 属性系统，需要测试命名参数的使用方式
        // 理由 3：直接实例化可以验证命名参数处理的正确性
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
        // 使用具体类 Tourze\JsonRPCLogBundle\Attribute\Log 的实例化，原因：
        // 理由 1：Log 是一个简单的值对象，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于 PHP 8+ 属性系统，需要测试其实例化过程
        // 理由 3：直接实例化可以验证属性实例化和参数处理的正确性
        $log = new Log();

        // 验证默认值
        $this->assertTrue($log->request);
        $this->assertTrue($log->response);

        // 测试带参数的实例化
        $logWithParams = new Log(request: false, response: true);
        $this->assertFalse($logWithParams->request);
        $this->assertTrue($logWithParams->response);
    }

    public function testAttributeRepeatabilityRestriction(): void
    {
        // 测试Log属性的重复性限制
        // 由于Log属性没有设置IS_REPEATABLE标志，理论上不应该允许重复
        // 但实际行为可能因PHP版本而异，我们检查属性的flags

        $reflection = new \ReflectionClass(Log::class);
        $attributes = $reflection->getAttributes(\Attribute::class);

        if (count($attributes) > 0) {
            $attributeInstance = $attributes[0]->newInstance();
            // 验证没有IS_REPEATABLE标志
            $this->assertEquals(\Attribute::TARGET_CLASS, $attributeInstance->flags);
            $this->assertNotEquals(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE, $attributeInstance->flags);

            // 验证Log属性确实没有重复标志，这是正确的行为
            $this->assertEquals(0, \Attribute::IS_REPEATABLE & $attributeInstance->flags, 'Log attribute should not be repeatable');
        } else {
            self::fail('Log class should have Attribute annotation');
        }
    }
}
