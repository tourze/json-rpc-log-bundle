<?php

namespace Tourze\JsonRPCLogBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\JsonRPCLogBundle\Entity\RequestLog;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Yiisoft\Json\Json;

/**
 * @internal
 */
#[CoversClass(RequestLog::class)]
final class RequestLogTest extends AbstractEntityTestCase
{
    public function testCustomGetterAndSetter(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Entity\RequestLog 的实例化，原因：
        // 理由 1：RequestLog 是一个简单的实体类，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于数据库实体映射，需要测试其 getter/setter 方法
        // 理由 3：直接实例化可以验证实体属性访问的正确性
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
        $createTime = new \DateTimeImmutable();
        $requestLog->setCreateTime($createTime);
        $this->assertSame($createTime, $requestLog->getCreateTime());

        // 测试 CreatedBy 相关方法
        $createdBy = 'Test User';
        $requestLog->setCreatedBy($createdBy);
        $this->assertSame($createdBy, $requestLog->getCreatedBy());
    }

    public function testNullableFields(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Entity\RequestLog 的实例化，原因：
        // 理由 1：RequestLog 是一个简单的实体类，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于数据库实体映射，需要测试其可空字段的默认值
        // 理由 3：直接实例化可以验证实体初始化状态
        $requestLog = new RequestLog();

        // 测试所有可空字段的默认值
        $this->assertNull($requestLog->getRequest());
        $this->assertNull($requestLog->getResponse());
        $this->assertNull($requestLog->getException());
        $this->assertNull($requestLog->getServerIp());
        $this->assertNull($requestLog->getStopwatchResult());
        $this->assertNull($requestLog->getStopwatchDuration());
        $this->assertNull($requestLog->getApiName());
        $this->assertNull($requestLog->getCreatedFromIp());
        $this->assertNull($requestLog->getCreatedFromUa());
        $this->assertNull($requestLog->getCreateTime());
        $this->assertNull($requestLog->getCreatedBy());
    }

    public function testSetNullValues(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Entity\RequestLog 的实例化，原因：
        // 理由 1：RequestLog 是一个简单的实体类，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于数据库实体映射，需要测试其 null 值处理能力
        // 理由 3：直接实例化可以验证实体属性边界值处理
        $requestLog = new RequestLog();

        // 测试设置null值
        $requestLog->setResponse(null);
        $this->assertNull($requestLog->getResponse());

        $requestLog->setException(null);
        $this->assertNull($requestLog->getException());

        $requestLog->setServerIp(null);
        $this->assertNull($requestLog->getServerIp());

        $requestLog->setStopwatchResult(null);
        $this->assertNull($requestLog->getStopwatchResult());

        $requestLog->setStopwatchDuration(null);
        $this->assertNull($requestLog->getStopwatchDuration());

        $requestLog->setApiName(null);
        $this->assertNull($requestLog->getApiName());

        $requestLog->setCreatedFromUa(null);
        $this->assertNull($requestLog->getCreatedFromUa());

        $requestLog->setCreateTime(null);
        $this->assertNull($requestLog->getCreateTime());

        $requestLog->setCreatedBy(null);
        $this->assertNull($requestLog->getCreatedBy());
    }

    public function testEmptyStringValues(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Entity\RequestLog 的实例化，原因：
        // 理由 1：RequestLog 是一个简单的实体类，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于数据库实体映射，需要测试其空字符串值处理能力
        // 理由 3：直接实例化可以验证实体属性边界值处理
        $requestLog = new RequestLog();

        // 测试空字符串值
        $requestLog->setRequest('');
        $this->assertSame('', $requestLog->getRequest());

        $requestLog->setResponse('');
        $this->assertSame('', $requestLog->getResponse());

        $requestLog->setException('');
        $this->assertSame('', $requestLog->getException());

        $requestLog->setServerIp('');
        $this->assertSame('', $requestLog->getServerIp());

        $requestLog->setStopwatchResult('');
        $this->assertSame('', $requestLog->getStopwatchResult());

        $requestLog->setStopwatchDuration('');
        $this->assertSame('', $requestLog->getStopwatchDuration());

        $requestLog->setApiName('');
        $this->assertSame('', $requestLog->getApiName());

        $requestLog->setCreatedFromIp('');
        $this->assertSame('', $requestLog->getCreatedFromIp());

        $requestLog->setCreatedFromUa('');
        $this->assertSame('', $requestLog->getCreatedFromUa());

        $requestLog->setCreatedBy('');
        $this->assertSame('', $requestLog->getCreatedBy());
    }

