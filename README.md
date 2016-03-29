[![Travis Ci](https://travis-ci.org/Fakerino/Fakerino.svg?branch=master)](https://travis-ci.org/Fakerino/Fakerino)
[![Code Climate](https://codeclimate.com/github/kix/apiranha/badges/gpa.svg)](https://codeclimate.com/github/kix/apiranha)

# Resource definitions

For each resource, you need to create a `ResourceDefinition`:

```php
$resourceDefn = new ResourceDefinition(
    $name = 'listUser',
    $method = 'GET',
    $path = '/users',
    $returnType = null,
    $parameterDefinitions = []
); 
```

An endpoint is composed of resource definitions:

```php
$endpoint = new Endpoint('http://base.url');
$endpoint->add(new ResourceDefinition(
   $name = 'listUsers',
   $method = 'GET',
   $path = '/users',
   $returnType = null,
   $parameterDefinitions = []
));
```

The resource's `$name` will we exposed as a method on the endpoint:

```php
$endpoint->listUsers(); // [User, User, User]
```

For parametrized paths, you should also define parameters for the resource. Given an API that returns a list of user 1's
bookmarks on `/users/1/bookmarks`, you have to put a placeholder on the path and add a corresponding parameter definition:

```php
$resourceDefn = new ResourceDefinition(
    'getBookmarks',
    'GET',
    '/users/{userId}/bookmarks',
    null,
    [
        new ParameterDefinition(
            $name = 'userId',
            $type = 'string',
            $required = true
        ),
    ]
);
```

This way, you will be able to pass the parameters to the endpoint method:

```php
$endpoint->add($resourceDefn);
$endpoint->getBookmarks(18); // to get user 18's bookmarks (/users/18/bookmarks)
```

You can also map objects to route parameters. To do this, you must specify the required type in the parameter definition:

```php
// Our imaginary User class:
class User {
    public function getId()
    {
        return 2; // chosen by a fair dice roll
    }
}

// Our resource definition
$resourceDefn = new ResourceDefinition(
    'getBookmarks',
    'GET',
    '/users/{user.id}/bookmarks',
    null,
    [
        new ParameterDefinition(
            $name = 'user',
            $type = User::class,
            $required = true
        ),
    ]
);
```

This way, you will be able to pass a `User` to the endpoint method:

```php
$endpoint->add($resourceDefn);
$endpoint->getBookmarks(new User()); // This will fetch the passed user's ID and generate `/users/2/bookmarks`
```

However, at some point manually creating resource definitions might get tedious, especially if your API has many 
resources. 


