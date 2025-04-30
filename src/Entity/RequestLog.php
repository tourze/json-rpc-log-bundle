<?php

namespace Tourze\JsonRPCLogBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineUserAgentBundle\Attribute\CreateUserAgentColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Exportable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\JsonRPCLogBundle\Repository\RequestLogRepository;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;

/**
 * 接口日志
 *
 * 一般来说，我们只记录重要的接口日志，主要是可能写数据的日志
 */
#[AsScheduleClean(expression: '41 1 * * *', defaultKeepDay: 180, keepDayEnv: 'JSON_RPC_LOG_PERSIST_DAY_NUM')]
#[AsPermission(title: '接口日志')]
#[Exportable]
#[ORM\Entity(repositoryClass: RequestLogRepository::class)]
#[ORM\Table(name: 'json_rpc_log', options: ['comment' => 'json_rpc日志'])]
class RequestLog
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'recursive_view', 'api_tree'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ListColumn]
    #[FormField]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '操作记录'])]
    private ?string $description = null;

    #[Groups(['restful_read'])]
    #[Keyword(inputWidth: 240)]
    #[Ignore]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '请求内容'])]
    private ?string $request = null;

    #[Groups(['restful_read'])]
    #[Keyword]
    #[Ignore]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '响应内容'])]
    private ?string $response = null;

    #[Groups(['restful_read'])]
    #[Keyword]
    #[Ignore]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '异常'])]
    private ?string $exception = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '服务端IP'])]
    private ?string $serverIp = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(length: 120, nullable: true, options: ['comment' => 'StopWatch结果'])]
    private ?string $stopwatchResult = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true, options: ['comment' => '执行时长'])]
    private ?string $stopwatchDuration = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(length: 200, nullable: true, options: ['comment' => 'API名称'])]
    private ?string $apiName = null;

    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[Groups(['restful_read'])]
    #[CreateIpColumn]
    #[Filterable]
    #[ExportColumn]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '操作IP'])]
    private ?string $createdFromIp = null;

    #[CreateUserAgentColumn]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '创建时UA'])]
    private ?string $createdFromUa = null;

    #[IndexColumn]
    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function setRequest(string $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getException(): ?string
    {
        return $this->exception;
    }

    public function setException(?string $exception): self
    {
        $this->exception = $exception;

        return $this;
    }

    #[ListColumn(title: '用户')]
    public function renderTrackUser(): string
    {
        return $this->getCreatedBy() ?: '';
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    #[ExportColumn(title: '状态')]
    #[ListColumn(title: '状态')]
    public function renderStatus(): string
    {
        return $this->getException() ? '异常' : '成功';
    }

    public function setCreatedFromIp(string $createdFromIp): static
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function getServerIp(): ?string
    {
        return $this->serverIp;
    }

    public function setServerIp(?string $serverIp): self
    {
        $this->serverIp = $serverIp;

        return $this;
    }

    public function getStopwatchResult(): ?string
    {
        return $this->stopwatchResult;
    }

    public function setStopwatchResult(?string $stopwatchResult): self
    {
        $this->stopwatchResult = $stopwatchResult;

        return $this;
    }

    public function getStopwatchDuration(): ?string
    {
        return $this->stopwatchDuration;
    }

    public function setStopwatchDuration(?string $stopwatchDuration): self
    {
        $this->stopwatchDuration = $stopwatchDuration;

        return $this;
    }

    public function getApiName(): ?string
    {
        return $this->apiName;
    }

    public function setApiName(?string $apiName): self
    {
        $this->apiName = $apiName;

        return $this;
    }

    public function getCreatedFromUa(): ?string
    {
        return $this->createdFromUa;
    }

    public function setCreatedFromUa(?string $createdFromUa): static
    {
        $this->createdFromUa = $createdFromUa;

        return $this;
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): self
    {
        $this->createTime = $createdAt;

        return $this;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setCreatedBy(?string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }
}
