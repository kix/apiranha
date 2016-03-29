<?php

namespace Kix\Apiranha\Definition\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Kix\Apiranha\Annotation;
use Kix\Apiranha\Exception\InvalidArgumentException;
use Kix\Apiranha\Exception\InvalidResourceDefinitionException;
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
    const ANNOTATION_METHOD_MAP = [
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
     * @TODO: use own exceptions instead:
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \RuntimeException
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
            throw new \InvalidArgumentException(sprintf(
                'Class or interface `%s` is specified as a resource definition source, but it doesn\'t exist',
                $source
            ));
        }

        if (class_exists($source)) {
            throw new \LogicException(sprintf(
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
                        throw new \RuntimeException(sprintf(
                            'Multiple method declarations are not supported, but were declared on `%s`',
                            $methodRefl->class.'#'.$methodRefl->getName()    
                        ));
                    }
                    
                    $method = static::ANNOTATION_METHOD_MAP[get_class($annotation)];
                    $path = $annotation->value;
                }
                
                if ($annotation instanceof Annotation\Returns) {
                    // @TODO: check for scalar types
                    // @TODO: subclass and add an exception message
                    if (!class_exists($annotation->value)) {
                        throw new \RuntimeException();
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
                throw new \RuntimeException();
            }

            if (!$method) {
                throw new \RuntimeException();
            }

            if (count($parameters) === 0) {
                foreach ($methodRefl->getParameters() as $parameter) {
                    $type = null;

                    if (method_exists($parameter, 'getType') && $parameter->hasType()) {
                        $type = $parameter->getType();
                    }

                    if ($parameter->getClass()) {
                        $type = $parameter->getClass()->name;
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
                    // @TODO: throw an exception saying these things ^^ are different
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
