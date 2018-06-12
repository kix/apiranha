<?php

namespace tests\Kix\Apiranha\Definition\Driver;

use Kix\Apiranha\Definition\Driver\AnnotationDriver;
use Kix\Apiranha\Exception\InvalidResourceDefinitionException;
use Kix\Apiranha\ResourceDefinition;
use Kix\Apiranha\Tests\Mocks\Resources\BrokenResource;
use Kix\Apiranha\Tests\Mocks\Resources\OfferResource;
use Kix\Apiranha\Tests\Mocks\Resources\UserResource;
use Kix\Apiranha\Tests\Mocks\User;

/**
 * Class AnnotationDriverTest
 */
class AnnotationDriverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     * @return ResourceDefinition[]
     */
    public function it_parses_annotations()
    {
        $driver = new AnnotationDriver();
        $definitions = $driver->createDefinitions(UserResource::class);

        foreach ($definitions as $definition) {
            static::assertInstanceOf(ResourceDefinition::class, $definition);
        }

        return $definitions;
    }

    /**
     * @test
     * @param ResourceDefinition[] $definitions
     * @depends it_parses_annotations
     */
    public function it_parses_plain_resource_defs($definitions)
    {
        $actionDefn = $definitions['listAction'];

        static::assertEquals('listAction', $actionDefn->getName());
        static::assertEquals('CGET', $actionDefn->getMethod());
        static::assertEquals('/users', $actionDefn->getPath());
        static::assertEmpty($actionDefn->getParameters());
        static::assertEquals(User::class, $actionDefn->getReturnType());
    }

    /**
     * @test
     * @param ResourceDefinition[] $definitions
     * @depends it_parses_annotations
     */
    public function it_parses_parametrized_resource_defs($definitions)
    {
        $actionDefn = $definitions['showAction'];

        static::assertEquals('showAction', $actionDefn->getName());
        static::assertEquals('GET', $actionDefn->getMethod());
        static::assertEquals('/users/{id}', $actionDefn->getPath());
        static::assertCount(1, $actionDefn->getParameters());
        // TODO: More asserts!
        static::assertEquals(User::class, $actionDefn->getReturnType());
    }

    /**
     * @test
     * @param ResourceDefinition[] $definitions
     * @depends it_parses_annotations
     */
    public function it_parses_typehinted_parametrized_resource_defs($definitions)
    {
        $actionDefn = $definitions['postAction'];

        static::assertEquals('postAction', $actionDefn->getName());
        static::assertEquals('POST', $actionDefn->getMethod());
        static::assertEquals('/users', $actionDefn->getPath());
        static::assertCount(1, $actionDefn->getParameters());
        // TODO: More asserts!
    }

    /**
     * @test
     * @expectedException \Kix\Apiranha\Exception\InvalidResourceDefinitionException
     */
    public function it_throws_for_bad_parameter_property_paths()
    {
        $driver = new AnnotationDriver();
        $definitions = $driver->createDefinitions(BrokenResource::class);
    }
    
    /**
     * @test
     */
    public function it_fetches_return_types_from_typehints()
    {
        if (PHP_VERSION_ID < 70000) {
            static::markTestSkipped('Return typehints not supported before PHP 7.0.0');
        }

        $driver = new AnnotationDriver();

        
    }
}
