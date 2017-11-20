# Middleware

## What is middleware in *ha* framework

Middleware is everything, what provides access to system and external resources (databases, cache systems, external API, other applications, ...). Middleware works as glue between our application and other systems or applications. Middleware class in *ha* framework is class, which provides access to external functionality via methods writted by *proxy* or *facade* design pattern (only simple wrapper). 

Middleware in general is some service in SOA architecture and is independent from application bussines logic. It provides some univerzal functionality based on some system, e.g. provides CRUD operations for concrete datasource, provides templates rendering, ... So middleware does something with data without dependency on these data.

**Middleware examples in general:**
- database driver
- image resampler
- file storage driver
- template driver (system for rendering templates)

**Middleware examples:**
- MySQL driver
- PDO driver
- elasticsearch API
- Twig template engine
- cache driver (Memcached, Redis, ...)
- AWS S3 PHP API

## What is not middleware

All functionality, which contains: bussines logic, concrete implemntations of application services, concrete IO services, models, controllers, ...

We can use only drivers (and packages/classes similary to driver) as a middleware. When service works with concrete data (save products, delete categories), this service is not middleware. But this service is a middleware, when works with universal functionality (insert some row, delete some row). Please see [modules docs](modules.md) for better understanding.


## How it works

In our case, middleware instances are stored in IoC container and this container is accessible from our app instance. This container is injected into application in app bootstrap and is accessible in every part of code by calling:

```php
$middlewareContainer = main()->middleware;
```

When IoC container is created, middleware configuration is readed and middleware instances are initialized by this configuration. Instances then are injected into prepared IoC container.

Every middleware has unique name in this container. This principe works like as singleton. Other instance with this name can not be injected into IoC container and middleware container is unique in our app. Middleware instances can be also instances of the same type but with different configuration (e.g. MySQL instances with different connections) and every configuration has unique name. So this works as pseudo singleton, but checked is name in container and not instance type. Middleware instances outside IoC container are wrong.

Middleware instance is accessible in every part of code by calling:

```php
// get middleware instance into $driver when instance has name 'mysql'
$driver = main()->middleware->mysql;

// other examples by middleware interface:
$driverName = main()->middleware->mysql->name(); // returns unique name in IoC container
$driverConfiguration = main()->middleware->mysql->cfg(); // returns a configuration injected to constructor

// how to call custom methods
$result = main()->middleware->mysql->doSomething(); // we can define custom method(s) in our middleware, e.g. doSomething()
```

We can wrap to middleware some drivers, vendor packages or other classes. We do it by *proxy* or *facade* design pattern. Our class must only implementing `ha\Middleware\Middleware` or must be extended from `ha\Middleware\MiddlewareDefaultAbstract`. We can use also lazy initializon principe for initializing drivers or other classes (create instance and e.g. connect to DB only on first call). 

```php
interface Middleware
{

    /**
     * Module constructor.
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

## Why is middleware required?

Middleware flow in ha framework provides:
- app provides central and unified access to middleware instances (deep dependency injection is not required), middleware is accessible from all parts of code via `main()->middleware->myMiddleware`
- automatic initialization by configuration in app bootstrap
- lazy initialization for many drivers
- lazy connections to databases or datasources

## How to add middleware instance to app

Middleware is automatically initialized from configuration stored in config file. See next chapter *Middleware configuration* for details. This code not works (IoC container is locked after initialization):

```php
main()->middleware->myMiddleware = new MyMiddleware(); // error, IoC container is locked!
```

## Middleware configuration

Middleware configuration is stored in config file in `$cfg['middleware']` array. Every item is array, which has two items: *(string) className* and *(array) configuration*. Key `name` must be unique in configruartion, this defines unique name of instance in middleware IoC container. Framework automatically creates instances from this configuration and adds theirs to IoC middleware container in app bootstrap. IoC container is locked after this process and access to items is read only.
So `name` key is always required in configuration, other keys are specific by middleware (in our example `host`, `user`, `password`, ...) and they defines real configuration data.

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

This example shows, how we can have multiple instances of the same class, which are defined as singleton like instances (instance name defines the singleton).


## How to create new middleware

Our class must implements `ha\Middleware\Middleware` or must be extended from `ha\Middleware\MiddlewareDefaultAbstract` (this abstract class has default implementation of middleware interface). Abstract is useful, when we can use normal functionality. If we need e.g. special initializon in constructor, we can implement an interface.


```php
class MyMiddleware extends MiddlewareDefaultAbstract
{
}
```
Next we can add lazy initialization of driver or some class instance.

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
Now we can add facade method for accessing some functionality.

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

Middleware instances are by default not singletons, only container can not have more instances with the same name. Real singleton functionality provides trait `ha\Middleware\MiddlewareSingletonTrait`, so we need use this trait in class and constructor must call method `$this->denyMultipleInstances()`. Constructor is required for framework functionality, so we could not use protected constructor. Magic singleton methods are not supported yet (`__sleep()`, `__wake_up()`). This functionality only refuses multiple constructor calls of the same class in the system.

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