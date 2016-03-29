<?php

namespace Kix\Apiranha\Util;

use Symfony\Component\PropertyAccess\PropertyAccess as SymfonyPropertyAccess;

/**
 * Class PropertyAccess
 */
class PropertyAccess
{
    /**
     * Checks if an abstract entity of the given class can fulfil the property path requirement.
     *
     * @param string $className
     * @param string $propertyPath
     * @return bool
     */
    public static function isPathAccessible($className, $propertyPath)
    {
        $reflClass = new \ReflectionClass($className);
        $fakeInstance = $reflClass->newInstanceWithoutConstructor();

        foreach ($reflClass->getProperties() as $reflectionProperty) {
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($fakeInstance, 'test');
        }
        
        $accessor = SymfonyPropertyAccess::createPropertyAccessor();
        
        return $accessor->isReadable($fakeInstance, $propertyPath);
    }
}
