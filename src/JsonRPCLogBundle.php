<?php

namespace Tourze\JsonRPCLogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class JsonRPCLogBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class => ['all' => true],
            \Tourze\DoctrineIpBundle\DoctrineIpBundle::class => ['all' => true],
            \Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle::class => ['all' => true],
            \Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle::class => ['all' => true],
            \Tourze\DoctrineUserAgentBundle\DoctrineUserAgentBundle::class => ['all' => true],
            \Tourze\DoctrineUserBundle\DoctrineUserBundle::class => ['all' => true],
            \Tourze\ScheduleEntityCleanBundle\ScheduleEntityCleanBundle::class => ['all' => true],
        ];
    }
}
