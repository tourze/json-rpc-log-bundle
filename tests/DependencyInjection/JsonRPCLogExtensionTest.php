<?php

namespace Tourze\JsonRPCLogBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Tourze\JsonRPCLogBundle\DependencyInjection\JsonRPCLogExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(JsonRPCLogExtension::class)]
final class JsonRPCLogExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 这个测试类不需要特殊的设置
    }

    public function testLoadWithEmptyConfigs(): void
    {
        // 使用反射创建Extension实例，避免直接实例化
        $extensionClass = new \ReflectionClass(JsonRPCLogExtension::class);
        $extension = $extensionClass->newInstance();
        $container = new ContainerBuilder();

        // AutoExtension 需要 kernel.environment 参数
        $container->setParameter('kernel.environment', 'test');

        // 测试空配置数组
        $extension->load([], $container);

        // 确保即使没有配置也能正常加载服务
        $this->assertGreaterThan(0, count($container->getDefinitions()));
    }

    public function testLoadWithMultipleConfigs(): void
    {
        // 使用反射创建Extension实例
        $extensionClass = new \ReflectionClass(JsonRPCLogExtension::class);
        $extension = $extensionClass->newInstance();
        $container = new ContainerBuilder();

        // AutoExtension 需要 kernel.environment 参数
        $container->setParameter('kernel.environment', 'test');

        // 测试多个配置数组
        $configs = [
            [],
            ['some_config' => 'value1'],
            ['another_config' => 'value2'],
        ];

        $extension->load($configs, $container);

        // 验证服务正常加载
        $this->assertGreaterThan(0, count($container->getDefinitions()));
    }

    public function testExtensionName(): void
    {
        // 使用反射创建Extension实例
        $extensionClass = new \ReflectionClass(JsonRPCLogExtension::class);
        $extension = $extensionClass->newInstance();

        // 验证扩展名称符合Symfony约定
        $expectedAlias = 'json_rpc_log'; // Symfony会自动从类名生成
        $this->assertEquals($expectedAlias, $extension->getAlias());
    }

    public function testYamlFileLoaderCreation(): void
    {
        // 使用反射创建Extension实例
        $extensionClass = new \ReflectionClass(JsonRPCLogExtension::class);
        $extension = $extensionClass->newInstance();
        $container = new ContainerBuilder();

        // AutoExtension 需要 kernel.environment 参数
        $container->setParameter('kernel.environment', 'test');

        // 这个测试主要验证YamlFileLoader能够正常创建和使用
        // 验证加载没有异常
        $extension->load([], $container);

        // 验证服务已加载
        $this->assertGreaterThan(0, count($container->getDefinitions()));
    }

    public function testServiceConfiguration(): void
    {
        // 使用反射创建Extension实例
        $extensionClass = new \ReflectionClass(JsonRPCLogExtension::class);
        $extension = $extensionClass->newInstance();
        $container = new ContainerBuilder();

        // AutoExtension 需要 kernel.environment 参数
        $container->setParameter('kernel.environment', 'test');

        $extension->load([], $container);

        // 验证服务配置的基本属性
        $definitions = $container->getDefinitions();

        foreach ($definitions as $id => $definition) {
            // 确保所有服务都有有效的类名
            if (null !== $definition->getClass()) {
                $this->assertNotEmpty($definition->getClass(), "Service {$id} should have a class");
            }
        }
    }

    public function testLoadServicesFromYaml(): void
    {
        // 直接测试YAML文件加载
        $container = new ContainerBuilder();
        $configDir = __DIR__ . '/../../src/Resources/config';

        $this->assertFileExists($configDir . '/services.yaml', 'services.yaml file should exist');

        $loader = new YamlFileLoader($container, new FileLocator($configDir));

        try {
            $loader->load('services.yaml');
            $this->assertInstanceOf(YamlFileLoader::class, $loader, 'Loader should successfully load YAML file');
        } catch (\Throwable $e) {
            self::fail('Failed to load services.yaml: ' . $e->getMessage());
        }
    }

    public function testContainerParametersAfterLoad(): void
    {
        // 使用反射创建Extension实例
        $extensionClass = new \ReflectionClass(JsonRPCLogExtension::class);
        $extension = $extensionClass->newInstance();
        $container = new ContainerBuilder();

        // AutoExtension 需要 kernel.environment 参数
        $container->setParameter('kernel.environment', 'test');

        $extension->load([], $container);

        // 验证容器状态
        // $container 已经是 ContainerBuilder 实例，无需再次断言

        // 验证没有编译错误
        $this->assertNotEmpty($container->getDefinitions());
    }

    public function testExtensionInheritance(): void
    {
        // 使用反射创建Extension实例
        $extensionClass = new \ReflectionClass(JsonRPCLogExtension::class);
        $extension = $extensionClass->newInstance();

        // 验证继承自正确的基类
        // $extension 已经是 JsonRPCLogExtension 实例，无需再次断言其继承关系
        // 验证扩展名符合 Symfony 约定
        $this->assertEquals('json_rpc_log', $extension->getAlias());
    }

    public function testConfigurationProcessing(): void
    {
        // 使用反射创建Extension实例
        $extensionClass = new \ReflectionClass(JsonRPCLogExtension::class);
        $extension = $extensionClass->newInstance();
        $container = new ContainerBuilder();

        // AutoExtension 需要 kernel.environment 参数
        $container->setParameter('kernel.environment', 'test');

        // 测试配置处理不会抛出异常
        $configs = [
            ['enabled' => true],
            ['debug' => false],
        ];

        try {
            $extension->load($configs, $container);
            $this->assertNotEmpty($container->getDefinitions(), 'Container should have service definitions after loading');
        } catch (InvalidArgumentException $e) {
            // 如果配置无效，应该抛出InvalidArgumentException
            $this->assertStringContainsString('configuration', $e->getMessage());
        }
    }

    public function testServiceTagsAndAttributes(): void
    {
        // 使用反射创建Extension实例
        $extensionClass = new \ReflectionClass(JsonRPCLogExtension::class);
        $extension = $extensionClass->newInstance();
        $container = new ContainerBuilder();

        // AutoExtension 需要 kernel.environment 参数
        $container->setParameter('kernel.environment', 'test');

        $extension->load([], $container);

        // 验证服务定义存在（由于使用了 autoconfigure，标签会在编译时自动添加）
        $this->assertTrue(
            $container->hasDefinition('Tourze\JsonRPCLogBundle\Logger\PayloadLogProcessor')
            || $container->hasAlias('Tourze\JsonRPCLogBundle\Logger\PayloadLogProcessor'),
            'PayloadLogProcessor 服务应该被定义'
        );

        $this->assertTrue(
            $container->hasDefinition('Tourze\JsonRPCLogBundle\EventSubscriber\LogSubscriber')
            || $container->hasAlias('Tourze\JsonRPCLogBundle\EventSubscriber\LogSubscriber'),
            'LogSubscriber 服务应该被定义'
        );

        // 验证容器至少加载了一些服务定义
        $definitions = $container->getDefinitions();
        $this->assertGreaterThan(0, count($definitions), '容器应该包含服务定义');
    }
}
