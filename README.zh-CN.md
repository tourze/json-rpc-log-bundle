# JsonRPC 日志组件

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/json-rpc-log-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-log-bundle)
[![License](https://img.shields.io/packagist/l/tourze/json-rpc-log-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-log-bundle)

一个用于记录 JsonRPC 服务端请求与响应日志的 Symfony 组件，支持异步数据库写入、请求/响应/异常日志、Stopwatch 计时，并与 Tourze 生态深度集成。

## 功能特性

- 将重要的 JsonRPC 请求与响应记录到数据库
- 支持 Doctrine 异步日志写入
- 支持 Stopwatch 执行时长与结果记录
- 捕获异常与错误信息
- 与 Tourze 生态（IP/User/UserAgent/Snowflake/Timestamp 等）无缝集成
- 支持日志导出与后台管理查看
- 通过事件自定义日志格式

## 安装说明

- PHP >= 8.1
- Symfony >= 6.4
- 使用 Composer 安装：

```bash
composer require tourze/json-rpc-log-bundle
```

- 需要配置数据库（Doctrine ORM）
- 请确保依赖的 Tourze 相关组件已安装（详见 composer.json）

## 快速开始

1. 在 `config/bundles.php` 注册本 Bundle（如未自动注册）。
2. 执行数据库迁移，创建 `json_rpc_log` 表。
3. 正常使用 JsonRPC 服务端，重要请求会自动记录日志。
4. 可通过后台管理或数据库查询查看日志。

## 使用示例

```php
// 使用 tourze/json-rpc-core 服务端时日志会自动写入
// 可通过在 JsonRPC 方法类上添加 #[Log] 注解自定义日志行为
```

## 配置说明

- 通过环境变量 `JSON_RPC_LOG_PERSIST_DAY_NUM` 控制日志保留天数
- 通过 #[Log] 注解控制是否记录请求/响应/结果
- 监听 `JsonRpcLogFormatEvent` 事件自定义日志格式

## 高级特性

- 支持高性能异步日志写入
- 集成 Tourze EasyAdmin 后台界面
- 提供完整的请求/响应/异常/上下文信息

## 贡献指南

- 请通过 GitHub 提交 Issue
- 欢迎 PR，代码需遵循 PSR-12 规范
- 使用 PHPUnit 进行测试

## 协议

MIT License，版权所有 (c) tourze

## 更新日志

详见 [CHANGELOG](CHANGELOG.md) 获取版本历史与升级说明。
