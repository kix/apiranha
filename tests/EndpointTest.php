<?php

namespace Tests\Kix\Apiranha;

use Kix\Apiranha\HttpAdapter\HttpAdapterInterface;
use Kix\Apiranha\Router;
use Kix\Apiranha\Tests\Mocks\User;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Kix\Apiranha\Endpoint;
use Kix\Apiranha\ParameterDefinition;
use Kix\Apiranha\ResourceDefinition;
use Kix\Apiranha\ResourceDefinitionInterface;

/**
 * Class EndpointTest
 */
class EndpointTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_is_initializable()
    {
        $adapter = $this->createMock(HttpAdapterInterface::class);
        $endpoint = new Endpoint($adapter, new Router(), 'http://localhost:8000');

        static::assertInstanceOf(Endpoint::class, $endpoint);
    }
}
