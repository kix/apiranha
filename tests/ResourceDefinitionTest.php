<?php

namespace Tests\Kix\Apiranha;

use Kix\Apiranha\Exception\InvalidArgumentException;
use Kix\Apiranha\ParameterDefinition;
use Kix\Apiranha\ResourceDefinition;
use Kix\Apiranha\ResourceDefinitionInterface;
use Kix\Apiranha\Tests\Mocks\User;

/**
 * @covers \Kix\Apiranha\ResourceDefinition  
 */
class ResourceDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_is_initializable()
    {
        $defn = new ResourceDefinition(
            'listUsers',
            ResourceDefinitionInterface::METHOD_GET,
            '/users',
            User::class
        );
    }

    /**
     * @test
     * @expectedException \Kix\Apiranha\Exception\InvalidArgumentException
     */
    public function it_throws_for_nonexistent_classes_as_return_type()
    {
        $defn = new ResourceDefinition(
            'listUsers',
            ResourceDefinitionInterface::METHOD_GET,
            '/users',
            '\Gibberish\NonExistent\Class'
        );
    }

    /**
     * @test
     * @expectedException \Kix\Apiranha\Exception\InvalidArgumentException
     */
    public function it_throws_for_non_supported_methods()
    {
        $defn = new ResourceDefinition(
            'listUsers',
            'OPTIONS',
            '/users',
            User::class
        );
    }

    /**
     * @test
     */
    public function it_processes_parameters()
    {
        $processedParams = ResourceDefinition::processParameters(
            '/hello/{name}/{user.id}',
            [
                new ParameterDefinition('name', 'string'),
                new ParameterDefinition('user', User::class),
            ]
        );

        static::assertCount(2, $processedParams);
        static::assertArrayHasKey('name', $processedParams);
        static::assertArrayHasKey('user', $processedParams);
    }

    /**
     * @test
     */
    public function it_checks_roots()
    {
        $processedParams = ResourceDefinition::processParameters(
            '/hello/{user.username}/{user.id}',
            [
                new ParameterDefinition('user', User::class),
            ]
        );

        static::assertCount(1, $processedParams);
        static::assertArrayHasKey('user', $processedParams);
        static::assertEquals('user', $processedParams['user']->getName());
        static::assertEquals(User::class, $processedParams['user']->getType());
    }

    /**
     * @test
     */
    public function it_throws_for_non_matching_root_count()
    {

    }

    /**
     * 
     */
    public function it_throws_when_not_all_placeholders_are_covered()
    {
        
    }
}
