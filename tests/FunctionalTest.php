<?php

namespace Tests\Kix\Apiranha;

use GeneratedHydrator\Configuration;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Kix\Apiranha\Endpoint;
use Kix\Apiranha\HttpAdapter\GuzzleHttpAdapter;
use Kix\Apiranha\Hydrator\GeneratedHydratorListener;
use Kix\Apiranha\Hydrator\ReflectionHydratorListener;
use Kix\Apiranha\Listener\ContentTypeListenener;
use Kix\Apiranha\Listener\HttpListener;
use Kix\Apiranha\Listener\JwtListener;
use Kix\Apiranha\Listener\RouterListener;
use Kix\Apiranha\Listener\StatusCodeListener;
use Kix\Apiranha\ResourceDefinition;
use Kix\Apiranha\ParameterDefinition;
use Kix\Apiranha\ResourceDefinitionInterface;
use Kix\Apiranha\Response\ApiResponse;
use Kix\Apiranha\Router;
use Kix\Apiranha\Serializer\SymfonySerializerAdapter;
use Kix\Apiranha\Tests\Mocks\Offer;
use Kix\Apiranha\Tests\Mocks\User;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * @coversNothing
 */
class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group functional
     */
    public function test()
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener('before_request', [new JwtListener(), 'onBeforeRequest']);
        $eventDispatcher->addListener('before_request', [new RouterListener(new Router()), 'onBeforeRequest']);

        $eventDispatcher->addListener('send_request', [new HttpListener(new GuzzleHttpAdapter(new Client())), 'onRequest']);

        $eventDispatcher->addListener('after_response', [new StatusCodeListener(), 'onResponse']);

        $serializerAdapter = new SymfonySerializerAdapter(new Serializer([], [new JsonEncoder()]));
        $serializerAdapter->addContentType('application/json', 'json');
        $eventDispatcher->addListener('after_response', [new ContentTypeListenener($serializerAdapter), 'onResponse']);
        $eventDispatcher->addListener('after_data', [new ReflectionHydratorListener(), 'onResponseData']);

        $endpoint = new Endpoint('http://localhost:8000', $eventDispatcher);

        $endpoint->addResourceDefinition(new ResourceDefinition(
            'getUser',
            ResourceDefinitionInterface::METHOD_GET,
            '/api/users/{id}',
            User::class,
            [
                new ParameterDefinition(
                    'id',
                    'string',
                    true
                )
            ]
        ));
        
        $endpoint->addResourceDefinition(new ResourceDefinition(
            'getUserOffers',
            ResourceDefinitionInterface::METHOD_CGET,
            '/api/users/{user.id}/offers',
            Offer::class,
            [
                new ParameterDefinition(
                    'user',
                    User::class,
                    true
                )
            ]
        ));

        $endpoint->addResourceDefinition(new ResourceDefinition(
            'breakStuff',
            ResourceDefinitionInterface::METHOD_CGET,
            '/api/huyapi',
            Offer::class
        ));

        $user = $endpoint->getUser('21e3108d-e041-4581-bf78-32775347ecb3');

        static::assertInstanceOf(User::class, $user);

        $offers = $endpoint->getUserOffers($user);
        foreach ($offers as $offer) {
            static::assertInstanceOf(Offer::class, $offer);
        }

        try {
            $endpoint->breakStuff();
        } catch (\Exception $e) {
            static::assertInstanceOf(\Exception::class, $e);
        }
    }
}
