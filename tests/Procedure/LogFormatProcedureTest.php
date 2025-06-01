<?php

namespace Tourze\JsonRPCLogBundle\Tests\Procedure;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCLogBundle\Procedure\LogFormatProcedure;

class LogFormatProcedureTest extends TestCase
{
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
        $this->assertEquals(JsonRpcRequest::class, $parameter->getType()?->getName());
        
        $returnType = $method->getReturnType();
        $this->assertEquals('string', $returnType?->getName());
    }

    public function testMockImplementationBasic(): void
    {
        // 创建模拟实现
        $mockImplementation = new class implements LogFormatProcedure {
            public function generateFormattedLogText(JsonRpcRequest $request): string
            {
                return "Mock log for method: {$request->getMethod()}";
            }
        };

        // 创建模拟JsonRpcRequest
        /** @var JsonRpcRequest&MockObject $request */
        $request = $this->createMock(JsonRpcRequest::class);
        $request->method('getMethod')->willReturn('test.method');

        // 测试实现
        $result = $mockImplementation->generateFormattedLogText($request);
        $this->assertEquals('Mock log for method: test.method', $result);
    }

    public function testImplementationWithComplexLogic(): void
    {
        // 创建更复杂的模拟实现
        $mockImplementation = new class implements LogFormatProcedure {
            public function generateFormattedLogText(JsonRpcRequest $request): string
            {
                $method = $request->getMethod();
                $id = $request->getId();
                
                // 处理参数（如果可用）
                $paramsInfo = '[]'; // 默认空数组
                try {
                    if (method_exists($request, 'getParams')) {
                        $params = $request->getParams();
                        if (method_exists($params, 'toArray')) {
                            $paramsArray = $params->toArray();
                            $paramsInfo = json_encode($paramsArray);
                        }
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

        // 测试空参数
        /** @var JsonRpcRequest&MockObject $emptyRequest */
        $emptyRequest = $this->createMock(JsonRpcRequest::class);
        $emptyRequest->method('getMethod')->willReturn('empty.method');
        $emptyRequest->method('getId')->willReturn('empty-id');
        
        $result = $mockImplementation->generateFormattedLogText($emptyRequest);
        $this->assertEquals('[empty-id] empty.method with params: []', $result);
    }

    public function testImplementationWithEdgeCases(): void
    {
        // 创建边界情况处理的实现
        $mockImplementation = new class implements LogFormatProcedure {
            public function generateFormattedLogText(JsonRpcRequest $request): string
            {
                try {
                    $method = $request->getMethod() ?: 'unknown';
                    $id = $request->getId() ?: 'no-id';
                    
                    return "Method: {$method}, ID: {$id}";
                } catch (\Throwable $e) {
                    return "Error processing request: " . $e->getMessage();
                }
            }
        };

        // 测试正常情况
        /** @var JsonRpcRequest&MockObject $request */
        $request = $this->createMock(JsonRpcRequest::class);
        $request->method('getMethod')->willReturn('test.method');
        $request->method('getId')->willReturn('test-id');
        
        $result = $mockImplementation->generateFormattedLogText($request);
        $this->assertEquals('Method: test.method, ID: test-id', $result);

        // 测试空字符串情况（而不是null）
        /** @var JsonRpcRequest&MockObject $emptyRequest */
        $emptyRequest = $this->createMock(JsonRpcRequest::class);
        $emptyRequest->method('getMethod')->willReturn('');
        $emptyRequest->method('getId')->willReturn('');
        
        $result = $mockImplementation->generateFormattedLogText($emptyRequest);
        $this->assertEquals('Method: unknown, ID: no-id', $result);
    }

    public function testMultipleImplementations(): void
    {
        // 测试不同的实现策略
        $simpleImplementation = new class implements LogFormatProcedure {
            public function generateFormattedLogText(JsonRpcRequest $request): string
            {
                return $request->getMethod();
            }
        };

        $detailedImplementation = new class implements LogFormatProcedure {
            public function generateFormattedLogText(JsonRpcRequest $request): string
            {
                return sprintf(
                    'RPC Call: %s (ID: %s)',
                    $request->getMethod(),
                    $request->getId()
                );
            }
        };

        /** @var JsonRpcRequest&MockObject $request */
        $request = $this->createMock(JsonRpcRequest::class);
        $request->method('getMethod')->willReturn('example.method');
        $request->method('getId')->willReturn('example-id');

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
        // 测试异常处理能力
        $robustImplementation = new class implements LogFormatProcedure {
            public function generateFormattedLogText(JsonRpcRequest $request): string
            {
                try {
                    return $request->getMethod() . ' - processed';
                } catch (\Throwable $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }
        };

        // 模拟抛出异常的请求
        /** @var JsonRpcRequest&MockObject $errorRequest */
        $errorRequest = $this->createMock(JsonRpcRequest::class);
        $errorRequest->method('getMethod')->willThrowException(new \RuntimeException('Method access failed'));
        
        $result = $robustImplementation->generateFormattedLogText($errorRequest);
        $this->assertEquals('Error: Method access failed', $result);
    }
} 