# Middleware
## What is middleware
Middleware is everything, what provides access to system and external resources (databases, cache systems, external API, other applications, ...). Middleware works as glue between your application and other systems or applications.

## How it works

In our case, middleware instances are stored in IoC container and this container is accessible from our app instance. This container is injected into application in app bootstrap and is accessible in every part of code via call:

```php
$middlewareContainer = main()->middleware;
```

Every middleware has unique name in this container. It works as nammed signgleton instance, so middleware instances can be also instances of the same type but with different configurations (e.g. MySQL instances with different connections) and every configuration has unique name.

Middleware instance is accessible in every part of code via call:

```php
// get middleware instance into $driver when instance has name 'mysql'
$driver = main()->middleware->mysql;
// other examples by middleware interface:
$driverName = main()->middleware->mysql->name();
$driverConfiguration = main()->middleware->mysql->cfg();
```

Middleware classes can be also proxy instances to other vendor package(s) or to other classes. So we can use some functionality as a middleware, when we make a class, which implements `ha\Middleware\Middleware` or extends `ha\Middleware\MiddlewareDefaultAbstract`. This is useful for lazy initializon of some drivers or functionality (create connection only in real usage, etc.). 

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

## How to add middleware instance to app

Middleware is automatically initialized by configuration from config file. See next chapter *Middleware configuration* for details. Another, but highly not recommended way is:

```php
main()->middleware->myMiddleware = new MyMiddleware();
```

## Middleware configuration

Middleware configuration is stored in config file in `$cfg['middleware']` array. Every item is array, which has two items: *(string) className* and *(array) configuration*. Key `name` must be unique in configruartion, this defines unique name of instance in middleware IoC container. Framework automatically creates instances from this configuration and adds theirs to IoC middleware container in app bootstrap. 
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
            'name' => 'SQL001', // required for app
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

## How to make real singleton from middleware class

Middleware instances are by default not singletons, only container can not have more instances with the same name. Real singleton functionality provides trait `ha\Middleware\MiddlewareSingletonTrait`, so we need use this trait in class and constructor must call method `$this->denyMultipleInstances()`. Constructor is required for framework functionality, so we could not use protected constructor. Magic singleton methods are not supported yet (`__sleep`, `__wake_up`).

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