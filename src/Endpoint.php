<?php

namespace Kix\Apiranha;

use Kix\Apiranha\Exception\LogicException;
use Kix\Apiranha\Listener\AfterDataListener;
use Kix\Apiranha\Request\Request;
use Kix\Apiranha\Listener\AfterResponseListener;
use GuzzleHttp\Client;
use Kix\Apiranha\Exception\UndefinedResourceException;
use GeneratedHydrator\Configuration;
use Kix\Apiranha\Resource\ParameterHydrator;

/**
 * Represents an API endpoint.
 *
 * <code>Endpoint</code> is the most important class in Apiranha. It registers the resources and it dispatches all the
 * API calls to the event dispatcher. 
 */
class Endpoint
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var ResourceDefinitionInterface[]
     */
    private $resources;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var
     */
    private $client;

    /**
     * <code>before_request</code> listeners can be used to populate a <code>Request</code> with headers or modified
     * parameters. Can be used for authentication or for passing headers.
     */
    const LISTENER_BEFORE_REQUEST = 'before_request';

    /**
     * <code>after_response</code> listeners receive a request and a response, so these can be used to process response
     * data depending on response headers of the kind of the request.
     */
    const LISTENER_AFTER_RESPONSE = 'after_response';

    /**
     * <code>after_data</code> listeners receive decoded (deserialized) response data.
     */
    const LISTENER_AFTER_DATA = 'after_data';
    
    private $listeners = [
        self::LISTENER_BEFORE_REQUEST => [],
        self::LISTENER_AFTER_RESPONSE => [],
        self::LISTENER_AFTER_DATA => [],
    ];

    /**
     * Create the endpoint for the given base URL.
     *
     * For example:
     * <pre>
     * $endpoint = new Endpoint('http://api.twitter.com');
     * </pre>
     *
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->router = new Router();
        $this->resources = [];
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'http_errors' => false,
        ]);
    }

    /**
     * Register a resource definition for this endpoint.
     *
     * @throws LogicException when a resource name was already used.
     * @param ResourceDefinitionInterface $resource
     */
    public function addResourceDefinition(ResourceDefinitionInterface $resource)
    {
        if (array_key_exists($resource->getName(), $this->resources)) {
            throw new LogicException(sprintf(
                'Cannot register a resource named `%s` twice.',
                $resource->getName()
            ));
        }

        $this->resources[$resource->getName()] = $resource;
    }

    /**
     * @TODO: Remove this in favour of a regular event listener
     *
     * @param string $type
     * @param callable $listener
     */
    public function addListener($type, $listener)
    {
        $this->listeners[$type] []= $listener;
    }

    /**
     * Call the given endpoint, transparently passing the call parameters to it.
     *
     * @param string $name
     * @param array $arguments
     * @throws UndefinedResourceException when the method called is not represented by a resource
     *
     * @return array|object
     */
    public function __call($name, $arguments)
    {
        if (!array_key_exists($name, $this->resources)) {
            throw UndefinedResourceException::create($name, array_keys($this->resources));
        }
        
        $resource = $this->resources[$name];
        $uri = $this->router->generate($resource, ParameterHydrator::hydrateParameters($resource->getParameters(), $arguments));

        $request = new Request($resource->getMethod(), $uri);
        
        foreach ($this->listeners[self::LISTENER_BEFORE_REQUEST] as $listener) {
            /** @var callable $listener */
            if (is_callable($listener)) {
                $request = $listener($request);
            } 
        }

        $response = $this->client->send($request);

        foreach ($this->listeners[self::LISTENER_AFTER_RESPONSE] as $listener) {
            if (is_callable($listener)) {
                $result = $listener($request, $response);
            } elseif ($listener instanceof AfterResponseListener) {
                $result = $listener->process($request, $response);
            }
            
            if ($result) {
                $response = $result;
            }
        }

        foreach ($this->listeners[self::LISTENER_AFTER_DATA] as $listener) {
            if (is_callable($listener)) {
                return $listener($response, $resource);
            } elseif ($listener instanceof AfterDataListener) {
                return $listener->process($response, $resource);
            }
        }

        throw new \RuntimeException('No hydrators :(');
    }

}
