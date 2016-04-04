<?php

namespace Kix\Apiranha\Listener;

use Kix\Apiranha\ResourceDefinitionInterface;
use Psr\Http\Message\ResponseInterface;

interface AfterDataListenerInterface
{
    public function process(ResponseInterface $response, ResourceDefinitionInterface $resource);
}
