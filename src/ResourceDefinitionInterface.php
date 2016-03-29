<?php

namespace Kix\Apiranha;

/**
 * Describes an API resource (that is, a single method)
 */
interface ResourceDefinitionInterface
{
    const METHOD_GET = 'GET';
    
    const METHOD_CGET = 'CGET';
    
    const METHOD_POST = 'POST';
    
    const METHOD_PUT = 'PUT';
    
    const METHOD_DELETE = 'DELETE';
    
    /**
     * Returns the method name where the resource should be exposed at. 
     * 
     * @return string
     */
    public function getName();
    
    /**
     * Returns the API method that is used by the resource
     *
     * The method should be one of <code>ResourceDefinitionInterface::METHOD_*</code> constants.
     * 
     * @return string
     */
    public function getMethod();

    /**
     * Returns the path pattern for the resource, e.g. <code>/users/{id}</code>.
     * 
     * Parts in <code>{}</code>'s are treated as placeholders.
     * 
     * @return string
     */
    public function getPath();

    /**
     * Returns the parameters defined for the resource, if any.
     * 
     * @return ParameterDefinitionInterface[]
     */
    public function getParameters();

    /**
     * If a return type is specified, returns it.
     * 
     * @return string
     */
    public function getReturnType();
}
