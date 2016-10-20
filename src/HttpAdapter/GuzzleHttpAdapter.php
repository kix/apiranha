<?php

namespace Kix\Apiranha\HttpAdapter;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class GuzzleHttpAdapter
 */
class GuzzleHttpAdapter implements HttpAdapterInterface
{
    /**
     * @var ClientInterface
     */
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
     * @return ResponseInterface
     */
    public function send(RequestInterface $request)
    {
        return $this->client->send($request);
    }
}
