<?php

namespace Tourze\JsonRPCLogBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCLogBundle\Controller\Admin\RequestLogCrudController;
use Tourze\JsonRPCLogBundle\Entity\RequestLog;

class RequestLogCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(RequestLog::class, RequestLogCrudController::getEntityFqcn());
    }

    public function testConfigureCrud(): void
    {
        $controller = new RequestLogCrudController();
        $crud = $this->createMock(Crud::class);
        
        $crud->expects($this->once())
            ->method('setEntityLabelInSingular')
            ->with('JsonRPC日志')
            ->willReturnSelf();
            
        $crud->expects($this->once())
            ->method('setEntityLabelInPlural')
            ->with('JsonRPC日志')
            ->willReturnSelf();
            
        $crud->expects($this->atLeast(2))
            ->method('setPageTitle')
            ->willReturnSelf();
            
        $crud->expects($this->once())
            ->method('setHelp')
            ->with('index', '记录JsonRPC服务端的重要请求与响应信息，用于监控和故障排查')
            ->willReturnSelf();
            
        $crud->expects($this->once())
            ->method('setDefaultSort')
            ->with(['id' => 'DESC'])
            ->willReturnSelf();
            
        $crud->expects($this->once())
            ->method('showEntityActionsInlined')
            ->willReturnSelf();
            
        $crud->expects($this->once())
            ->method('setSearchFields')
            ->with(['id', 'apiName', 'description', 'createdFromIp'])
            ->willReturnSelf();
            
        $controller->configureCrud($crud);
    }

    public function testConfigureActions(): void
    {
        $controller = new RequestLogCrudController();
        
        // 使用反射验证方法签名
        $reflection = new \ReflectionMethod($controller, 'configureActions');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertSame('actions', $parameters[0]->getName());
        $paramType = $parameters[0]->getType();
        $this->assertSame(Actions::class, $paramType instanceof \ReflectionNamedType ? $paramType->getName() : '');
        
        $returnType = $reflection->getReturnType();
        $this->assertSame(Actions::class, $returnType instanceof \ReflectionNamedType ? $returnType->getName() : '');
    }

    public function testConfigureFields(): void
    {
        $controller = new RequestLogCrudController();
        $fields = $controller->configureFields(Crud::PAGE_INDEX);
        
        // 字段总是返回可迭代对象，所以不需要检查
        
        // 验证字段是否存在
        $fieldArray = iterator_to_array($fields);
        $fieldNames = array_map(function($field) {
            return $field->getAsDto()->getProperty();
        }, $fieldArray);
        
        $this->assertContains('id', $fieldNames);
        $this->assertContains('description', $fieldNames);
        $this->assertContains('apiName', $fieldNames);
        $this->assertContains('request', $fieldNames);
        $this->assertContains('response', $fieldNames);
        $this->assertContains('exception', $fieldNames);
        $this->assertContains('stopwatchDuration', $fieldNames);
        $this->assertContains('stopwatchResult', $fieldNames);
        $this->assertContains('createTime', $fieldNames);
    }
}