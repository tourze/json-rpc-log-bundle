<?php

namespace Tourze\JsonRPCLogBundle\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCLogBundle\JsonRPCLogBundle;

class JsonRPCLogBundleTest extends TestCase
{
    public function testBundleInstantiation(): void
    {
        $bundle = new JsonRPCLogBundle();
        $this->assertInstanceOf(JsonRPCLogBundle::class, $bundle);
    }
}
