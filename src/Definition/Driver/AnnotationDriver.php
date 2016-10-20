<?php

namespace Kix\Apiranha\Definition\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Kix\Apiranha\Annotation;
use Kix\Apiranha\Exception\InvalidArgumentException;
use Kix\Apiranha\Exception\InvalidResourceDefinitionException;
use Kix\Apiranha\Exception\LogicException;
use Kix\Apiranha\Exception\RuntimeException;
use Kix\Apiranha\ParameterDefinition;
use Kix\Apiranha\ResourceDefinition;
use Kix\Apiranha\ResourceDefinitionInterface;

/**
 * Annotation driver creates resource definitions from annotations.
 *
 * Create an interface and pass it to AnnotationDriver#createDefinitions, and you get a whole bunch of 'em:
 *
 * <pre>
 * use Kix\Apiranha\Annotations as Rest;
 *
 * interface MyApi {
 *      /**
 *       * @Rest\CGet("/api/list")
 *       *\/
 *      public function listAction();
 *      /**
 *       * @Rest\Get("/api/get/{id}")
 *       *\/
 *      public function showAction($id);
 * }
 *
 * $definitions = $annotationDriver->createDefinitions(MyApi::class); // → `ResourceDefinitionInterface[]`
 * </pre>
 */
class AnnotationDriver
{
    private static $annotationMethodMap = [
        Annotation\CGet::class => ResourceDefinitionInterface::METHOD_CGET,
        Annotation\Get::class => ResourceDefinitionInterface::METHOD_GET,
        Annotation\Delete::class => ResourceDefinitionInterface::METHOD_DELETE,
        Annotation\Post::class => ResourceDefinitionInterface::METHOD_POST,
        Annotation\Put::class => ResourceDefinitionInterface::METHOD_PUT,
    ];

    /**
     * Creates a definition for the given interface, taking into account its method annotations and type hints.
     *
     * Note that classes are not supported here. If you pass a class name, you get an exception, as there's no way
     * we could use the method's own logic, at least currently.
     *
     * @param string $source Name of the interface to create definitions from.
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws RuntimeException
     *
     * @TODO: Rethrow, process?
     * @throws InvalidArgumentException
     * @throws InvalidResourceDefinitionException
     *
     * @return ResourceDefinitionInterface[]
     */
    public function createDefinitions($source)
    {
        if (!interface_exists($source)) {
            throw new InvalidArgumentException(sprintf(
                'Class or interface `%s` is specified as a resource definition source, but it doesn\'t exist',
                $source
            ));
        }

        if (class_exists($source)) {
            throw new LogicException(sprintf(
                'A resource should be defined over an interface, not over a class (as in %s), because we cannot '.
                'use its implementation.',
                $source
            ));
        }
        
        $reader = new AnnotationReader();
        $reflection = new \ReflectionClass($source);
        $definitions = [];

        foreach ($reflection->getMethods() as $methodRefl) {
            $annotations = $reader->getMethodAnnotations($methodRefl);
            $method = false;
            $path = false;
            $returnType = false;
            $parameters = [];
            
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Annotation\Method) {
                    if ($method) {
                        throw new RuntimeException(sprintf(
                            'Multiple method declarations are not supported, but were declared on `%s`',
                            $methodRefl->class.'#'.$methodRefl->getName()    
                        ));
                    }
                    
                    $method = static::$annotationMethodMap[get_class($annotation)];
                    $path = $annotation->value;
                }
                
                if ($annotation instanceof Annotation\Returns) {
                    // @TODO: check for scalar types
                    if (!class_exists($annotation->value)) {
                        throw new InvalidArgumentException(sprintf(
                            'Class `%s` declared as return type for `%s`, but it does not exist',
                            $reflection->getName().'#'.$methodRefl->getName(),
                            $annotation->value
                        ));
                    }
                    
                    $returnType = $annotation->value;
                }

                if ($annotation instanceof Annotation\Parameter) {
//                     $parameters []= new ParameterDefinition(
//                         $annotation->
//
//                     );
                }
            }

            if (!$path) {
                throw new RuntimeException(sprintf(
                    'Path was not declared for method %s',
                    $reflection->getName().'#'.$methodRefl->getName()
                ));
            }

            if (!$method) {
                throw new RuntimeException(sprintf(
                    'API method was not declared for method %s',
                    $reflection->getName().'#'.$methodRefl->getName()
                ));
            }

            if (count($parameters) === 0) {
                foreach ($methodRefl->getParameters() as $parameter) {
                    $type = null;

                    if (method_exists($parameter, 'getType') && $parameter->hasType()) {
                        $type = (string) $parameter->getType();
                    }

                    if ($parameter->getClass()) {
                        $type = $parameter->getClass()->name;
                    }

                    if (!$type) {
                        throw new RuntimeException(sprintf(
                            'Could not determine a type for parameter `%s` of method `%s`',
                            $parameter->getName(),
                            $methodRefl->getDeclaringClass().'::'.$methodRefl->getName()
                        ));
                    }

                    $parameters []= new ParameterDefinition(
                        $parameter->getName(),
                        $type,
                        !$parameter->allowsNull()
                    );
                }
            }

            if (method_exists($methodRefl, 'getReturnType') && $methodRefl->getReturnType()) {
                if ($returnType && $returnType !== $methodRefl->getReturnType()) {
                    throw new LogicException(sprintf(
                        'Method `%s`\'s return type (%s) does not match the annotated one, which is %s',
                        $methodRefl->getName(),
                        $methodRefl->getReturnType(),
                        $returnType
                    ));
                }
                
                $returnType = $methodRefl->getReturnType();
            }

            $definitions [$methodRefl->getName()]= new ResourceDefinition(
                $methodRefl->getName(),
                $method,
                $path,
                $returnType,
                $parameters
            );
        }

        return $definitions;
    }
}
