<?php

namespace Tests\Kix\Apiranha;

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
class EndpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_is_initializable()
    {
        $endpoint = new Endpoint('http://localhost:8000');
        
    }
}
