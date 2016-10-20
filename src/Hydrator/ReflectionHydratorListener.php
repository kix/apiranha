<?php

namespace Kix\Apiranha\Hydrator;

use Kix\Apiranha\Exception\RuntimeException;
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

    /**
     * @param ResponseInterface           $response
     * @param ResourceDefinitionInterface $resource
     * @return array
     */
    public function process(ResponseInterface $response, ResourceDefinitionInterface $resource)
    {
        if (!$response instanceof ApiResponse) {
            throw new \InvalidArgumentException(sprintf(
                'Expected an ApiResponse, got %s',
                get_class($response)
            ));
        }

        $dataClass = $resource->getReturnType();

        if (!$dataClass) {
            throw new RuntimeException(sprintf(
                'Return type is not defined for resource `%s` (%s), cannot hydrate',
                $resource->getName(),
                $resource->getPath()
            ));
        }

        if (!class_exists($dataClass)) {
            throw new RuntimeException(sprintf(
                'Cannot hydrate an instance of class `%s` as the class does not exist',
                $dataClass
            ));
        }

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

    /**
     * @param mixed $instance
     * @param array $data
     * @return mixed
     */
    private function hydrateRow($instance, $data)
    {
        $hydratedCount = 0;

        foreach ($data as $k => $v) {
            $reflectionProperty = self::findProperty($this->reflObject, $k);

            if (!$reflectionProperty) {
                continue;
            }

            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($instance, $v);
            $hydratedCount++;
        }

        if (!$hydratedCount === 0) {
            throw new RuntimeException('Empty object hydrated');
        }

        return $instance;
    }

    /**
     * @param \ReflectionObject $reflectionObject
     * @param string $key
     * @return null|\ReflectionProperty
     */
    private static function findProperty(\ReflectionObject $reflectionObject, $key)
    {
        if ($reflectionObject->hasProperty($key)) {
            return $reflectionObject->getProperty($key);
        }

        $camelized = lcfirst(join(array_map('ucfirst', explode('_', $key))));

        if ($reflectionObject->hasProperty($camelized)) {
            return $reflectionObject->getProperty($camelized);
        }

        return null;
    }
}
