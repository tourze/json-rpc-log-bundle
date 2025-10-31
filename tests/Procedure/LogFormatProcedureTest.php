<?php

namespace Tourze\JsonRPCLogBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCLogBundle\Procedure\LogFormatProcedure;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(LogFormatProcedure::class)]
#[RunTestsInSeparateProcesses]
final class LogFormatProcedureTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 接口测试不需要额外设置
    }

    public function testInterfaceExists(): void
    {
        // 验证接口存在且可以被引用
        $this->assertTrue(interface_exists(LogFormatProcedure::class));
    }

    public function testInterfaceHasCorrectMethod(): void
    {
        // 验证接口有正确的方法签名
        $reflection = new \ReflectionClass(LogFormatProcedure::class);
        $this->assertTrue($reflection->hasMethod('generateFormattedLogText'));

        $method = $reflection->getMethod('generateFormattedLogText');
        $this->assertTrue($method->isPublic());
        $this->assertCount(1, $method->getParameters());

        $parameter = $method->getParameters()[0];
        $this->assertEquals('request', $parameter->getName());
        $paramType = $parameter->getType();
        $this->assertNotNull($paramType);
        $this->assertEquals(JsonRpcRequest::class, $paramType instanceof \ReflectionNamedType ? $paramType->getName() : '');

        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('string', $returnType instanceof \ReflectionNamedType ? $returnType->getName() : '');
    }

    public function testMockImplementationBasic(): void
    {
        // 创建模拟实现
        $mockImplementation = new class () implements LogFormatProcedure {
            public function generateFormattedLogText(JsonRpcRequest $request): string
            {
                return "Mock log for method: {$request->getMethod()}";
            }
        };

        // 创建真实的JsonRpcRequest对象，比Mock更简单可靠
        $request = new JsonRpcRequest();
        $request->setMethod('test.method');

        // 测试实现
        $result = $mockImplementation->generateFormattedLogText($request);
        $this->assertEquals('Mock log for method: test.method', $result);
    }

    public function testImplementationWithComplexLogic(): void
    {
        // 创建更复杂的模拟实现
        $mockImplementation = new class () implements LogFormatProcedure {
            public function generateFormattedLogText(JsonRpcRequest $request): string
            {
                $method = $request->getMethod();
                $id = $request->getId();

                // 处理参数（如果可用）
                $paramsInfo = '[]'; // 默认空数组
                try {
                    $params = $request->getParams();

                    if (null !== $params) {
                        $paramsArray = $params->toArray();
                        $paramsInfo = json_encode($paramsArray);
                    }
                } catch (\Throwable $e) {
                    $paramsInfo = 'error';
                }

                return sprintf(
                    '[%s] %s with params: %s',
                    $id,
                    $method,
                    $paramsInfo
                );
            }
        };

        // 创建真实的JsonRpcRequest对象测试空参数场景
        $emptyRequest = new JsonRpcRequest();
        $emptyRequest->setMethod('empty.method');
        $emptyRequest->setId('empty-id');
        // params默认为null，无需设置

        $result = $mockImplementation->generateFormattedLogText($emptyRequest);
        $this->assertEquals('[empty-id] empty.method with params: []', $result);
    }

    public function testImplementationWithEdgeCases(): void
    {
        // 创建边界情况处理的实现
        $mockImplementation = new class () implements LogFormatProcedure {
            public function generateFormattedLogText(JsonRpcRequest $request): string
            {
                try {
                    $method = $request->getMethod();
                    if ('' === $method) {
                        $method = 'unknown';
                    }

                    $id = $request->getId();
                    if (null === $id || '' === $id) {
                        $id = 'no-id';
                    }

                    return "Method: {$method}, ID: {$id}";
                } catch (\Throwable $e) {
                    return 'Error processing request: ' . $e->getMessage();
                }
            }
        };

        // 创建真实的JsonRpcRequest对象测试正常边界值处理
        $request = new JsonRpcRequest();
        $request->setMethod('test.method');
        $request->setId('test-id');

        $result = $mockImplementation->generateFormattedLogText($request);
        $this->assertEquals('Method: test.method, ID: test-id', $result);

        // 创建真实的JsonRpcRequest对象测试空字符串边界情况
        $emptyRequest = new JsonRpcRequest();
        $emptyRequest->setMethod('');
        $emptyRequest->setId('');

        $result = $mockImplementation->generateFormattedLogText($emptyRequest);
        $this->assertEquals('Method: unknown, ID: no-id', $result);
    }

    public function testMultipleImplementations(): void
    {
        // 测试不同的实现策略
        $simpleImplementation = new class () implements LogFormatProcedure {
            public function generateFormattedLogText(JsonRpcRequest $request): string
            {
                return $request->getMethod();
            }
        };

        $detailedImplementation = new class () implements LogFormatProcedure {
            public function generateFormattedLogText(JsonRpcRequest $request): string
            {
                return sprintf(
                    'RPC Call: %s (ID: %s)',
                    $request->getMethod(),
                    $request->getId()
                );
            }
        };

        // 创建真实的JsonRpcRequest对象测试多个实现策略
        $request = new JsonRpcRequest();
        $request->setMethod('example.method');
        $request->setId('example-id');

        // 测试简单实现
        $this->assertEquals('example.method', $simpleImplementation->generateFormattedLogText($request));

        // 测试详细实现
        $this->assertEquals(
            'RPC Call: example.method (ID: example-id)',
            $detailedImplementation->generateFormattedLogText($request)
        );
    }

    public function testImplementationExceptionHandling(): void
    {
        // 测试异常处理能力，使用更简单的方式
        $robustImplementation = new class () implements LogFormatProcedure {
            public function generateFormattedLogText(JsonRpcRequest $request): string
            {
                $method = $request->getMethod();

                // 简单的错误处理逻辑，不依赖异常
                if ('' === $method || 'invalid' === $method) {
                    return 'Error: Invalid method';
                }

                return $method . ' - processed';
            }
        };

        // 创建一个用于测试错误处理的Request对象
        $errorRequest = new JsonRpcRequest();
        $errorRequest->setMethod('invalid');

        $result = $robustImplementation->generateFormattedLogText($errorRequest);
        $this->assertEquals('Error: Invalid method', $result);

        // 测试正常情况
        $normalRequest = new JsonRpcRequest();
        $normalRequest->setMethod('valid.method');

        $normalResult = $robustImplementation->generateFormattedLogText($normalRequest);
        $this->assertEquals('valid.method - processed', $normalResult);
    }
}
