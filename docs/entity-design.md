# RequestLog 实体设计说明

## 表结构

- 表名：`json_rpc_log`
- 说明：用于存储 JsonRPC 服务端关键请求与响应的日志信息。

## 字段一览

| 字段名           | 类型            | 说明           |
|------------------|-----------------|----------------|
| id               | bigint/string   | 主键，雪花ID    |
| request          | text            | 请求内容        |
| response         | text            | 响应内容        |
| exception        | text            | 异常内容        |
| serverIp         | string(40)      | 服务端IP        |
| stopwatchResult  | string(120)     | Stopwatch结果   |
| stopwatchDuration| decimal(12,2)   | 执行时长        |
| apiName          | string(200)     | API名称         |
| createTime       | datetime        | 创建时间        |
| createdFromIp    | string(45)      | 操作IP          |
| createdFromUa    | text            | 创建时UA        |
| createdBy        | string          | 创建人          |

## 字段设计说明

- **id**：主键，采用雪花算法生成，保证分布式唯一性。
- **request/response/exception**：分别记录请求报文、响应报文、异常信息，便于追踪和排查。
- **serverIp/createdFromIp/createdFromUa**：追踪请求来源、服务端信息和用户代理，有助于安全审计。
- **stopwatchResult/stopwatchDuration**：记录接口执行耗时与详细 StopWatch 结果，便于性能分析。
- **apiName**：记录调用的 API 方法名。
- **createTime/createdBy**：记录日志生成时间和操作人。

## 相关注解与特性

- 支持 EasyAdmin 导出、列表、过滤等功能（通过注解实现）。
- 支持自动清理（通过定时任务注解 AsScheduleClean），默认保留180天。
- 支持通过事件自定义日志格式。

## 示例实体代码片段

```php
#[ORM\Entity(repositoryClass: RequestLogRepository::class)]
#[ORM\Table(name: 'json_rpc_log', options: ['comment' => 'json_rpc日志'])]
class RequestLog
{
    // ...
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;
    // ... 其余字段见上表
}
```
