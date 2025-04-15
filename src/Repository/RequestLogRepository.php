<?php

namespace Tourze\JsonRPCLogBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\JsonRPCLogBundle\Entity\RequestLog;

/**
 * @method RequestLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestLog[]    findAll()
 * @method RequestLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestLog::class);
    }
}
