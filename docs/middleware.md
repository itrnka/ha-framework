![ha framework](docs/img/ha-logo.png "ha framework")

# Middleware in *ha* framework

## What is middleware

Midddleware is anything that allows you to access operating system functionality, external resources (DB, cache, files, external API, logging layer, ...), vendor packages, or what provides some universal functionality (such as template renderer). Middleware acts as a glue between the application and such external functionality.

The middleware implementation is designed to either wrap a driver or has façade methods taught to access external functionality. What is important is that the middleware functionality is not directly dependent on the contextual models from the application logic. And it is also independent of application logic. Middleware solves only access to something or something transforms and so on.

Middleware in general is some service in SOA (service oriented architecture), respectively provides access to such service. Middleware does something with data without dependence on this data.

> Please also read the [modules documentation](modules.md) to understand exactly what is the module and what is the middleware.


**Middleware examples in general:**

- database driver
- image resampler
- file storage driver
- template renderer (system for rendering templates)

**Specific middleware examples:**

- MySQL driver
- PDO driver
- elasticsearch API
- Twig template engine
- cache driver (Memcached, Redis, ...)
- AWS S3 PHP API
- Log4PHP



### Requirements

Middleware class must implement interface [`ha\Middleware\Middleware`](#middleware-interface) or must be extended from an abstract class `ha\Middleware\MiddlewareDefaultAbstract`.
## How it works

When an application is initialized, a IoC container for middleware instances is created. Configuration for middleware is then loaded from the configuration file. Middleware instances are instantiated according to the configuration and the middleware instances created are injected into the ready IoC container. The container is then locked and is read-only. Finally, the container is injected into the application and can be used in any part of the code by the following call:

```php
$middlewareContainer = main()->middleware;
```

Each middleware instance must have a unique name in the IoC container (name is case insensitive). Thus, we can create named instances of the same class but with a different configuration (for example, two MySQL connections). According to this name we then call specific middleware as follows:

```php
// get middleware instance into $driver when instance has name 'mysql'
$driver = main()->middleware->mysql;. 

// other examples by middleware interface:
$driverName = main()->middleware->mysql->name(); // returns unique name in IoC container
$driverConfiguration = main()->middleware->mysql->cfg(); // returns a configuration injected to constructor

// how to call custom methods
$result = main()->middleware->mysql->doSomething(); // we can define custom method(s) in our middleware, e.g. doSomething()
```


## Middleware configuration

The configuration of middleware instances must be stored in the config file in the `$cfg['middleware']` variable. This variable must be an array of particular configurations. Particular configuration is an array consisting of the midddleware class name and an associative array of values that make up a particular configuration for a given class.

This associative array must always have a defined key `name`, this string value specifying the name under which the middleware will be available in the IoC container. Other values are dependent on the middleware's needs and are optional (in our example `host`, `user`, `password`, ...):


```php
// schema:
$cfg['middleware'] = [
    [className1::class, ['name' => 'uniqueName1', ...]],
    [className2::class, ['name' => 'uniqueName2', ...]],
    [className3::class, ['name' => 'uniqueName3', ...]],
];

// example:
$cfg['middleware'] = [
    // MySQLi (connection to local database 'products_db')
    [
        \ha\Middleware\RDBMS\MySQLi\MySQLi::class,
        [
            'name' => 'SQL001', // required for app (key in IoC container)
            'host' => '127.0.0.1', // required for middleware
            'user' => 'root', // required for middleware
            'password' => 'password', // required for middleware
            'database' => 'products_db', // required for middleware
            'port' => null, // required for middleware
            'socket' => null, // required for middleware
        ]
    ],
    // MySQLi (connection to local database 'clients_db')
    [
        \ha\Middleware\RDBMS\MySQLi\MySQLi::class,
        [
            'name' => 'SQL002',
            'host' => '127.0.0.1',
            'user' => 'root',
            'password' => 'password',
            'database' => 'clients_db',
            'port' => null,
            'socket' => null,
        ]
    ],
    // elasticsearch
    [
        ha\Middleware\NoSQL\Elasticsearch\Elasticsearch::class,
        [
            'name' => 'ES001',
            'hosts' => ['127.0.0.1:9200'],
        ]
    ],
];
```

The example shows how we can create two instances of the same middleware with a completely different configuration (`MySQLi::class`).


## How to create new middleware

Middleware class must implement interface [`ha\Middleware\Middleware`](#middleware-interface) or must be extended from an abstract class `ha\Middleware\MiddlewareDefaultAbstract`. The abstract class contains a predefined logic that is sufficient for the vast majority of implementations. If we need a special implementation, we need to implement the interface (for example, if we need something special in the constructor).

```php
class MyMiddleware extends MiddlewareDefaultAbstract
{
}
```
We will add lazy initialization of the driver or some class instance (it might be a class from vendor package):

```php
class MyMiddleware extends MiddlewareDefaultAbstract
{

    private $driver;

    private function driver(): MyDriverInterface
    {
        if (!isset($this->driver)) {
            // we have access to self configuration via: $this->cfg($configurationKey);
            $this->driver = new SomeDriverOrSomeClass($this->cfg('user'), $this->cfg('password'));
        }
        return $this->driver;
    }

}
```

Finally, add public methods to make the selected functionality available outside of the midddleware:

```php
class MyMiddleware extends MiddlewareDefaultAbstract
{

    private $driver;

    private function driver(): MyDriverInterface
    {
        if (!isset($this->driver)) {
            // we have access to self configuration via: $this->cfg($configurationKey);
            $this->driver = new SomeDriverOrSomeClass($this->cfg('user'), $this->cfg('password'));
        }
        return $this->driver;
    }

    public function doSomething(int $someArgument): SomeReturnValue
    {
        return $this->driver()->executeSomething($someArgument);
    }
}

// example call in external code:
$result = main()->middleware->myMiddlewareName->doSomething(45);
```

Note: If we need full access to driver (our middleware is only proxy), `driver()` will be public and facade methods are not required.


## How to make pseudo singleton from middleware class

Middleware instances are by default not singletons, only container can not have more instances with the same name. Real singleton functionality provides trait `ha\Middleware\MiddlewareSingletonTrait`, so we need to use this trait in class and the constructor must call method `$this->denyMultipleInstances()`. The constructor is needed for the functionality of *ha* framework, so we can not use a protected constructor. Magic singleton methods are not supported yet (`__sleep()`, `__wake_up()`). This functionality only refuses multiple constructor calls of the same class in our application.

```php
// example
class MyMiddleware extends MiddlewareDefaultAbstract
{
    use MiddlewareSingletonTrait;

    public function __construct(Configuration $configuration)
    {
        $this->denyMultipleInstances();
        parent::construct($configuration);
    }
    
    // ...
}
```



## Middleware interface

```php
interface Middleware
{

    /**
     * Middleware constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration);

    /**
     * Get value from internal configuration by key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function cfg(string $key);

    /**
     * Get instance name.
     *
     * @return string
     */
    public function name() : string;

}
```