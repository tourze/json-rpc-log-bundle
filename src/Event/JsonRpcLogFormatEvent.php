<?php

namespace Tourze\JsonRPCLogBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tourze\JsonRPCLogBundle\Entity\RequestLog;

class JsonRpcLogFormatEvent extends Event
{
    private RequestLog $log;

    private string $result = '';

    private array $request = [];

    public function getLog(): RequestLog
    {
        return $this->log;
    }

    public function setLog(RequestLog $log): void
    {
        $this->log = $log;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    public function getRequest(): array
    {
        return $this->request;
    }

    public function setRequest(array $request): void
    {
        $this->request = $request;
    }
}