    public function testComplexJsonData(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Entity\RequestLog 的实例化，原因：
        // 理由 1：RequestLog 是一个简单的实体类，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于数据库实体映射，需要测试其复杂 JSON 数据处理能力
        // 理由 3：直接实例化可以验证实体属性数据处理能力
        $requestLog = new RequestLog();

        // 测试复杂JSON数据
        $complexRequest = [
            'jsonrpc' => '2.0',
            'method' => 'user.create',
            'params' => [
                'user' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'metadata' => [
                        'tags' => ['admin', 'power-user'],
                        'preferences' => ['theme' => 'dark'],
                    ],
                ],
            ],
            'id' => 'req-123',
        ];

        $requestJson = Json::encode($complexRequest);
        $requestLog->setRequest($requestJson);
        $this->assertSame($requestJson, $requestLog->getRequest());

        // 验证能够正确解码
        $decoded = Json::decode($requestLog->getRequest());
        $this->assertSame($complexRequest, $decoded);
    }

    public function testDateTimeHandling(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Entity\RequestLog 的实例化，原因：
        // 理由 1：RequestLog 是一个简单的实体类，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于数据库实体映射，需要测试其 DateTime 处理能力
        // 理由 3：直接实例化可以验证实体属性时间处理能力
        $requestLog = new RequestLog();

        // 测试不同的DateTime对象
        $dateTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $requestLog->setCreateTime($dateTime);
        $this->assertSame($dateTime, $requestLog->getCreateTime());

        // 测试DateTimeImmutable
        $immutableDateTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $requestLog->setCreateTime($immutableDateTime);
        $this->assertSame($immutableDateTime, $requestLog->getCreateTime());
    }

    public function testIpAddressFormats(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Entity\RequestLog 的实例化，原因：
        // 理由 1：RequestLog 是一个简单的实体类，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于数据库实体映射，需要测试其 IP 地址格式处理能力
        // 理由 3：直接实例化可以验证实体属性 IP 地址处理能力
        $requestLog = new RequestLog();

        // 测试IPv4地址
        $ipv4 = '192.168.1.100';
        $requestLog->setCreatedFromIp($ipv4);
        $this->assertSame($ipv4, $requestLog->getCreatedFromIp());

        $requestLog->setServerIp($ipv4);
        $this->assertSame($ipv4, $requestLog->getServerIp());

        // 测试IPv6地址
        $ipv6 = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';
        $requestLog->setCreatedFromIp($ipv6);
        $this->assertSame($ipv6, $requestLog->getCreatedFromIp());

        // 测试localhost
        $localhost = '127.0.0.1';
        $requestLog->setServerIp($localhost);
        $this->assertSame($localhost, $requestLog->getServerIp());
    }

    public function testStopwatchDurationFormats(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Entity\RequestLog 的实例化，原因：
        // 理由 1：RequestLog 是一个简单的实体类，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于数据库实体映射，需要测试其持续时间格式处理能力
        // 理由 3：直接实例化可以验证实体属性数值处理能力
        $requestLog = new RequestLog();

        // 测试不同的持续时间格式
        $durations = ['0.001', '1.234', '999.999', '0', '1000.00'];

        foreach ($durations as $duration) {
            $requestLog->setStopwatchDuration($duration);
            $this->assertSame($duration, $requestLog->getStopwatchDuration());
        }
    }

    public function testUserAgentStrings(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Entity\RequestLog 的实例化，原因：
        // 理由 1：RequestLog 是一个简单的实体类，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于数据库实体映射，需要测试其 User Agent 字符串处理能力
        // 理由 3：直接实例化可以验证实体属性字符串处理能力
        $requestLog = new RequestLog();

        // 测试常见的User Agent字符串
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'curl/7.68.0',
            'PostmanRuntime/7.28.4',
            'custom-api-client/1.0',
            '',
        ];

        foreach ($userAgents as $ua) {
            $requestLog->setCreatedFromUa($ua);
            $this->assertSame($ua, $requestLog->getCreatedFromUa());
        }
    }

    public function testFluentInterface(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Entity\RequestLog 的实例化，原因：
        // 理由 1：RequestLog 是一个简单的实体类，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于数据库实体映射，需要测试其setter方法的正确性
        // 理由 3：直接实例化可以验证实体方法设计的正确性
        $requestLog = new RequestLog();

        // 测试setter方法（现在返回void，不支持链式调用）
        $requestLog->setRequest('test request');
        $requestLog->setResponse('test response');
        $requestLog->setApiName('test.api');
        $requestLog->setServerIp('127.0.0.1');

        // 验证设置的值是否正确
        $this->assertSame('test request', $requestLog->getRequest());
        $this->assertSame('test response', $requestLog->getResponse());
        $this->assertSame('test.api', $requestLog->getApiName());
        $this->assertSame('127.0.0.1', $requestLog->getServerIp());
    }

    public function testLongTextContent(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Entity\RequestLog 的实例化，原因：
        // 理由 1：RequestLog 是一个简单的实体类，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于数据库实体映射，需要测试其长文本内容处理能力
        // 理由 3：直接实例化可以验证实体属性长文本处理能力
        $requestLog = new RequestLog();

        // 测试长文本内容
        $longText = str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 100);

        $requestLog->setRequest($longText);
        $this->assertSame($longText, $requestLog->getRequest());

        $requestLog->setResponse($longText);
        $this->assertSame($longText, $requestLog->getResponse());

        $requestLog->setException($longText);
        $this->assertSame($longText, $requestLog->getException());

        $requestLog->setCreatedFromUa($longText);
        $this->assertSame($longText, $requestLog->getCreatedFromUa());
    }

    public function testSpecialCharacters(): void
    {
        // 使用具体类 Tourze\JsonRPCLogBundle\Entity\RequestLog 的实例化，原因：
        // 理由 1：RequestLog 是一个简单的实体类，不需要依赖注入，直接实例化是合理的测试方式
        // 理由 2：该类用于数据库实体映射，需要测试其特殊字符处理能力
        // 理由 3：直接实例化可以验证实体属性特殊字符处理能力
        $requestLog = new RequestLog();

        // 测试特殊字符
        $specialChars = '!@#$%^&*()_+-=[]{}|;:,.<>?`~"\'\\';
        $requestLog->setApiName($specialChars);
        $this->assertSame($specialChars, $requestLog->getApiName());

        // 测试Unicode字符
        $unicode = '测试中文内容 🚀 emoji 日本語 한국어';
        $requestLog->setRequest($unicode);
        $this->assertSame($unicode, $requestLog->getRequest());
    }

    protected function createEntity(): RequestLog
    {
        return new RequestLog();
    }

    /**
     * @return array<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            'request' => ['request', 'test request data'],
            'response' => ['response', 'test response data'],
            'exception' => ['exception', 'test exception'],
            'serverIp' => ['serverIp', '127.0.0.1'],
            'stopwatchResult' => ['stopwatchResult', 'test result'],
            'stopwatchDuration' => ['stopwatchDuration', '1.23'],
            'apiName' => ['apiName', 'test.api'],
            'createdFromIp' => ['createdFromIp', '192.168.1.1'],
            'createdFromUa' => ['createdFromUa', 'test ua'],
            'createTime' => ['createTime', new \DateTimeImmutable()],
            'createdBy' => ['createdBy', 'test user'],
        ];
    }
}
