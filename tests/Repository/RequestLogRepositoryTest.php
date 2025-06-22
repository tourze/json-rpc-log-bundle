<?php

namespace Tourze\JsonRPCLogBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCLogBundle\Repository\RequestLogRepository;

class RequestLogRepositoryTest extends TestCase
{
    public function testConstructor(): void
    {
        // 模拟 ManagerRegistry
        $registry = $this->createMock(ManagerRegistry::class);

        // 实例化 Repository
        $repository = new RequestLogRepository($registry);

        // 由于构造函数只是调用父类的构造函数，因此我们只能确保没有抛出异常
        $this->assertInstanceOf(RequestLogRepository::class, $repository);
    }

    public function testInheritance(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new RequestLogRepository($registry);

        // 验证继承关系
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $repository);
    }

    public function testRepositoryMethods(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new RequestLogRepository($registry);

        // 验证Repository实例化成功
        $this->assertInstanceOf(RequestLogRepository::class, $repository);
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $repository);
    }

    public function testEntityClass(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new RequestLogRepository($registry);

        // 通过反射检查Repository管理的实体类
        $reflection = new \ReflectionClass($repository);
        $parentClass = $reflection->getParentClass();
        
        // 验证是ServiceEntityRepository的子类
        $this->assertEquals('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $parentClass->getName());
    }
}
