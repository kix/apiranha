<?php

namespace Kix\Apiranha\Hydrator;

use GeneratedHydrator\Configuration;
use Kix\Apiranha\ResourceDefinitionInterface;
use Kix\Apiranha\Response\ApiResponse;

/**
 * Class GeneratedHydratorListener
 */
class GeneratedHydratorListener 
{
    public function process(ApiResponse $response, ResourceDefinitionInterface $resource)
    {
        $returnType = $resource->getReturnType();
        $config = new Configuration($returnType);
        $hydratorClass = $config->createFactory()->getHydratorClass();
        $hydrator = new $hydratorClass();
        $object = new $returnType();

        if ($resource->getMethod() !== ResourceDefinitionInterface::METHOD_CGET) {
            return $hydrator->hydrate(
                $response->getData(),
                $object
            );
        }

        $result = [];

        foreach ($response->getData() as $item) {
            $current = clone $object;

            $result []= $hydrator->hydrate(
                $item,
                $current
            );
        }

        return $result;
    }
}
