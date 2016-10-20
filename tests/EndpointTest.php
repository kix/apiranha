<?php

namespace Tests\Kix\Apiranha;

use Kix\Apiranha\HttpAdapter\HttpAdapterInterface;
use Kix\Apiranha\Router;
use Kix\Apiranha\Tests\Mocks\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Kix\Apiranha\Endpoint;
use Kix\Apiranha\ParameterDefinition;
use Kix\Apiranha\ResourceDefinition;
use Kix\Apiranha\ResourceDefinitionInterface;

/**
 * Class EndpointTest
 */
class EndpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_is_initializable()
    {
        $endpoint = new Endpoint('http://localhost:8000', $this->getMock(EventDispatcherInterface::class));

        static::assertInstanceOf(Endpoint::class, $endpoint);
    }

    /**
     * @test
     */
    public function it_adds_endpoint_definitions()
    {
        $endpoint = new Endpoint('http://localhost:8000', $this->getMock(EventDispatcherInterface::class));

        $endpoint->addResourceDefinition(new ResourceDefinition(
            'getUser',
            ResourceDefinitionInterface::METHOD_GET,
            '/api/users/{id}',
            User::class,
            [
                new ParameterDefinition(
                    'id',
                    'integer',
                    true
                )
            ]
        ));

        
    }
}
