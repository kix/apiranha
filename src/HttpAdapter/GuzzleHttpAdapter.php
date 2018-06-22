<?php

namespace Kix\Apiranha\HttpAdapter;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class GuzzleHttpAdapter
 */
class GuzzleHttpAdapter implements HttpAdapterInterface
{
    private $client;

    /**
     * GuzzleHttpAdapter constructor.
     *
     * @param $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param RequestInterface $request
     * @throws GuzzleException
     * @return ResponseInterface
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        return $this->client->send($request);
    }
}
