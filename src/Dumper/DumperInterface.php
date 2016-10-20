<?php

namespace Kix\Apiranha\Dumper;

use Kix\Apiranha\ResourceDefinitionInterface;

/**
 * A dumper creates text representations of resources.
 */
interface DumperInterface
{
    /**
     * @param ResourceDefinitionInterface[] $resourceDefinitions
     * @return string
     */
    public function dump($resourceDefinitions);
}
