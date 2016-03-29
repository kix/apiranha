<?php

namespace Kix\Apiranha\Listener;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A listener is able to hook up into different aspects of Apiranha's work. 
 */
interface ListenerInterface
{
    /**
     * A listener should accept a request and a response. Instead of modifying the original request's state, listeners
     * should return new <code>RequestInterface</code> instances.
     * 
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, ResponseInterface $response);
}
