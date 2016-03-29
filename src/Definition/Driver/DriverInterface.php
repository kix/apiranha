<?php

namespace Kix\Apiranha\Definition\Driver;

use Kix\Apiranha\ResourceDefinitionInterface;

/**
 * A definition driver generates definitions from a given source.
 */
interface DriverInterface
{
    /**
     * @param string $source
     * @return ResourceDefinitionInterface[]
     */
    public function createDefinitions($source);
}
