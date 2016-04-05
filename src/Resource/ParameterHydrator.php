<?php

namespace Kix\Apiranha\Resource;

use Kix\Apiranha\ParameterDefinitionInterface;
use Kix\Apiranha\Exception\InvalidParameterException;

/**
 * Parameter hydrator is responsible for matching an array of parameters passed to an endpoint against an array of
 * parameter definitions.
 */
class ParameterHydrator
{
    /**
     * @param ParameterDefinitionInterface[] $definitions
     * @param $arguments
     * @throws InvalidParameterException for mismatched types
     * @return array
     */
    public static function hydrateParameters($definitions, $arguments)
    {
        $i = 0;
        $parameters = [];

        foreach ($definitions as $parameterDefinition) {
            if (in_array($parameterDefinition->getType(), InvalidParameterException::$scalarTypes, true)) {
                $validator = 'is_'.$parameterDefinition->getType();
                if (!$validator($arguments[$i])) {
                    throw InvalidParameterException::forTypeMismatch(['string', 'int', 'integer', 'bool'], $arguments[$i]);
                }
            } else {
                $class = $parameterDefinition->getType();

                if (!$arguments[$i] instanceof $class) {
                    throw InvalidParameterException::forTypeMismatch($class, $arguments[$i]);
                }
            }

            $parameters[$parameterDefinition->getName()] = $arguments[$i];
            $i++;
        }

        return $parameters;
    }
}
