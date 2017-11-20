# Modules

## What is module in *ha* framework

**We can very simply say: module uses application services to some functionality and application service uses IO services to read/write data. IO services uses middleware to concrete CRUD operations. Service input or service output is representeed as model, models collection or scalar value. Complex of these instances is module, when these instances are in the same logic family. Module makes public access to some these instances. So we can see clear difference between module and middleware. At a first look is it often not clear. So module is set of services and models with public access via facade pattern, but without middleware - midddleware is outside module.**

Module is facade to complex functionality family. Module also encapsulate complete functionality for some complex logic. When we have e.g. articles in system, module can be *ArticleModule* and here would be all about reading and writing articles: models (*Article, Articles, ArticleCategory, ArticleCategories, ...*), application services with bussines logic (e.g. verify ACL or something other and call IO service in background), IO services (transform input params to query and call concrete middleware driver), so full flow about articles. Other module can be e.g. *LanguageModule* and this module will have all about languages (all services and all models).

Module instances can be called from controllers (reading or writing data). When request is mapped via router to concrete route, route will calls controller method an in this method we can use module method to accessing service.

When we have e.g. e-commerce project, in module will be everything, what is required for e-commerce solution (services and models for products, products categories, etc.). 

**Idea:** separate universal logic from access type and rendering. E.g. website uses custom controllers and views, API uses custom controllers and views, mobile page uses custom controllers and views, some Angular app uses custom controllers and views, but logic is the same and is separated and encapsulated into module. This idea is very useful for projects, when we have different access method to the same functionality.

## What is not module

Module is not driver, module does not map request to functionality. Module is complete and specific functionality in some complex flow independent from middleware and access metods.

## How it works

In our case, module instances are stored in IoC container and this container is accessible from our app instance. This container is injected into application in app bootstrap and is accessible in every part of code via call:

```php
$moduleContainer = main()->module;
```

When IoC container is created, modules configuration is readed and by this configuration are initialized module instances. Instances have been injected now into prepared IoC container.

Every module has unique name in this container. It works as nammed signgleton instance.

Module instance is accessible in every part of code via call:

```php
// get module instance into $module when instance has name 'myModule'
$module = main()->module->myModule;

// other examples by module interface:
$moduleName = main()->module->myModule->name(); // returns unique name in IoC container
$moduleConfiguration = main()->module->myModule->cfg(); // returns a configuration injected to constructor

// how to call custom methods
$result = main()->module->myModule->doSomething(); // we can define custom method(s) in our module, e.g. doSomething()
```

Module class is *facade* to module service(s), module factory or to other special functionality.

Module class implements `ha\Internal\DefaultClass\Module\Module` or extends `ha\Internal\DefaultClass\Module\ModuleDefaultAbstract`. Module class method are also useful for lazy services initializon (create service instance only when is required). 

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

## Module parts by functionality

- [Models](models.md): represents a records in some datasource
- Models collections: array of models with extended functionality and type checking for children 
- Factory: factory for creating models and models collections
- [Services](services.md): application/bussines logic, IO logic (read, insert, delete and update data)

## Why are modules required?

Module flow in ha framework provides:

- unified and standardized access to logic
- logic separated from other logic (logic is exactly divided into modules)
- large dependency injections in our classes are not required, module is accessible from all parts of code via 
`main()->module->myModule`
- lazy initialization of services
- automatic initialization by configuration in app bootstrap
- module makes public access only for concrete functionality (everything other is private outside module); this separates and encapsualtes large logic in module services and subservices from other app parts, so accessible are only required services


## How to add module instance to app

Module is automatically initialized by configuration from config file. See next chapter *Module(s) configuration* for details. Another, but **highly not recommended way** is:

```php
main()->module->myMyModule = new MyModule(); // really very wrong, this makes app bootstrap
```

## Module(s) configuration

Module(s) configuration is stored in config file in `$cfg['modules']` array. Every item is array, which has two items: *(string) className* and *(array) configuration*. Key `name` must be unique in configruartion, this defines unique name of instance in module IoC container. Framework automatically creates instances from this configuration and adds theirs to IoC module container in app bootstrap. 
So `name` key is always required in configuration, other keys are specific by module (in our example `sqlMiddlewareName`, `cacheTTL`) and they defines real configuration data.

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

This example shows, how we can have separated logic for users, articles and languages.


## How to create new module

Our class must implement interface `ha\Internal\DefaultClass\Module\Module` or must be extended from `ha\Internal\DefaultClass\Module\ModuleDefaultAbstract` (this abstract class has default implementation of module interface). Abstract is useful, when we can use normal functionality. If we need e.g. special initializon in constructor, we can implement an interface.

```php
class ArticleModule extends ModuleDefaultAbstract
{
}
```

Next we can add facade method for factory, which will create module models.

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

// create facade method for factory in module with lazy initialization
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

// example call in external code:
$article = main()->module->article->factory()->createArticle(Article::ARTCILE_TYPE_BLOG);
```

Now we can add facade method(s) for accessing to some service(s). Please see [services docs](services.md) for more details about services.

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

Now we can add facade method(s) to other functionality.

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

As we can see, internal complex logic is invisible from outside. Implementation details are independent outside module.
