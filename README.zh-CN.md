# JsonRPC 日志组件

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/json-rpc-log-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/json-rpc-log-bundle)
[![License](https://img.shields.io/packagist/l/tourze/json-rpc-log-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/json-rpc-log-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/json-rpc-log-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/json-rpc-log-bundle)
[![Build Status](https://img.shields.io/github/workflow/status/tourze/php-monorepo/CI/master.svg?style=flat-square)]
(https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo/master.svg?style=flat-square)]
(https://codecov.io/gh/tourze/php-monorepo)

一个全面的 JsonRPC 服务器日志记录 Symfony 组件，支持异步数据库存储、性能监控，
并与 Tourze 生态系统无缝集成。

## 目录

- [功能特性](#功能特性)
- [系统要求](#系统要求)
- [安装说明](#安装说明)
- [快速开始](#快速开始)
- [配置日志保留](#配置日志保留)
- [访问管理界面](#访问管理界面)
- [配置选项](#配置选项)
- [环境变量](#环境变量)
- [高级用法](#高级用法)
- [数据库架构](#数据库架构)
- [测试](#测试)
- [安全性](#安全性)
- [贡献指南](#贡献指南)
- [许可证](#许可证)
- [更新日志](#更新日志)

## 功能特性

- **自动请求/响应日志**: 自动记录 JsonRPC 请求、响应和异常到数据库
- **异步数据库存储**: 使用 Doctrine 高性能异步插入
- **性能监控**: Stopwatch 计时和执行指标
- **事件驱动架构**: 通过事件订阅者实现可扩展的日志记录
- **管理界面**: 内置 EasyAdmin CRUD 日志管理
- **自动清理**: 可配置的日志保留策略
- **丰富上下文**: 捕获 IP、User-Agent、用户信息等
- **灵活配置**: 通过属性注解和环境变量控制日志记录

## 系统要求

- PHP >= 8.1
- Symfony >= 6.4
- Doctrine ORM

## 安装说明

### 通过 Composer 安装

```bash
composer require tourze/json-rpc-log-bundle
```

### 必需依赖

该组件与多个 Tourze 生态系统包集成：

- `tourze/doctrine-async-insert-bundle` - 异步数据库操作
- `tourze/doctrine-snowflake-bundle` - 雪花 ID 生成
- `tourze/json-rpc-core` - JsonRPC 核心功能

### 数据库设置

运行迁移以创建所需的数据库表：

```bash
php bin/console doctrine:migrations:migrate
```

## 快速开始

### 1. 在 JsonRPC 过程中启用日志记录

```php
<?php

use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\JsonRPCCore\Procedure\BaseProcedure;

#[Log(request: true, response: true)]
class CreateUserProcedure extends BaseProcedure
{
    public function process(): array
    {
        // 您的 JsonRPC 过程逻辑
        return ['status' => 'success', 'user_id' => 123];
    }
}
```

## 配置日志保留

在您的 `.env` 文件中设置环境变量：

```env
# 保留日志 30 天（默认：180）
JSON_RPC_LOG_PERSIST_DAY_NUM=30

# 限制日志中响应的大小（默认：1000）
JSON_RPC_LOG_RESULT_LENGTH=1000
```

## 访问管理界面

配置完成后，通过管理面板访问 JsonRPC 日志：
- 导航到"系统监控" → "JsonRPC 日志"
- 查看、过滤和导出日志条目
- 监控性能指标和错误

## 配置选项

### 日志属性注解

使用 `#[Log]` 属性控制记录内容：

```php
// 仅记录请求（隐私敏感的响应）
#[Log(request: true, response: false)]
class SensitiveDataProcedure extends BaseProcedure { }

// 仅记录响应（当请求数据不重要时）
#[Log(request: false, response: true)]
class ReadOnlyProcedure extends BaseProcedure { }

// 记录所有内容（默认）
#[Log(request: true, response: true)]
class StandardProcedure extends BaseProcedure { }
```

## 环境变量

| 变量 | 描述 | 默认值 |
|------|------|--------|
| `JSON_RPC_LOG_PERSIST_DAY_NUM` | 清理前保留日志的天数 | 180 |
| `JSON_RPC_LOG_RESULT_LENGTH` | 记录响应的最大长度 | 1000 |

## 高级用法

### 自定义日志格式

通过创建实现 `LogFormatProcedure` 接口的服务来实现自定义日志格式：

```php
<?php

use Tourze\JsonRPCLogBundle\Interface\LogFormatProcedure;

class CustomLogFormatter implements LogFormatProcedure
{
    public function format(array $data): array
    {
        // 自定义格式化逻辑
        return $data;
    }
}
```

### Monolog 集成

该组件包含一个 Monolog 处理器，将 JsonRPC 负载添加到日志上下文：

```php
// 在您的 services.yaml 中
services:
    Tourze\JsonRPCLogBundle\Monolog\PayloadLogProcessor:
        tags:
            - { name: monolog.processor }
```

### 性能监控

每个记录的请求包括：
- 执行时间（stopwatch）
- 内存使用情况
- 请求/响应大小
- 错误详情（如果有）

## 数据库架构

该组件创建具有以下关键字段的 `json_rpc_log` 表：

- `id` - 雪花 ID（主键）
- `request_id` - JsonRPC 请求 ID
- `method` - JsonRPC 方法名
- `request_payload` - 请求数据（JSON）
- `response_payload` - 响应数据（JSON）
- `exception_message` - 错误详情（如果有）
- `duration` - 执行时间（毫秒）
- `client_ip` - 客户端 IP 地址
- `server_ip` - 服务器 IP 地址
- `user_agent` - 客户端 User-Agent
- `user_id` - 关联用户（如果已认证）
- `created_at` - 时间戳

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/json-rpc-log-bundle/tests
```

该组件包含全面的测试，涵盖：
- 属性注解功能
- 事件订阅者
- 数据库实体
- 管理控制器
- 服务集成

## 安全性

此组件处理敏感的请求/响应数据，请考虑：

- 审查记录的数据类型并实施适当的数据清理
- 配置适当的日志保留策略以符合合规要求
- 通过适当的基于角色的权限保护管理界面访问
- 考虑对静态敏感日志数据进行加密

## 贡献指南

- 请通过 GitHub 提交 Issue
- 欢迎 PR，代码需遵循 PSR-12 规范
- 提交前确保所有测试通过
- 为新功能添加测试

## 许可证

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 更新日志

详见 [CHANGELOG](CHANGELOG.md) 获取版本历史与升级说明。
