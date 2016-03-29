<?php

namespace Kix\Apiranha\Exception;

/**
 * Class InvalidParameterException
 */
class InvalidParameterException extends InvalidArgumentException
{
    const SCALAR_TYPES = ['int', 'integer', 'float', 'long', 'bool', 'boolean', 'string', 'array'];

    /**
     * Thrown when a parameter passed to a <code>ParameterDefinition</code> does not comply with the definition's 
     * parameter specification.
     * 
     * @param string|array $expected A list of class names or scalar types
     * @param mixed $actual Actual value passed
     * @return InvalidParameterException
     */
    public static function forTypeMismatch($expected, $actual)
    {
        $expectedClasses = [];
        $expectedTypes = [];
        
        if (is_array($expected)) {
            foreach ($expected as $item) {
                if (in_array($item, self::SCALAR_TYPES, true)) {
                    $expectedTypes []= $item;
                } elseif (class_exists($item)) {
                    $expectedClasses []= $item;
                }
            }
        } else {
            if (in_array($expected, self::SCALAR_TYPES, true)) {
                $expectedTypes []= $expected;
            } elseif (class_exists($expected)) {
                $expectedClasses []= $expected;
            }
        }
        
        $classOrType = [];

        $expectedClassCount = count($expectedClasses);
        if ($expectedClassCount > 0) {
            $title = ($expectedClassCount > 1) ? 'classes' : 'class';
            $classOrType []= $title.' '.implode(', ', $expectedClasses);
        }

        $expectedTypeCount = count($expectedTypes);
        if ($expectedTypeCount > 0) {
            $title = ($expectedTypeCount > 1) ? 'types' : 'type';
            $classOrType []= $title.' '.implode(', ', $expectedTypes);
        }

        $actualType = is_object($actual) ? get_class($actual) : gettype($actual);
        
        return new self(sprintf(
            'Expected %s of %s; got %s instead',
            (count($expectedClasses) > 0) ? 'an instance' : 'an argument',
            implode(' or ', $classOrType) ,
            $actualType
        ));
    }
}
