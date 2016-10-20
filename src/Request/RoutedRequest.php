<?php

namespace Kix\Apiranha\Request;

/**
 * Class RoutedRequest
 */
class RoutedRequest
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $url;

    /**
     * ConcreteRequest constructor.
     *
     * @param Request $request
     * @param string $url
     */
    public function __construct(Request $request, $url)
    {
        $this->request = $request;
        $this->url = $url;
    }
    
    public function getMethod()
    {
        return $this->request->getResource()->getMethod();
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
