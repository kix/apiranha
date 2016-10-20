<?php

namespace Kix\Apiranha\Request;

use Symfony\Component\EventDispatcher\Event;
use Kix\Apiranha\Endpoint;
use Kix\Apiranha\Resource\ParameterHydrator;
use Kix\Apiranha\ResourceDefinitionInterface;

/**
 * Class representing an API request.
 */
class Request
{
    /**
     * @var Endpoint
     */
    private $endpoint;
    
    /**
     * @var ResourceDefinitionInterface
     */
    private $resource;

    /**
     * @var array
     */
    private $parameters;

    /**
     * Request constructor.
     *
     * @param ResourceDefinitionInterface $resource
     * @param array                       $parameters
     */
    public function __construct(Endpoint $endpoint, ResourceDefinitionInterface $resource, array $parameters)
    {
        $this->endpoint = $endpoint;
        $this->resource = $resource;
        $this->parameters = ParameterHydrator::hydrateParameters($resource->getParameters(), $parameters);
    }

    /**
     * @return Endpoint
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
    
    /**
     * @return ResourceDefinitionInterface
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
