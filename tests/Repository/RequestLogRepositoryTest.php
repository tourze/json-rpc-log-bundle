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
}
