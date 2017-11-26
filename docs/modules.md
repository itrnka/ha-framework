![ha framework](img/ha-logo.png "ha framework")

# Modules in *ha* framework

## What is module

Module is a encapsulated system that provides some complex functionality. So we can use name "logic provider" for this application component. Access to functions can be provided via facade methods, or if you do not want use facade access, module can working as IoC container with services and as a factory for models. Very important functionality of module is hidding of most functionality from public scope. Access can only be public for the necessary functionality and is, by default, completely independent of data sources. The public scope does not need to know what source was used for our activity. We just want to read, write, delete or update some data and the way to do it is irrelevant outside the module.

Another very important module functionality is simple access to the module instance without requring dependency injections. Our application contains an IoC container with our modules and this container is directly accessible from the public scope. So we can call the module directly from any part of our code.

The last important feature of the module is strict separation of data rendering and processing of requests (HTTP requests or console commands).

### Benefits

*ha* framework is mainly designed with this idea and there are some great benefits:

- Each module can be used as a microservice at application level or as API.
- Also our application can be used as a microservice.
- Each module can be completely independent of other modules and can be removed or replaced in the future without large code changes.
- Our application code is independent of data and we can simply add, replace or remove some data sources in a background, changes arise only in the hidden functionality of the module.
- The application can work for many years without changes, changes will only be at middleware level. This is useful for unpredictable future changes: new middleware versions in future, better technologies in future, transformation to cloud technologies in future, etc.
- We can very easly add some subsystems to our application without large code changes, when in our application performance issues will be occured in future.
- Our full application can be used as microservice.

### Module usage
In most cases, modules are used in controllers (HTTP access) or in console commands (shell access), e.g. for reading and writing models. Modules can also be used in other modules, but the use of cross modules is highly not recommended - we want to keep loose connections between the application components. In special cases, modules can also be used in HTTP paths when data from datasources is needed to analyze URLs.


### Simple module schema

