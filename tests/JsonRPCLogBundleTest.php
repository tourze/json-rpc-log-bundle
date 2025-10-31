<?php

declare(strict_types=1);

namespace Tourze\JsonRPCLogBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPCLogBundle\JsonRPCLogBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(JsonRPCLogBundle::class)]
#[RunTestsInSeparateProcesses]
final class JsonRPCLogBundleTest extends AbstractBundleTestCase
{
}
