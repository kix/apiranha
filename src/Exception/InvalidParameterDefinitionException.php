<?php

namespace Kix\Apiranha\Exception;

use Kix\Apiranha\ParameterDefinitionInterface;

/**
 * Class InvalidParameterDefinitionException
 */
class InvalidParameterDefinitionException extends LogicException
{
    /**
     * Thrown when a parameter is not an implementation of <code>ParameterDefinitionInterface</code>.
     * 
     * @param mixed $parameter
     * @return InvalidParameterDefinitionException
     */
    public static function invalidParameter($parameter)
    {
        return new self(sprintf(
            'Parameter definitions must be instances of `%s`, `%s` given',
            ParameterDefinitionInterface::class,
            is_object($parameter) ? get_class($parameter) : gettype($parameter)
        ));
    }

    /**
     * Thrown when not all required parameters were passed.
     * 
     * @param int $expectedCount
     * @param int $actualCount
     * @return InvalidParameterDefinitionException
     */
    public static function countMismatch($expectedCount, $actualCount)
    {
        return new self(sprintf(
            'Invalid parameter definition count: expected %s, got %s',
            $expectedCount,
            $actualCount
        ));
    }
}
