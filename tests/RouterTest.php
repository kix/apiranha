<?php

namespace Tests\Kix\Apiranha;

use Kix\Apiranha\ParameterDefinition;
use Kix\Apiranha\ResourceDefinition;
use Kix\Apiranha\Router;

/**
 * @covers \Kix\Apiranha\Router
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_routes()
    {
        $resourceDefn = new ResourceDefinition(
            'listUsers',
            'CGET',
            '/users'
        );

        $router = new Router();
        static::assertEquals('/users', $router->generate($resourceDefn));
    }

    /**
     * @test
     */
    public function it_adds_extra_parameters_to_request_uri()
    {
        $resourceDefn = new ResourceDefinition(
            'getUser',
            'GET',
            '/users/{id}',
            null,
            [
                new ParameterDefinition(
                    'id',
                    'string',
                    true
                ),
                new ParameterDefinition(
                    'locale',
                    'string',
                    false
                )
            ]
        );

        $router = new Router();
        static::assertEquals('/users/1?locale=en', $router->generate($resourceDefn, ['id' => 1, 'locale' => 'en']));
    }

    /**
     * @test
     * @dataProvider provideUserPropIdUrls
     */
    public function it_populates_multiple_parameters($userId, $propId, $expectedUrl)
    {
        $this->markTestIncomplete();
        $resourceDefn = new ResourceDefinition(
            $name = 'getUserProp',
            $method = 'GET',
            $path = '/users/{id}/props/{propId}',
            $returnType = null,
            $parameterDefinitions = [
                new ParameterDefinition(
                    'id',
                    'string',
                    true
                ),
                new ParameterDefinition(
                    'propId',
                    'string',
                    true
                )
            ]
        );

        $router = new Router();
        
        static::assertEquals(
            $expectedUrl,
            $router->generate($resourceDefn, [
                'id' => $userId,
                'propId' => $propId,
            ])
        );
    }

    public function provideUserPropIdUrls()
    {
        return [
            [1, 2, '/users/1/props/2'],
            ['f6eded06', '130e2008', '/users/f6eded06/props/130e2008'],
        ];
    }
}
