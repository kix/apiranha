<?php

namespace Kix\Apiranha\Listener;

use Kix\Apiranha\Response\ApiResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ContentTypeListenener
 */
class ContentTypeListenener implements ListenerInterface
{
    /**
     * @var Serializer
     */
    private $serializer;
    
    private static $typeMap = [
        'application/json' => 'json'
    ];
    
    public function __construct()
    {
        $this->serializer = new Serializer([], [
            new JsonEncoder()
        ]);
    }

    public function process(RequestInterface $request, ResponseInterface $response)
    {
        $format = self::$typeMap[implode('', $response->getHeader('Content-type'))];
        
        return new ApiResponse(
            $response,
            $this->serializer->decode($response->getBody(), $format)
        );
    }
}
