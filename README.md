# JsonRPC Log Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/json-rpc-log-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-log-bundle)
[![License](https://img.shields.io/packagist/l/tourze/json-rpc-log-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-log-bundle)

A Symfony bundle for logging JsonRPC server requests and responses, supporting async database storage, request/response/exception logging, stopwatch timing, and rich integration with the Tourze ecosystem.

## Features

- Logs important JsonRPC requests and responses to database
- Async log writing with Doctrine
- Stopwatch duration and result tracking
- Exception and error capturing
- Integrates with Tourze bundles (IP/User/UserAgent/Snowflake/Timestamp, etc.)
- Supports log export and admin viewing
- Customizable log format via event

## Installation

- PHP >= 8.1
- Symfony >= 6.4
- Install via Composer:

```bash
composer require tourze/json-rpc-log-bundle
```

- Requires database setup (Doctrine ORM)
- Ensure required Tourze bundles are installed (see composer.json)

## Quick Start

1. Register the bundle in your `config/bundles.php` if not auto-registered.
2. Run migrations to create the `json_rpc_log` table.
3. Use JsonRPC server as usual; important requests will be logged automatically.
4. View logs via admin or query the database.

## Example Usage

```php
// Logs are written automatically when using tourze/json-rpc-core server
// Customize logging by adding #[Log] attribute to your JsonRPC method class
```

## Configuration

- Control log retention with `JSON_RPC_LOG_PERSIST_DAY_NUM` env var
- Log result/request/response via #[Log] attribute
- Customize log formatting by listening to `JsonRpcLogFormatEvent`

## Advanced

- Supports async log insertion for high performance
- Integrates with Tourze EasyAdmin for UI
- Provides full request/response/exception/context info

## Contributing

- Submit issues via GitHub
- Pull requests welcome, follow PSR-12 code style
- Run tests with PHPUnit

## License

MIT License. Copyright (c) tourze

## Changelog

See [CHANGELOG](CHANGELOG.md) for version history and upgrade notes.
