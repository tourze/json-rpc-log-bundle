# JsonRPC Log Bundle

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

A Symfony bundle for comprehensive JsonRPC server logging with async database storage, 
performance monitoring, and seamless integration with the Tourze ecosystem.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configure Log Retention](#configure-log-retention)
- [Access Admin Interface](#access-admin-interface)
- [Configuration Options](#configuration-options)
- [Environment Variables](#environment-variables)
- [Advanced Usage](#advanced-usage)
- [Database Schema](#database-schema)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)
- [Changelog](#changelog)

## Features

- **Automatic Request/Response Logging**: Log JsonRPC requests, responses, and exceptions to database
- **Async Database Storage**: High-performance async insertion using Doctrine
- **Performance Monitoring**: Stopwatch timing and execution metrics
- **Event-Driven Architecture**: Extensible logging through event subscribers
- **Admin Interface**: Built-in EasyAdmin CRUD for log management
- **Automatic Cleanup**: Configurable log retention policies
- **Rich Context**: Captures IP, User-Agent, User info, and more
- **Flexible Configuration**: Control logging via attributes and environment variables

## Requirements

- PHP >= 8.1
- Symfony >= 6.4
- Doctrine ORM

## Installation

### Install via Composer

```bash
composer require tourze/json-rpc-log-bundle
```

### Required Dependencies

The bundle integrates with several Tourze ecosystem packages:

- `tourze/doctrine-async-insert-bundle` - Async database operations
- `tourze/doctrine-snowflake-bundle` - Snowflake ID generation
- `tourze/json-rpc-core` - Core JsonRPC functionality

### Database Setup

Run migrations to create the required database tables:

```bash
php bin/console doctrine:migrations:migrate
```

## Quick Start

### 1. Enable Logging on JsonRPC Procedures

```php
<?php

use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\JsonRPCCore\Procedure\BaseProcedure;

#[Log(request: true, response: true)]
class CreateUserProcedure extends BaseProcedure
{
    public function process(): array
    {
        // Your JsonRPC procedure logic
        return ['status' => 'success', 'user_id' => 123];
    }
}
```

## Configure Log Retention

Set environment variables in your `.env` file:

```env
# Keep logs for 30 days (default: 180)
JSON_RPC_LOG_PERSIST_DAY_NUM=30

# Limit response size in logs (default: 1000)
JSON_RPC_LOG_RESULT_LENGTH=1000
```

## Access Admin Interface

Once configured, access the JsonRPC logs through your admin panel:
- Navigate to "System Monitoring" → "JsonRPC Logs"
- View, filter, and export log entries
- Monitor performance metrics and errors

## Configuration Options

### Log Attributes

Control what gets logged using the `#[Log]` attribute:

```php
// Log only requests (privacy-sensitive responses)
#[Log(request: true, response: false)]
class SensitiveDataProcedure extends BaseProcedure { }

// Log only responses (when request data is not important)
#[Log(request: false, response: true)]
class ReadOnlyProcedure extends BaseProcedure { }

// Log everything (default)
#[Log(request: true, response: true)]
class StandardProcedure extends BaseProcedure { }
```

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `JSON_RPC_LOG_PERSIST_DAY_NUM` | Days to keep logs before cleanup | 180 |
| `JSON_RPC_LOG_RESULT_LENGTH` | Maximum length of logged response | 1000 |

## Advanced Usage

### Custom Log Formatting

Implement custom log formatting by creating a service that implements `LogFormatProcedure`:

```php
<?php

use Tourze\JsonRPCLogBundle\Interface\LogFormatProcedure;

class CustomLogFormatter implements LogFormatProcedure
{
    public function format(array $data): array
    {
        // Custom formatting logic
        return $data;
    }
}
```

### Monolog Integration

The bundle includes a Monolog processor that adds JsonRPC payload to log context:

```php
// In your services.yaml
services:
    Tourze\JsonRPCLogBundle\Monolog\PayloadLogProcessor:
        tags:
            - { name: monolog.processor }
```

### Performance Monitoring

Each logged request includes:
- Execution time (stopwatch)
- Memory usage
- Request/response size
- Error details (if any)

## Database Schema

The bundle creates a `json_rpc_log` table with the following key fields:

- `id` - Snowflake ID (primary key)
- `request_id` - JsonRPC request ID
- `method` - JsonRPC method name
- `request_payload` - Request data (JSON)
- `response_payload` - Response data (JSON)
- `exception_message` - Error details (if any)
- `duration` - Execution time in milliseconds
- `client_ip` - Client IP address
- `server_ip` - Server IP address
- `user_agent` - Client User-Agent
- `user_id` - Associated user (if authenticated)
- `created_at` - Timestamp

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/json-rpc-log-bundle/tests
```

The bundle includes comprehensive tests covering:
- Attribute functionality
- Event subscribers
- Database entities
- Admin controllers
- Service integration

## Security

This bundle handles sensitive request/response data. Consider:

- Review logged data types and implement appropriate data sanitization
- Configure proper log retention policies for compliance
- Secure admin interface access with appropriate role-based permissions
- Consider encrypting sensitive log data at rest

## Contributing

- Submit issues via GitHub
- Pull requests welcome, follow PSR-12 code style
- Ensure all tests pass before submitting
- Add tests for new features

## License

MIT License. Copyright (c) tourze

## Changelog

See [CHANGELOG](CHANGELOG.md) for version history and upgrade notes.
