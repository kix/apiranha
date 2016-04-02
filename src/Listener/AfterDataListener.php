<?php

namespace Kix\Apiranha\Listener;

use Kix\Apiranha\ResourceDefinitionInterface;
use Psr\Http\Message\ResponseInterface;

interface AfterDataListener
{
    public function process(ResponseInterface $response, ResourceDefinitionInterface $resource);
}
