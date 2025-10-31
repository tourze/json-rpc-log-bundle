<?php

namespace Tourze\JsonRPCLogBundle\Attribute;

/**
 * 如果方法做了这个标记，则会将请求日志存入数据库
 *
 * 建议只在写操作、耗时统计这类接口上加这个注解
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS)]
class Log
{
    public function __construct(
        public readonly bool $request = true,
        public readonly bool $response = true,
    ) {
    }
}
