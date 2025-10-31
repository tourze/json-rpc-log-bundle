<?php

namespace Tourze\JsonRPCLogBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIpBundle\Traits\CreatedFromIpAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use Tourze\DoctrineUserAgentBundle\Attribute\CreateUserAgentColumn;
use Tourze\DoctrineUserBundle\Traits\CreatedByAware;
use Tourze\JsonRPCLogBundle\Repository\RequestLogRepository;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;

/**
 * 接口日志
 *
 * 一般来说，我们只记录重要的接口日志，主要是可能写数据的日志
 */
#[AsScheduleClean(expression: '41 1 * * *', defaultKeepDay: 180, keepDayEnv: 'JSON_RPC_LOG_PERSIST_DAY_NUM')]
#[ORM\Entity(repositoryClass: RequestLogRepository::class)]
#[ORM\Table(name: 'json_rpc_log', options: ['comment' => 'json_rpc日志'])]
class RequestLog implements \Stringable
{
    use CreateTimeAware;
    use CreatedByAware;
    use CreatedFromIpAware;
    use SnowflakeKeyAware;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '操作记录'])]
    private ?string $description = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '请求内容'])]
    private ?string $request = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '响应内容'])]
    private ?string $response = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '异常'])]
    private ?string $exception = null;

    #[Assert\Length(max: 40)]
    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '服务端IP'])]
    private ?string $serverIp = null;

    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120, nullable: true, options: ['comment' => 'StopWatch结果'])]
    private ?string $stopwatchResult = null;

    #[Assert\Length(max: 15)]
    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true, options: ['comment' => '执行时长'])]
    private ?string $stopwatchDuration = null;

    #[Assert\Length(max: 200)]
    #[ORM\Column(length: 200, nullable: true, options: ['comment' => 'API名称'])]
    private ?string $apiName = null;

    #[Assert\Length(max: 65535)]
    #[CreateUserAgentColumn]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '创建时UA'])]
    private ?string $createdFromUa = null;

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function setRequest(?string $request): void
    {
        $this->request = $request;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): void
    {
        $this->response = $response;
    }

    public function getException(): ?string
    {
        return $this->exception;
    }

    public function setException(?string $exception): void
    {
        $this->exception = $exception;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getServerIp(): ?string
    {
        return $this->serverIp;
    }

    public function setServerIp(?string $serverIp): void
    {
        $this->serverIp = $serverIp;
    }

    public function getStopwatchResult(): ?string
    {
        return $this->stopwatchResult;
    }

    public function setStopwatchResult(?string $stopwatchResult): void
    {
        $this->stopwatchResult = $stopwatchResult;
    }

    public function getStopwatchDuration(): ?string
    {
        return $this->stopwatchDuration;
    }

    public function setStopwatchDuration(?string $stopwatchDuration): void
    {
        $this->stopwatchDuration = $stopwatchDuration;
    }

    public function getApiName(): ?string
    {
        return $this->apiName;
    }

    public function setApiName(?string $apiName): void
    {
        $this->apiName = $apiName;
    }

    public function getCreatedFromUa(): ?string
    {
        return $this->createdFromUa;
    }

    public function setCreatedFromUa(?string $createdFromUa): void
    {
        $this->createdFromUa = $createdFromUa;
    }

    public function getRenderStatus(): string
    {
        return null !== $this->exception && '' !== $this->exception ? '异常' : '正常';
    }

    public function getRenderTrackUser(): ?string
    {
        return $this->createdBy;
    }

    public function __toString(): string
    {
        return $this->apiName ?? $this->id ?? '';
    }
}
