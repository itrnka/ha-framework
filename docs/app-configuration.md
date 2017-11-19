# App configuration

**Idea of ha framewrok is: everything, what we need to use, is defined in configuration.**

### About configuration files
Our configuration is stored in config files in folder `{projectRoot}/php/conf`. Configuration files are not `*.ini` files, but native `*.php` files. Why? In php can be defined closure functions, we can use constants as `__DIR__`, etc. 

Our application can be used in many different cases (the same code can works in many variations and combinations). Concrete variation has concrete configuration file. What this means? In configuration file is defined:
- which application implementation would be loaded (AppBuilder class name)
- which middleware would be loaded (list of configrations for concrete middleware instances)
- which modules would be loaded (list of configrations for concrete module instances)
- which folder is used for our project files (project files version)
- which router implementation would be used for handling requests (only HTTP access; RouterBuilder class name is defined)
- which commands would be available from console (only shell access)
- project specific variables (pseudo global variables)
- etc.

Concrete configuration file is based on some single environment variable. This variable defines concrete environment name. This name determines, which configuration file is loaded. We can also use custom logic for environment name detection, framework is open for this functionality. Environment variable can be defined in `.htaccess` file (or we can also use anything other, e.g. host name). This logic is defined in initialization file `{projectRoot}/public/index.php` (this file will handle entire application and other php files in public folder are useless and potentialy unsafe).

App bootstrap detects environment name, then loads configuration file by this name and initializes everything by this configuration.

### Configuration file location

When we have our environment name, configuration file for this environment will be stored in `{projectRoot}/php/conf/{environmentName}.conf.php`.


### Configuration file structure

Configuration file must begining with variable definition:

```php
$cfg = []; // do not remove this line, $cfg variable is very important and required for bootstrap
```

#### Part 1 - Application core	

App is created in bootstrap via app builder and builder class name must be defined in configuration. This builder must implements interface `ha\App\App\AppBuilder`. We can use default implementation `ha\App\App\AppBuilderDefault` (suitable for most cases, but in special cases we can use different implementation). App builder is object, which returns builded application instance.

```php
$cfg['app.builder.className'] = ha\App\App\AppBuilderDefault::class;
```

Store environment name recieved from bootstrap: bootstrap initializes variable `$environmentName` and is usefull making this variable accessible later from our app.

```php
$cfg['app.environmentName'] = $environmentName;
```

Application must have unique name in our OS (or in cluster). This name is used e.g. in cache, when cache requires unique key for some variable. For example, in APC(u) we must have prefix for variables (when app1 and app2 uses variable 'x', this variable must be different for app1 and app2 and therefore must be prefixed with unique string).

```php
$cfg['app.uniqueName'] = md5(__DIR__); // please use concrete name
```

Root directory path for project (where is stored `public` and `php` directory). In our examples we use `{projectRoot}` string for this variable.

```php
$cfg['app.rootDir'] = dirname(__DIR__);
```

Public directory: here is our `index.php`. By default is it `{projectRoot}/public` (this is highly recommended).

```php
$cfg['app.publicDirHTTP'] = $cfg['app.rootDir'] . '/public';
```

#### Part 2 - Project files (prepare *PSR-4* autoloading)

We must define, where are stored our project files (for this configuration). This directory is used as root for autoloading php files by *PSR-4* standard. This is very very useful, this can be used for versioning. When our project files have e.g. 3 access methods with the same functionality (e.g. API, web page, mobile page) and we need add or modify some access method, we can clone files to another dir and set root for this case to this folder. So we can create next version of app without changing other access methods. This is perfect interface for multiple variations of our app. By default, value is `ver-1.0.0` and full path to this directory is `{projectRoot}/php/ver-1.0.0`. 

```php
$cfg['app.class.version'] = 'ver-1.0.0';
```

Next, when we have defined root directory for class autoloading, we must list namespaces, which are stored in this directory. But we only defines root names of this namespaces. Fo example, when we have classes `MyProject\Module\MySomeModule\MySomeModule`, `MyProject\Module\MySomeModule\Models\MyModel1`, ..., namespace root will be (`MyProject`). Its highly recommend using custom namespace for every access method, it will make access method independent from other code and will be simply removed from your application in future.

