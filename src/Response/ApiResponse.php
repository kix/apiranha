<?php

namespace Kix\Apiranha\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class ApiResponse
 */
class ApiResponse implements ResponseInterface
{
    private $wrapped;
    
    private $data;

    public function __construct(ResponseInterface $wrapped, $data)
    {
        $this->wrapped = $wrapped;
        $this->data = $data;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->wrapped, $name], $arguments);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function getProtocolVersion()
    {
        return $this->wrapped->getProtocolVersion();
    }

    public function withProtocolVersion($version)
    {
        return $this->wrapped->withProtocolVersion($version);
    }

    public function getHeaders()
    {
        return $this->wrapped->getHeaders();
    }

    public function hasHeader($name)
    {
        return $this->wrapped->hasHeader($name);
    }

    public function getHeader($name)
    {
        return $this->wrapped->getHeader($name);
    }

    public function getHeaderLine($name)
    {
        return $this->wrapped->getHeaderLine($name);
    }

    public function withHeader($name, $value)
    {
        return $this->wrapped->withHeader($name, $value);
    }

    public function withAddedHeader($name, $value)
    {
        return $this->wrapped->withAddedHeader($name, $value);
    }

    public function withoutHeader($name)
    {
        return $this->wrapped->withoutHeader($name);
    }

    public function getBody()
    {
        return $this->wrapped->getBody();
    }

    public function withBody(StreamInterface $body)
    {
        return $this->wrapped->withBody($body);
    }

    public function getStatusCode()
    {
        return $this->wrapped->getStatusCode();
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        return $this->wrapped->withStatus($code, $reasonPhrase);
    }

    public function getReasonPhrase()
    {
        return $this->wrapped->getReasonPhrase();
    }
}
