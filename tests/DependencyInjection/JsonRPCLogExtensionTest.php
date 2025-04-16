<?php

namespace Tourze\JsonRPCLogBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\JsonRPCLogBundle\DependencyInjection\JsonRPCLogExtension;

class JsonRPCLogExtensionTest extends TestCase
{
    public function testLoad(): void
    {
        $extension = new JsonRPCLogExtension();
        $container = new ContainerBuilder();

        // 调用 load 方法
        $extension->load([], $container);

        // 验证容器中是否有预期的服务定义
        $this->assertTrue($container->hasDefinition('Tourze\JsonRPCLogBundle\EventSubscriber\LogSubscriber') ||
            $container->hasAlias('Tourze\JsonRPCLogBundle\EventSubscriber\LogSubscriber'));

        $this->assertTrue($container->hasDefinition('Tourze\JsonRPCLogBundle\Logger\PayloadLogProcessor') ||
            $container->hasAlias('Tourze\JsonRPCLogBundle\Logger\PayloadLogProcessor'));

        $this->assertTrue($container->hasDefinition('Tourze\JsonRPCLogBundle\Repository\RequestLogRepository') ||
            $container->hasAlias('Tourze\JsonRPCLogBundle\Repository\RequestLogRepository'));
    }
}
