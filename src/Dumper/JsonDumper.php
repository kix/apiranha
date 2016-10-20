<?php

namespace Kix\Apiranha\Dumper;

use Kix\Apiranha\ResourceDefinitionInterface;

/**
 * A JsonDumper dumps resource definitions as JSON.
 */
class JsonDumper implements DumperInterface
{
    /**
     * @param ResourceDefinitionInterface[] $resourceDefinitions
     * @return string
     */
    public function dump($resourceDefinitions)
    {
        $defns = [];

        foreach ($resourceDefinitions as $resourceDefinition) {
            $params = [];

            foreach ($resourceDefinition->getParameters() as $parameter) {
                $params []= [
                    'name' => $parameter->getName(),
                    'type' => $parameter->getType()
                ];
            }

            $defns [$resourceDefinition->getName()]= [
                'path' => $resourceDefinition->getPath(),
                'method' => $resourceDefinition->getMethod(),
                'returnType' => $resourceDefinition->getReturnType(),
                'parameters' => $params,
            ];
        }

        return json_encode($defns, JSON_PRETTY_PRINT);
    }
}
