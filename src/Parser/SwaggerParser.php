<?php

namespace Kix\Apiranha\Parser;

use Kix\Apiranha\Exception\InvalidArgumentException;
use Kix\Apiranha\ParameterDefinition;
use Kix\Apiranha\Parser\Exception\InvalidDefinitionException;
use Kix\Apiranha\ResourceDefinition;
use Kix\Apiranha\ResourceDefinitionInterface;

/**
 * Class SwaggerParser
 */
class SwaggerParser implements ParserInterface
{
    private static $methodMap = [
        'get' => ResourceDefinitionInterface::METHOD_GET,
        'post' => ResourceDefinitionInterface::METHOD_POST,
    ];

    /**
     * @param string $source
     * @throws InvalidDefinitionException
     * @return ResourceDefinitionInterface[]
     */
    public function parse($source)
    {
        $data = json_decode($source, true);
        $resources = [];

        foreach ($data['paths'] as $path => $specs) {
            foreach ($specs as $method => $spec) {
                $parameters = [];

                if (array_key_exists('parameters', $spec)) {
                    foreach ($spec['parameters'] as $paramSpec) {
                        try {
                            $parameters [] = new ParameterDefinition(
                                $paramSpec['name'],
                                $paramSpec['type'],
                                $paramSpec['required']
                            );
                        } catch (InvalidArgumentException $e) {
                            throw new InvalidDefinitionException(sprintf(
                                'Swagger definition for `%s` is invalid',
                                $path
                            ), 0, $e);
                        }
                    }
                }

                try {
                    $resources []= new ResourceDefinition(
                        $spec['operationId'],
                        self::$methodMap[$method],
                        $path,
                        null,
                        $parameters
                    );
                } catch (\Exception $e) {
                    throw new InvalidDefinitionException(sprintf(
                        'Could not create a resource definition from source: %s',
                        json_encode($spec)
                    ), 0, $e);
                }

            }
        }

        return $resources;
    }
}
