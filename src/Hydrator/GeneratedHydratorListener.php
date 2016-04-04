<?php

namespace Kix\Apiranha\Hydrator;

use GeneratedHydrator\Configuration;
use Kix\Apiranha\Exception\InvalidArgumentException;
use Kix\Apiranha\Listener\AfterDataListenerInterface;
use Kix\Apiranha\ResourceDefinitionInterface;
use Kix\Apiranha\Response\ApiResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Class GeneratedHydratorListenerInterface
 */
class GeneratedHydratorListener implements AfterDataListenerInterface
{
    public function process(ResponseInterface $response, ResourceDefinitionInterface $resource)
    {
        if (!$response instanceof ApiResponse) {
            throw new InvalidArgumentException(sprintf(
                'Expected an `ApiResponse`, got `%s` instead',
                get_class($response)
            ));
        }

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
