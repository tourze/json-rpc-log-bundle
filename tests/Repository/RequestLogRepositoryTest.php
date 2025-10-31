<?php

namespace Tourze\JsonRPCLogBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPCLogBundle\Entity\RequestLog;
use Tourze\JsonRPCLogBundle\Repository\RequestLogRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(RequestLogRepository::class)]
#[RunTestsInSeparateProcesses]
#[Group('skip-database-tests')]
final class RequestLogRepositoryTest extends AbstractRepositoryTestCase
{
    protected static function getEntityClass(): string
    {
        return RequestLog::class;
    }

    protected function createNewEntity(): object
    {
        return new RequestLog();
    }

    protected function getRepository(): RequestLogRepository
    {
        return self::getService(RequestLogRepository::class);
    }

    protected function onSetUp(): void
    {
        // 空实现，因为不需要数据库设置
    }

    public function testRepositoryInstanceCreation(): void
    {
        // 测试Repository实例化行为
        $repository = $this->getRepository();
        $this->assertInstanceOf(RequestLogRepository::class, $repository);
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }

    public function testSaveEntityBehavior(): void
    {
        // 测试save方法的行为
        $repository = $this->getRepository();
        $entity = $this->createTestEntity();

        // 验证save方法可以调用而不抛出异常
        $this->expectNotToPerformAssertions();
        $repository->save($entity, false); // 不flush以避免数据库操作
    }

    public function testSaveEntityWithFlushBehavior(): void
    {
        // 测试save方法带flush的行为
        $repository = $this->getRepository();
        $entity = $this->createTestEntity();

        // 验证save方法带flush参数可以调用
        $this->expectNotToPerformAssertions();
        $repository->save($entity, true);
    }

    public function testRemoveEntityBehavior(): void
    {
        // 测试remove方法的行为
        $repository = $this->getRepository();
        $entity = $this->createTestEntity();

        // 先保存实体
        $repository->save($entity, false);

        // 验证remove方法可以调用而不抛出异常
        $this->expectNotToPerformAssertions();
        $repository->remove($entity, false); // 不flush以避免数据库操作
    }

    public function testRemoveEntityWithFlushBehavior(): void
    {
        // 测试remove方法带flush的行为
        $repository = $this->getRepository();
        $entity = $this->createTestEntity();

        // 先保存实体
        $repository->save($entity, false);

        // 验证remove方法带flush参数可以调用
        $this->expectNotToPerformAssertions();
        $repository->remove($entity, true);
    }

    public function testInheritedMethodsAvailable(): void
    {
        // 测试从ServiceEntityRepository继承的基本方法是否可用
        $repository = $this->getRepository();

        // 测试这些方法可以调用（即使在没有数据的情况下）
        $result = $repository->findAll();
        $this->assertIsArray($result);

        $count = $repository->count([]);
        $this->assertIsInt($count);

        $findByResult = $repository->findBy([]);
        $this->assertIsArray($findByResult);
    }

    public function testEntityRelationship(): void
    {
        // 测试Repository与Entity的关系
        $repository = $this->getRepository();
        $entity = $this->createTestEntity();

        // 验证Repository能处理正确的实体类型
        $this->assertInstanceOf(RequestLog::class, $entity);

        // 测试实体的基本方法
        $entity->setApiName('test.api');
        $this->assertSame('test.api', $entity->getApiName());

        $entity->setRequest('{"method":"test"}');
        $this->assertSame('{"method":"test"}', $entity->getRequest());
    }

    /**
     * 创建测试用的RequestLog实体
     */
    private function createTestEntity(): RequestLog
    {
        $entity = new RequestLog();
        $entity->setApiName('test.method');
        $entity->setRequest('{"jsonrpc":"2.0","method":"test","id":1}');
        $entity->setResponse('{"jsonrpc":"2.0","result":"success","id":1}');
        $entity->setDescription('Test API call');
        $entity->setServerIp('127.0.0.1');
        $entity->setStopwatchDuration('0.05');

        return $entity;
    }
}
