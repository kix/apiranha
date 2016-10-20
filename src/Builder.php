<?php

namespace Kix\Apiranha;

use GuzzleHttp\Client;
use Kix\Apiranha\Definition\Driver\AnnotationDriver;
use Kix\Apiranha\HttpAdapter\GuzzleHttpAdapter;
use Kix\Apiranha\HttpAdapter\HttpAdapterInterface;
use Kix\Apiranha\Hydrator\ReflectionHydratorListener;
use Kix\Apiranha\Listener\ContentTypeListenener;
use Kix\Apiranha\Serializer\SymfonySerializerAdapter;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class Builder
 */
class Builder
{
    /**
     * @param string                    $baseUrl
     * @param array                     $definitions
     * @param array                     $listeners
     * @param HttpAdapterInterface|null $adapter
     * @param Router|null               $router
     * @return self
     */
    public static function createEndpoint($baseUrl, array $definitions, array $listeners = array(), HttpAdapterInterface $adapter = null, Router $router = null)
    {
        if (!$adapter) {
            $adapter = new GuzzleHttpAdapter(new Client());
        }

        if (!$router) {
            $router = new Router();
        }

        if (!count($listeners)) {
            $serializerAdapter = new SymfonySerializerAdapter(
                new Serializer([], [new JsonEncoder()])
            );
            $serializerAdapter->addContentType('application/json', 'json');

            $listeners[Endpoint::LISTENER_AFTER_RESPONSE] = new ContentTypeListenener($serializerAdapter);
            $listeners[Endpoint::LISTENER_AFTER_DATA] = new ReflectionHydratorListener();
        }

        $endpoint = new Endpoint($adapter, $router, $baseUrl);

        foreach ($listeners as $evt => $listener) {
            $endpoint->addListener($evt, $listener);
        }

        $driver = new AnnotationDriver();

        foreach ($definitions as $interfaceName) {
            $resources = $driver->createDefinitions($interfaceName);
            foreach ($resources as $resource) {
                $endpoint->addResourceDefinition($resource);
            }
        }

        return $endpoint;
    }
}
