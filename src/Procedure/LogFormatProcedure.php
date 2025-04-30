<?php

namespace Tourze\JsonRPCLogBundle\Procedure;

use Tourze\JsonRPC\Core\Model\JsonRpcRequest;

interface LogFormatProcedure
{
    public function generateFormattedLogText(JsonRpcRequest $request): string;
}
