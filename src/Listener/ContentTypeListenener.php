<?php

namespace Kix\Apiranha\Listener;

use Kix\Apiranha\Response\ApiResponse;
use Kix\Apiranha\Serializer\SerializerAdapterInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ContentTypeListenener
 */
class ContentTypeListenener implements AfterResponseListenerInterface
{
    /**
     * @var SerializerAdapterInterface
     */
    private $serializer;

    /**
     * ContentTypeListenener constructor.
     *
     * @param SerializerAdapterInterface $serializer
     */
    public function __construct(SerializerAdapterInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @return ApiResponse
     */
    public function process(RequestInterface $request, ResponseInterface $response)
    {
        return new ApiResponse(
            $response,
            $this->serializer->decode($response->getHeader('Content-type'), $response->getBody())
        );
    }
}