- [**application services**](services.md#application-services) - module uses application services for some bussiness logic, it is a provider of bussines logic and application logic 
- [**IO services**](services.md#io-services) - are called from application services for CRUD operations on datasources, so it is a data provider
- **models factory** - provides models creating functionality, so it is a model provider
- [**models**](models.md) - data representation classes

> We can very simply say: module uses application services for some functionality. In application services is implemented bussines logic, ACL, input models validation, ... and anything else that is not dependent on a particular data source. The application service uses IO service(s) to read and write data. IO services use middleware for specific CRUD operations and make conversions between data sources and application models. These models will be built through module factory.

### Differences between module and middleware

The module can use middleware, but modules can not be used in middleware. Module contains logic for specific models, but middleware provides universal functionality (in most cases, this is a query for some CRUD operations). Middleware is not based on any models and provides universal access. Middleware can be used as a strategy, but not a module.

### What the module does not contain

Everything that provides functionality for processing and handling requests, drivers, universal handlers, data renderers, ... it is part of the access methods or middleware. We want to write reusable code and applications independent of the access method and rendering, so modules and middleware must be strictly separate from this logic.

### Requirements

Module must implement interface [`ha\Internal\DefaultClass\Module\Module`](#module-interface) or must be extended from an abstract class `ha\Internal\DefaultClass\Module\ModuleDefaultAbstract`.


### How it works

When an application is initialized, a IoC container for the modules is created. Configuration for modules is then loaded from the configuration file. Modules are instantiated according to the configuration and the modules created are injected into the ready IoC container. The container is then locked and is read-only. Finally, the container is injected into the application and can be used in any part of the code by the following call:

```php
$moduleContainer = main()->module;
```
Each module must have a unique name in the IoC container (name is case insensitive). According to this name we then approach the module as follows: 

```php
// get module instance into $module when instance has name 'myModule'
$module = main()->module->myModule;

// other examples by module interface:
$moduleName = main()->module->myModule->name(); // returns unique name in IoC container
$moduleConfiguration = main()->module->myModule->cfg(); // returns a configuration injected to constructor

// how to call custom methods
$result = main()->module->myModule->doSomething(); // we can define custom method(s) in our module, e.g. doSomething()
```



## Module(s) configuration

The configuration of the modules must be stored in the config file in the `$cfg['modules']` variable. This variable must be an array of particular configurations. Module Configuration is an array consisting of the module class name and an associative array of values that make up a particular configuration for a given class.

This associative array must always have a defined key `name`, this string value specifying the name under which the module will be available in the IoC container. Other values are dependent on the module's needs and are optional (in our example `sqlMiddlewareName`, `cacheTTL`):


```php
// schema:
$cfg['modules'] = [
    [className1::class, ['name' => 'uniqueName1', ...]],
    [className2::class, ['name' => 'uniqueName2', ...]],
    [className3::class, ['name' => 'uniqueName3', ...]],
];

// example:
$cfg['modules'] = [
    // Facade to "article" functionality
    [
        \MyProject\ArticleModule::class,
        [
            'name' => 'article', // required for app (key in IoC container)
            'sqlMiddlewareName' => 'SQL001', // required for module (middleware name for SQL IO operations in this module), this is example only
            'cacheTTL' => 60, // required for module (other example value required in module logic)
        ]
    ],
    // Facade to "language" functionality
    [
        \MyProject\LanguageModule::class,
        [
            'name' => 'language',
        ]
    ],
    // Facade to "users" functionality
    [
        \MyProject\UserModule::class,
        [
            'name' => 'user',
        ]
    ],
];
```

> This example shows how we can have a separate logic for users, articles, and languages (Each logic is a separate module).


## How to create a new module

Module must implement interface [`ha\Internal\DefaultClass\Module\Module`](#module-interface) or must be extended from an abstract class `ha\Internal\DefaultClass\Module\ModuleDefaultAbstract`. The abstract class contains a predefined logic that is sufficient for the vast majority of implementations. If we need a special implementation, we need to implement the interface (for example, if we need something special in the constructor).


```php
class ArticleModule extends ModuleDefaultAbstract
{
}
```

Once the class is created, we create a factory and make it available in the module with the public method  `factory()`:

```php
// create factory
class ArticleModuleFactory
{
    public function createArticle(string $articleType): Article
    {
        switch ($articleType) {
            case Article::ARTCILE_TYPE_BLOG:
                return new BlogArticle();
            case Article::ARTCILE_TYPE_PRODUCT:
                return new ProductArticle();
            default:
                throw new \Error("Invalid article type");
        }
    }

    public function createArticleCategory(): ArticleCategory
    {
        return new ArticleCategory();
    }
}

// create factory getter in module with lazy initialization
class ArticleModule extends ModuleDefaultAbstract
{

    private $factory;

    public function factory(): ArticleModuleFactory
    {
        if (!isset($this->factory)) {
            $this->factory = new ArticleModuleFactory();
        }
        return $this->factory;
    }

}
```

 The factory will be accessible from any part of the application as follows:

```php
$article = main()->module->article->factory()->createArticle(Article::ARTCILE_TYPE_BLOG);
```

Finally, add public methods to make the selected functionality available outside of the module (e.g. application [services](services.md) getters):

```php
class ArticleModule extends ModuleDefaultAbstract
{

    // ...

    private $articleService;

    public function articleService(): ArticleService
    {
        if (!isset($this->articleService)) {
            // service requires module as argument (easy access to module in service)
            $this->articleService = new ArticleAppService($this);
            // we have access to self configuration via: $this->cfg($configurationKey);
	    $this->articleService->setTTL($this->cfg('cacheTTL'));
        }
        return $this->driver;
    }

    // ...
}

// example call in external code:
$article = main()->module->article->articleService()->readArticleById(144587); // if service has method "readArticleById(int $id): Article"
```

And/or add some facades methods (optionaly):

```php
class ArticleModule extends ModuleDefaultAbstract
{

    // ...

    public function invalidateCache(): void
    {
        $this->articleService()->invalidateCache();
        $this->articleCategoryService()->invalidateCache();
        $this->someOtherService()->invalidateCache();
    }

    // ...
}

// example call in external code:
$article = main()->module->article->invalidateCache(); // clear all data cached in module services
```

As we can see, the functionality is nicely encapsulated. Everything else is hidden inside the module, and internal logic is so completely separate from the outside world.



Module methods can be beautifully used to initialize the called instances of the module only when we really need them, in this example e.g. `ArticleModule::factory()` and `ArticleModule::articleService()`. 



## Module interface

```php
interface Module
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

