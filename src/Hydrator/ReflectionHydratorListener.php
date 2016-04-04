<?php

namespace Kix\Apiranha\Hydrator;

use Kix\Apiranha\Listener\AfterDataListenerInterface;
use Kix\Apiranha\ResourceDefinitionInterface;
use Kix\Apiranha\Response\ApiResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ReflectionHydratorListenerInterface
 */
class ReflectionHydratorListener implements AfterDataListenerInterface
{
    /**
     * @var \ReflectionObject
     */
    private $reflObject;
    
    public function process(ResponseInterface $response, ResourceDefinitionInterface $resource)
    {
        if (!$response instanceof ApiResponse) {
            throw new \InvalidArgumentException();
        }

        $dataClass = $resource->getReturnType();
        $reflection = new \ReflectionClass($dataClass);
        $this->reflObject = new \ReflectionObject($reflection->newInstance());

        if ($resource->getMethod() === ResourceDefinitionInterface::METHOD_CGET) {
            $result = [];
            
            foreach ($response->getData() as $row) {
                $result []= $this->hydrateRow(
                    $reflection->newInstance(),
                    $row
                );
            }
        } else {
            $result = $this->hydrateRow(
                $reflection->newInstance(),
                $response->getData()
            );
        }
        
        return $result;
    }
    
    private function hydrateRow($instance, $data)
    {
        foreach ($data as $k => $v) {
            $reflectionProperty = $this->reflObject->getProperty($k);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($instance, $v);
        }

        return $instance;
    }
}
