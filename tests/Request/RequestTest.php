<?php

namespace tests\Kix\Apiranha\Request;

use Kix\Apiranha\Request\Request;
use Kix\Apiranha\ResourceDefinitionInterface;

/**
 * Class RequestTest
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_replaces_cget_with_get()
    {
        $request = new Request(ResourceDefinitionInterface::METHOD_CGET, '/users');

        static::assertEquals('GET', $request->getMethod());
    }

    /**
     * @test
     */
    public function it_passes_get_as_is()
    {
        $request = new Request(ResourceDefinitionInterface::METHOD_GET, '/users/1');

        static::assertEquals('GET', $request->getMethod());
    }
}