```php
$cfg['app.class.namespaceRoots'] = ['MyProject', 'MyMiddleware', 'WebAccess', 'ConsoleAccess'];
```

Note: class `MyProject\Module\MySomeModule\MySomeModule` will be autoloaded in this case from location `{projectRoot}/php/ver-1.0.0/MyProject/Module/MySomeModule/MySomeModule.php`.


#### Part 3 - Middleware configuration

See [middleware docs](middleware.md).

#### Part 4 - Modules configuration

See [modules docs](modules.md).

#### Part 5A - Routing configuration - only for HTTP access

(Todo).

#### Part 5B - Commands configuration - only for shell access (console)

(Todo).

#### Part 6 - Custom variables (pseudo global variables)

This configuration (full variable `$cfg`) is available via `main()->cfg()->get('my.some.configuration.variable')`, so is very usefull store some specific data into configuration. It will works as global variable. We can define our custom pseudo global variable by this example:

```php
$cfg['my.some.key1'] = 123;
$cfg['my.some.key2'] = function(int $val): int { return ($val * 2);};
$cfg['my.server.timezone'] = function(): DateTimeZone { return date_​default_​timezone_​get();};
// ...
```

### Full example

```php
<?php
// create configuration variable (here is cinfiguration stored)
$cfg = []; // do not remove this line, $cfg variable is very important and required for bootstrap

//-------------------------------------------------------------------------------------------------------------------------
// APP
//-------------------------------------------------------------------------------------------------------------------------

/** @var string $cfg ['app.environmentName'] Store env name recieved from bootstrap (make it accessible from app container) */
$cfg['app.environmentName'] = $environmentName;

/** @var string $cfg ['app.builder.className'] Name of a class, which implements \ha\App\Builder\AppBuilder */
$cfg['app.builder.className'] = ha\App\App\AppBuilderDefault::class;

/** @var string $cfg ['app.uniqueName'] Unique name of app - define unique app name in app "cluster" */
$cfg['app.uniqueName'] = md5(__DIR__); // please use concrete name

/** @var string Project root directory. */
$cfg['app.rootDir'] = dirname(__DIR__);

/** @var string Public root directory for HTTP access. */
$cfg['app.publicDirHTTP'] = $cfg['app.rootDir'] . '/public';

//-------------------------------------------------------------------------------------------------------------------------
// Class autoload by PSR-4 standard
//-------------------------------------------------------------------------------------------------------------------------

/** @var string $cfg ['app.class.version'] Project files root dir for class autoloading */
$cfg['app.class.version'] = 'ver-1.0.0';

/** @var array $cfg ['app.class.namespaceRoots'] string[] Array with your custom namespace roots used in your logic */
$cfg['app.class.namespaceRoots'] = ['MyProject', 'MyMiddleware', 'WebAccess', 'ConsoleAccess'];

//-------------------------------------------------------------------------------------------------------------------------
// Middleware
//-------------------------------------------------------------------------------------------------------------------------

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

//-------------------------------------------------------------------------------------------------------------------------
// Modules
//-------------------------------------------------------------------------------------------------------------------------

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

//-------------------------------------------------------------------------------------------------------------------------
// Access configuration (rounting/commands, based on access type)
//-------------------------------------------------------------------------------------------------------------------------

// TODO

//-------------------------------------------------------------------------------------------------------------------------
// Other (pseudo global variables)
//-------------------------------------------------------------------------------------------------------------------------

$cfg['my.some.key1'] = 123;
$cfg['my.some.key2'] = function(int $val): int { 
    return ($val * 2);
};
$cfg['my.server.timezone'] = function(): DateTimeZone { 
    return date_​default_​timezone_​get();
};
```


#### Best practices for configuration files

We can have shared configuration file (e.g. `{projectRoot}/conf/_shared.php`) and this file will be included into concrete configuration. Concrete configuration then only appends some specific data. We extendig our base configuration in this case with specific configuration data.
