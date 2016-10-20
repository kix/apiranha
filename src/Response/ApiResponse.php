<?php

namespace Kix\Apiranha\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class ApiResponse
 */
class ApiResponse
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
}
