<?php
namespace Kix\Apiranha\Parser;

use Kix\Apiranha\ResourceDefinitionInterface;

interface ParserInterface
{
    /**
     * @param string $source
     * @return ResourceDefinitionInterface[]
     */
    public function parse($source);
}
