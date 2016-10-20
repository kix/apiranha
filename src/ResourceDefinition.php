<?php

namespace Kix\Apiranha;

use Kix\Apiranha\Exception\InvalidArgumentException;
use Kix\Apiranha\Exception\InvalidParameterDefinitionException;
use Kix\Apiranha\Exception\InvalidResourceDefinitionException;
use Kix\Apiranha\Util\PropertyAccess;

/**
 * Class ResourceDefinition
 */
class ResourceDefinition implements ResourceDefinitionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $path;

    /**
     * @var ParameterDefinitionInterface[]
     */
    private $parameters;

    /**
     * @var string
     */
    private $returnType;

    /**
     * @var string
     */
    private $description;
    
    private static $supportedMethods = [
        ResourceDefinitionInterface::METHOD_GET,
        ResourceDefinitionInterface::METHOD_CGET,
        ResourceDefinitionInterface::METHOD_POST,
        ResourceDefinitionInterface::METHOD_DELETE,
        ResourceDefinitionInterface::METHOD_PUT,
    ];
    
    /**
     * ResourceDefinition constructor.
     *
     * @param string $name Action name (will be exposed on the endpoint)
     * @param string $method One of ResourceDefinitionInterface::METHOD_* options
     * @param string $path Resource path
     * @param string $returnType A class name or a scalar type that should be returned from the resource
     * @param ParameterDefinitionInterface[] $parameters
     *
     * @throws InvalidArgumentException for bad parameters
     * @throws InvalidResourceDefinitionException
     * @throws InvalidParameterDefinitionException
     */
    public function __construct($name, $method, $path, $returnType = null, array $parameters = array())
    {
        $this->name = $name;
        $this->setMethod($method);
        $this->path = $path;

        if ($returnType) {
            $this->setReturnType($returnType);
        }

        try {
            $this->parameters = self::processParameters($path, $parameters);
        } catch (InvalidResourceDefinitionException $e) {
            throw new InvalidResourceDefinitionException(
                $e->getMessage() . sprintf(' in %s (%s %s)', $this->name, $this->method, $this->path)
            );
        } catch (InvalidParameterDefinitionException $e) {
            throw new InvalidParameterDefinitionException(
                $e->getMessage() . sprintf(' in %s (%s %s)', $this->name, $this->method, $this->path)
            );
        }
    }
    
    public static function processParameters($path, array $parameters = array())
    {
        preg_match_all('/\{([a-zA-Z.]+)\}/', $path, $matches);
        $paramNames = $matches[1];
        
        $roots = array_unique(array_map(function($name) {
            return explode('.', $name)[0];
        }, $paramNames));
        
        if (count($roots) > count($parameters)) {
            throw InvalidParameterDefinitionException::countMismatch(count($matches[0]), count($parameters));
        }
        
        $result = [];
        
        foreach ($parameters as $parameter) {
            if (!$parameter instanceof ParameterDefinitionInterface) {
                throw InvalidParameterDefinitionException::invalidParameter($parameter);
            }
            
            $result[$parameter->getName()] = $parameter;
        }

        $pathParams = array_filter($paramNames, function ($paramName) {
            return strpos($paramName, '.') !== false;
        });

        foreach ($pathParams as $pathParam) {
            $exploded = explode('.', $pathParam, 2);
            list($root, $propPath) = $exploded;

            if (!PropertyAccess::isPathAccessible($result[$root]->getType(), $propPath)) {
                throw new InvalidResourceDefinitionException(sprintf(
                    'Path `%s` declared on `%s` is not accessible',
                    $propPath,
                    $root
                ));
            }
        }

        return $result;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return ParameterDefinitionInterface[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * @param string $returnType A class name
     * @throws InvalidArgumentException for non-existent classes
     */
    private function setReturnType($returnType)
    {
        if (!class_exists($returnType)) {
            $forResource = ' ';
            
            if ($this->name) {
                $forResource = 'for resource `'.$this->name.'` ';
            }
            
            throw new InvalidArgumentException(sprintf(
                'Class `%s` declared as return type %s does not exist',
                $returnType,
                $forResource
            ));
        }
        
        $this->returnType = $returnType;
    }

    /**
     * @param string $method
     * @throws InvalidArgumentException for non-supported methods
     */
    private function setMethod($method)
    {
        if (!in_array($method, self::$supportedMethods)) {
            throw new InvalidArgumentException(sprintf(
                'Method `%s` is not supported. Use one of: %s',
                $method,
                implode(', ', self::$supportedMethods)
            ));
        }

        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
