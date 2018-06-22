# Apiranha

Apiranha is a library that makes consuming APIs easier and faster. Some of the inspiration for Apiranha comes from [Retrofit](square.github.io/retrofit/)

[![Travis Ci](https://travis-ci.org/kix/apiranha.svg?branch=master)](https://travis-ci.org/kix/apiranha)
[![Code Climate](https://codeclimate.com/github/kix/apiranha/badges/gpa.svg)](https://codeclimate.com/github/kix/apiranha)
[![Coverage Status](https://coveralls.io/repos/github/kix/apiranha/badge.svg?branch=master)](https://coveralls.io/github/kix/apiranha?branch=master)

## Example usage

A complete example can be found at `examples/`. See [`ExampleCommand`](https://github.com/kix/apiranha/blob/master/examples/Command/ExampleCommand.php) and [`BuilderExampleCommand`](https://github.com/kix/apiranha/blob/master/examples/Command/BuilderExampleCommand.php).

## Quick start

Make sure you have Composer installed, then run this in your project folder:

```
composer require kix/apiranha='dev-master'
```

Also, you'll need to require `guzzlehttp/guzzle` if you don't want to bring your own implementation of an HTTP client.

In order to consume [Github API](https://developer.github.com/v3/), for example, first you need to declare your endpoints 
in an interface and annotate those. The annotations available under the `Kix\Apiranha\Annotation` namespace are:

- HTTP/REST method annotations:
  - `Get`
  - `CGet`
  - `Delete`
  - `Post`
  - `Put`
- `Returns`, a special annotation to denote the endpoint return type.

First, you need to decide which data you actually need from the endpoint. In our case, we're just getting the ID, name,
language and stargazers count from the Github REST API: 

```php
namespace \Kix\Apiranha\Examples\Model;

class Repository
{
    private $id;
    private $name;
    private $language;
    private $stargazersCount;
}
```

Of course, it will be necessary for you to read the properties, so you could either declare the fields as `public`, or
you could add getters for the fields.

Next, to enable getting a single repo from the Github API, you would declare an interface like this (note the 
annotations):

```php
use Kix\Apiranha\Annotation as Rest;

interface GithubApi
{
    /**
     * @Rest\Returns("\Kix\Apiranha\Examples\Model\Repository")
     * @Rest\Get("/repos/{username}/{repo}")
     */
    public function getRepo(string $username, string $repo);
}
```

Here, we say that the `getRepo` method will be returning data from the [`/repos` endpoint](https://developer.github.com/v3/repos/#list-user-repositories). 
Note that the URL parameters are consistently named and also typehinted with `string`: this will help us generate a 
correct URL for a specific call.

After you've implemented your endpoint interface and your model, it's time for the magic to happen:
```php
use Kix\Apiranha\Builder;
use Kix\Apiranha\Examples\Definition\GithubApi;

/** @var $endpoint GithubApi */
$endpoint = Builder::createEndpoint('http://api.github.com', [GithubApi::class]);
```

Calling `Builder::createEndpoint`, you get back an object that represents your API in terms of the interface you have
declared previously. Which, in turn, means you can annotate it with `@var $endpoint GithubApi`.

Since now, all the methods you have declared in the interface become available via the endpoint object. Here's how it
looks:

```php
 > $endpoint->listRepos('kix');

=> array(30) {
=>   object(Kix\Apiranha\Examples\Model\Repository)#76 (4) {
=>     ["id":"Kix\Apiranha\Examples\Model\Repository":private]=>
=>     int(43456580)
=>     ["name":"Kix\Apiranha\Examples\Model\Repository":private]=>
=>     string(6) "apiranha"
=>     ["language":"Kix\Apiranha\Examples\Model\Repository":private]=>
=>     NULL
=>     ["stargazersCount":"Kix\Apiranha\Examples\Model\Repository":private]=>
=>     int(1)
=>   }
=>   ...
=> }
```

# Digging deeper

Well, what happens under the hood? The `Builder` you've used in this simple API client is just a facade that helps you do
stuff quick, and hides a lot of implementation details you might not actually care about. However, there's more to it
than just calling a method and getting a result back. Here's what the builder presumes:

- Your routes will always be declared in the annotations you provide, and the parameters will be bound to the URIs as-is;
- You will be using Guzzle as the HTTP client (which this package will suggest upon installation)
- The API serialization format will be JSON, handled by Symfony's serializer
- Model instances your API will return will be hydrated using PHP's reflection API (which could be costly) 

If some of these statements do not comply with your requirements, you're always free to extend the logic behind Apiranha.

## Builder under the hood

Let's take a look at `Builder`'s insides. Here's what the `createEndpoint` facade looks like:

```php
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
```

Note that you still can pass an array of listeners, an HTTP adapter and a router as arguments to this factory method. 
However,  if you want to do things manually, or you dislike when things are decided for you (or, you just hate static 
facades), you could always reimplement this logic manually. That's all there is, basically. 

Now, to the specifics. Why do we need all these things the `Builder` instantiates for us?

## HTTP layer

First of all, in order to consume an API, you need to somehow interact with the 3rd party server. For this, you need an
HTTP adapter. It should implement `Kix\Apiranha\HttpAdapter\HttpAdapterInterface`, and `GuzzleHttpAdapter` is an example
you can use right now.

The only method you have to implement is `send(RequestInterface $request): ResponseInterface`, where `RequestInterface` and
`ResponseInterface` are standard `Psr\Http` messages.

## Resource definitions

When processing your annotated interface you've created before, the endpoint registers it as a resource. A resource is
an instance of `Kix\Apiranha\ResourceDefinitionInterface`, which basically contains all the data necessary to make an
HTTP request to an API. You might think of it like a Swagger definition.

## Router

A router is responsible for generating concrete URIs when given a resource and an array of parameters the request is 
executed with. The built-in router allows you to pass extra parameters you have not explicitly declared in a resource's
path. Those will be added as query parameters.

## Listeners

You can attach your own or built-in listeners to the lifecycle, which consists of three events:

- `Endpoint::LISTENER_BEFORE_REQUEST`, used to modify the request before it has been sent,
- `Endpoint::LISTENER_AFTER_RESPONSE`, which is used to attach serializers to the workflow,
- `Endpoint::LISTENER_AFTER_DATA`, which is used to hydrate your model instances.

All of the listeners can be implemented as callables, and 'after response' and 'after data' listeners can also implement
`Kix\Apiranha\Listener\AfterResponseListenerInterface` or `Kix\Apiranha\Listener\AfterDataListenerInterface`.  

### BEFORE_REQUEST listeners

A listener that is attached to the `Endpoint::LISTENER_BEFORE_REQUEST` receives just one argument: the `ResponseInterface`
instance that has been instantiated for the current request. For example, you could use it to pass authorization headers
to a secured API:

```php
$endpoint = Builder::createEndpoint('http://api.github.com', [GithubApi::class]);

$endpoint->addListener(Endpoint::LISTENER_BEFORE_REQUEST, function (RequestInterface $request) {
    return $request->withAddedHeader('Authorization', 'Basic 12345');
});
```

### AFTER_RESPONSE listeners

A listener attached to `Endpoint::LISTENER_AFTER_RESPONSE` could either be a callable that matches the signature of
`function(RequestInterface $request, ResponseInterface $response)` or an instance of 
`Kix\Apiranha\Listener\AfterResponseListenerInterface`. For example, such a listener could be helpful in case when the
HTTP client you are using does not throw for bad HTTP status codes.

Here's `StatusCodeListener`, for example:

```php
class StatusCodeListener implements AfterResponseListenerInterface
{
    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @throws \Exception
     * @return void
     */
    public function process(RequestInterface $request, ResponseInterface $response)
    {
        if ($response->getStatusCode() > 400) {
            throw new \RuntimeException(sprintf(
                'Bad status code: %s',
                $response->getStatusCode()
            ));
        }
    }
}
```

Having the request also available allows you to log precise request/response interactions, for example.

Also note that you're allowed to return an instance of `ApiResponse`, which wraps the PSR response, but also has a `data`
property which you can manipulate.

### AFTER_DATA listeners

A listener attached to `Endpoint::LISTENER_AFTER_DATA` could either be a callable that matches the signature of
`function(ResponseInterface $response, ResourceDefinitionInterface $resource)` or an instance of 
`Kix\Apiranha\Listener\AfterDataListenerInterface`. 

Such listeners are used by the library to hydrate objects with data returned by the API we wrap.
 
#### Hydration

There are several options available for the hydration strategies:

- Hydration via reflection, implemented in `ReflectionHydratorListener`
- Hydration with `ocramius/generated-hydrator`, implemented in `GeneratedHydratorListener`

Reflection hydrator is always available, as long as PHP has reflection APIs available. Generated hydrator is an 
alternative implementation which could be more performant.

Both of these strategies are implemented as listeners and can be attached to the `Endpoint::LISTENER_AFTER_DATA` event:

```php
$endpoint = new Endpoint($adapter, new Router(), 'http://api.github.com');
$endpoint->addListener(Endpoint::LISTENER_AFTER_DATA, new ReflectionHydratorListener());
```
**TODO**: benchmarks

Once the model instance has been hydrated, you get back an instance of the class you've declared as your model. And 
that's it! 

