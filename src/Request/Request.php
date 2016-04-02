<?php

namespace Kix\Apiranha\Request;

use \GuzzleHttp\Psr7\Request as GuzzleRequest;
use Kix\Apiranha\ResourceDefinitionInterface;

/**
 * Class representing an API request.
 * 
 * This is merely a GuzzleRequest wrapper that translates RESTful methods (that include CGET, for instance) into HTTP
 * methods.
 */
class Request extends GuzzleRequest
{
    /**
     * Resource methods map to HTTP methods so that <code>CGET</code> is transformed to <code>GET</code>. All other
     * methods map one-to-one.
     */
    private static $methodMap = [
        ResourceDefinitionInterface::METHOD_CGET => 'GET',
        ResourceDefinitionInterface::METHOD_DELETE => 'DELETE',
        ResourceDefinitionInterface::METHOD_GET => 'GET',
        ResourceDefinitionInterface::METHOD_POST => 'POST',
        ResourceDefinitionInterface::METHOD_PUT => 'PUT',
    ];

    /**
     * Request constructor.
     *
     * @param null|string $method
     * @param null|\Psr\Http\Message\UriInterface|string $uri
     * @param array $headers
     * @param null $body
     * @param string $protocolVersion
     */
    public function __construct($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1')
    {
        parent::__construct(self::$methodMap[$method], $uri, $headers, $body, $protocolVersion);
    }
}
