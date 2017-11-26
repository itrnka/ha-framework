# Services

Service in *ha* framework is extended from `ha\Internal\DefaultClass\Service\ModuleServiceDefaultAbstract` or service must implement interface `ha\Internal\DefaultClass\Service\ModuleService`. Service is instance, which provides some application/bussines logic or privdes IO operations (CRUD operations).

Service interface is very simple:

```php
interface ModuleService
{

    /**
     * ModuleService constructor.
     *
     * @param Module $module
     */
    public function __construct(Module $module);

}
```

Service abstract is also very simple:

```php

abstract class ModuleServiceDefaultAbstract implements ModuleService
{

    /** @var Module  */
    protected $module;

    /**
     * ModuleService constructor.
     *
     * @param Module $module
     */
    final public function __construct(Module $module)
    {
        $this->module = $module;
        $this->bootstrap();
    }

    /**
     * Constructor replacement. Setup here class properties.
     *
     */
    abstract protected function bootstrap() : void;

}
```

**Services by functionality and accessibility:**

- Application services
- IO services


## Application services

Application services are services, which are visible from other code parts via facade pattern in module (but that's not the rule - some instances are used only in internal module calls). Application services accessible from module instance are accessible via facade methods, e.g. `$service = main()->module->article->articleService()`. Please see [modules docs](modules.md) for details.

Application services are called from controllers or console commands via module facade methods. These services implements some bussines logic, e.g. ACL checking, some verifications, ... and calls IO services in background. IO services are not directly accessible from controllers or console commands (better security, better modularity, free bond, ...). Controller or console command wants only read or write concrete data and way and datasource is in this view absolutely irrelevant.


## IO services

IO service is a service, which executes CRUD operations on datasource, e.g. RDBMS database, cache system, external system (API) and uses concrete middlewares to CRUD operations. IO service can be called only from application service in the same module and this operation is irrelevant outside module. Controller or console command wants only read or write concrete data and way and datasource is in this view absolutely irrelevant.

IO service:

- transforms standardized query from parent application service to middleware query and executes this query via middleware
- transforms module models to midleware models and inserts or updates this models via middleware query (when called method is for inserting or updating data)
- transforms data loaded from datasource via middleware to module models or models collections and returns these models (when called method is for selecting data) 

So IO service is also data transformator between middleware and application service. Application service works only wwith models inependent from datasource.

### Best practices with IO services

Every IO service will use only one middleware. Very wrong way is combining multiple datasources in one IO service, e.g. cache and SQL. Better way is using separate IO service for every used middleware instance.

When we can use multiple datasorurces in cascade for the same data. .... (also in future is it possible)  ------- to do - under construction 