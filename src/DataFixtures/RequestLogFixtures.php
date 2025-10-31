<?php

namespace Tourze\JsonRPCLogBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\JsonRPCLogBundle\Entity\RequestLog;

/**
 * RequestLog 数据夹具
 *
 * 此夹具创建多个 JSON-RPC 请求日志示例，包括：
 * - 成功请求：完整的请求和响应记录
 * - 异常请求：包含异常信息的请求
 * - 慢查询请求：执行时间较长的请求
 * - 用户相关请求：包含用户信息的请求
 * - 服务器内部请求：服务器间的 API 调用
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class RequestLogFixtures extends Fixture implements FixtureGroupInterface
{
    // 定义引用常量
    public const SUCCESS_REQUEST_REFERENCE = 'success-request-log';
    public const EXCEPTION_REQUEST_REFERENCE = 'exception-request-log';
    public const SLOW_REQUEST_REFERENCE = 'slow-request-log';
    public const USER_REQUEST_REFERENCE = 'user-request-log';
    public const SERVER_REQUEST_REFERENCE = 'server-request-log';

    /**
     * 返回此fixture所属的组名
     */
    public static function getGroups(): array
    {
        return ['json-rpc-log', 'api', 'test'];
    }

    public function load(ObjectManager $manager): void
    {
        // 成功请求日志
        $successRequest = new RequestLog();
        $successRequest->setDescription('用户登录接口调用');
        $successRequest->setRequest('{"jsonrpc":"2.0","method":"user.login","params":{"username":"test","password":"***"},"id":1}');
        $successRequest->setResponse('{"jsonrpc":"2.0","result":{"token":"abc123","expires":3600},"id":1}');
        $successRequest->setServerIp('127.0.0.1');
        $successRequest->setStopwatchResult('default: 125.34ms');
        $successRequest->setStopwatchDuration('0.12');
        $successRequest->setApiName('user.login');
        $successRequest->setCreatedFromUa('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $manager->persist($successRequest);
        $this->addReference(self::SUCCESS_REQUEST_REFERENCE, $successRequest);

        // 异常请求日志
        $exceptionRequest = new RequestLog();
        $exceptionRequest->setDescription('用户注册接口调用失败');
        $exceptionRequest->setRequest('{"jsonrpc":"2.0","method":"user.register","params":{"email":"invalid-email"},"id":2}');
        $exceptionRequest->setException('ValidationException: 邮箱格式不正确');
        $exceptionRequest->setServerIp('127.0.0.1');
        $exceptionRequest->setStopwatchResult('default: 45.23ms');
        $exceptionRequest->setStopwatchDuration('0.05');
        $exceptionRequest->setApiName('user.register');
        $exceptionRequest->setCreatedFromUa('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15');
        $manager->persist($exceptionRequest);
        $this->addReference(self::EXCEPTION_REQUEST_REFERENCE, $exceptionRequest);

        // 慢查询请求日志
        $slowRequest = new RequestLog();
        $slowRequest->setDescription('数据导出接口调用');
        $slowRequest->setRequest('{"jsonrpc":"2.0","method":"data.export","params":{"type":"full","format":"csv"},"id":3}');
        $slowRequest->setResponse('{"jsonrpc":"2.0","result":{"download_url":"/api/export/download/12345"},"id":3}');
        $slowRequest->setServerIp('192.168.1.100');
        $slowRequest->setStopwatchResult('default: 15234.67ms');
        $slowRequest->setStopwatchDuration('15.23');
        $slowRequest->setApiName('data.export');
        $slowRequest->setCreatedFromUa('curl/7.68.0');
        $manager->persist($slowRequest);
        $this->addReference(self::SLOW_REQUEST_REFERENCE, $slowRequest);

        // 用户相关请求日志
        $userRequest = new RequestLog();
        $userRequest->setDescription('获取用户信息');
        $userRequest->setRequest('{"jsonrpc":"2.0","method":"user.info","params":{"user_id":12345},"id":4}');
        $userRequest->setResponse('{"jsonrpc":"2.0","result":{"id":12345,"name":"测试用户","email":"test@test.local"},"id":4}');
        $userRequest->setServerIp('10.0.0.1');
        $userRequest->setStopwatchResult('default: 89.12ms');
        $userRequest->setStopwatchDuration('0.09');
        $userRequest->setApiName('user.info');
        $userRequest->setCreatedFromUa('PostmanRuntime/7.29.0');
        $manager->persist($userRequest);
        $this->addReference(self::USER_REQUEST_REFERENCE, $userRequest);

        // 服务器内部请求日志
        $serverRequest = new RequestLog();
        $serverRequest->setDescription('服务器内部API调用');
        $serverRequest->setRequest('{"jsonrpc":"2.0","method":"internal.sync","params":{"data":"batch_update"},"id":5}');
        $serverRequest->setResponse('{"jsonrpc":"2.0","result":{"processed":100,"errors":0},"id":5}');
        $serverRequest->setServerIp('172.16.0.10');
        $serverRequest->setStopwatchResult('default: 2345.89ms');
        $serverRequest->setStopwatchDuration('2.35');
        $serverRequest->setApiName('internal.sync');
        $serverRequest->setCreatedFromUa('InternalService/1.0');
        $manager->persist($serverRequest);
        $this->addReference(self::SERVER_REQUEST_REFERENCE, $serverRequest);

        $manager->flush();
    }
}
