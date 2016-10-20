<?php

namespace Tests\Kix\Apiranha;

use PHPUnit_Framework_Assert as Assert;

/**
 * Class ReflectionAsserts
 */
trait ReflectionAsserts
{
    private static function getReflectionClass($class)
    {
        if (!$class instanceof \ReflectionClass) {
            if (!class_exists($class)) {
                throw new \InvalidArgumentException(sprintf(
                    'Class %s passed is not a \ReflectionClass, or the class does not exist',
                    $class
                ));
            }

            $class = new \ReflectionClass($class);
        }

        return $class;
    }

    public static function assertHasMethod($methodName, $class)
    {
        $class = self::getReflectionClass($class);

        Assert::assertTrue($class->hasMethod($methodName), sprintf(
            'Failed to assert that class %s has method %s',
            $class->getName(),
            $methodName
        ));
    }

    public static function assertMethodHasAnnotation($class, $method)
    {
        $class = self::getReflectionClass($class);
        $method = $class->getMethod($method);
        $docBlock = $method->getDocComment();
    }
}
