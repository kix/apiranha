<?php

namespace Kix\Apiranha\HttpAdapter;


use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttplugAdapter
 */
class HttplugAdapter implements HttpAdapterInterface
{
    public function __construct()
    {
    }

    public function send(RequestInterface $request)
    {
        // TODO: Implement send() method.
    }
}
