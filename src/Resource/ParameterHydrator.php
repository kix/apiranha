<?php

namespace Kix\Apiranha\Resource;

use Kix\Apiranha\Exception\RuntimeException;
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
            $type = $parameterDefinition->getType();

            if (in_array($type, InvalidParameterException::$scalarTypes, true)) {
                $validator = 'is_'.$type;
                if (!$validator($arguments[$i])) {
                    throw InvalidParameterException::forTypeMismatch(['string', 'int', 'integer', 'bool'], $arguments[$i]);
                }
            } else {
                $class = $type;

                if (!class_exists($class)) {
                    throw new RuntimeException(sprintf(
                        'Class `%s` does not exist',
                        $class
                    ));
                }

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
