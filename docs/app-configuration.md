# App configuration

**Idea of ha framewrok is: everything, what we need to use, is defined in configuration.**

### About configuration files
Our configuration is stored in config files in folder `{projectRoot}/php/conf`. Configuration files are not `*.ini` files, but native `*.php` files. Why? In php can be defined closure functions, we can use constants as e.g. `__DIR__`, etc. 

Our application can be used in many different cases (the same code can works in many variations and combinations). Concrete variation has concrete configuration file. What this means? In configuration file is defined:
- which application implementation is loaded (AppBuilder class name)
- which middleware is loaded (list of configrations for concrete middleware instances)
- which modules are loaded (list of configrations for concrete module instances)
- which folder is used for our project files (project files version)
- which router implementation is loaded for handling requests (only HTTP access; RouterBuilder class name is defined)
- which commands are available from console (only shell access)
- project specific variables (pseudo global variables)
- etc.

Concrete configuration file is based on some single environment variable. This variable defines concrete environment name. This name determines, which configuration file is loaded. We can also use custom logic for environment name detection, framework is open for this functionality. Environment variable can be defined in `.htaccess` file (or we can also use anything other, e.g. host name). This logic is defined in initialization file `{projectRoot}/public/index.php` (this file will handle entire application and other php files in public folder are useless and potentialy unsafe). `{projectRoot}/public/index.php` is a bootstrap for HTTP access to application.

App bootstrap detects environment name, then loads configuration file by this name and initializes everything by this configuration.

### Configuration file location

When we have our environment name, configuration file path for this environment is `{projectRoot}/php/conf/{environmentName}.conf.php`.


### Configuration file structure

Configuration file must begining with variable definition:

```php
$cfg = []; // do not remove this line, $cfg variable is very important and required for bootstrap
```

#### Part 1 - Application core	

**App builder definition:** App is created in bootstrap via app builder. This builder creates single instance of our application and this instance is accessible from all parts of code via `$app = main();`. So builder class name must be defined in configuration for this functionality. This class must implementing interface `ha\App\App\AppBuilder`. We can also use default framework implementation `ha\App\App\AppBuilderDefault`. This is suitable for most cases, but we can use in special case also different implementation. We can simply change functionality of our app with our new implementation. This is unreal in most frameworks. So app builder is object, which builds and returns certain application instance with required interface implementation.

```php
$cfg['app.builder.className'] = ha\App\App\AppBuilderDefault::class;
```

**Environment name:** Store environment name recieved from bootstrap: bootstrap declares variable `$environmentName` and this variable is accessible in this scope. Is very usefull making this variable accessible from our app as our pseudo global variable.

```php
$cfg['app.environmentName'] = $environmentName;
```

**Unique app name:** Application must have unique name in our OS (or in cluster). This name is used e.g. in cache, when cache requires unique key for some variable. For example, in APC(u) we must have prefix for variables (when app1 and app2 uses variable 'x', this variable must be different for app1 and app2 and therefore must be prefixed with unique string). Without this variable are data not unique in some systems and not unique value for this variable can cause large problems accross our applications.

```php
$cfg['app.uniqueName'] = md5(__DIR__); // please use concrete name
```

**Root directory path for project (project root):** Here is stored `public` and `php` directory. In our examples we use `{projectRoot}` string for this variable.

```php
$cfg['app.rootDir'] = dirname(__DIR__);
```

**Public directory path (document root):** Here is our `index.php`. By default is it `{projectRoot}/public` (this is highly recommended). This folder is document root in our server (e.g. nginx, apache, ...).

```php
$cfg['app.publicDirHTTP'] = $cfg['app.rootDir'] . '/public';
```

#### Part 2 - Project files (prepare *PSR-4* autoloading)

**Autoloading root path:** We must define, where are stored our project files for this current configuration. This directory is used as root for autoloading php files by *PSR-4* standard. This is very very useful, this can be e.g. used for special or new versions of our application code. When our project has e.g. 3 access methods with the same functionality (e.g. API, web page, mobile page) and we need add or modify some access method, we can clone files to another dir and set root for this case to this folder. So we can create next version of our app without changing other access methods and without changing existing files. This is perfect interface for multiple variations of our app. By default, value is `ver-1.0.0` and full path to this directory is `{projectRoot}/php/ver-1.0.0`. 

```php
$cfg['app.class.version'] = 'ver-1.0.0';
```

**Supported namespaces:** When we have defined root directory for classes autoloading, we must also define supported namespaces stored in this directory. But we only defines root names of this namespaces. For example, when we have classes `MyProject\Module\MySomeModule\MySomeModule`, `MyProject\Module\MySomeModule\Models\MyModel1`, ..., namespace root is `MyProject`. Its highly recommend using custom namespace for every access method. It will make access method totaly independent from other code and will be simply removed from your application in future.

```php
$cfg['app.class.namespaceRoots'] = ['MyProject', 'MyMiddleware', 'WebAccess', 'ConsoleAccess'];
```

**Autoloading notes:** class `MyProject\Module\MySomeModule\MySomeModule` is automatically loaded in this case from location `{projectRoot}/php/ver-1.0.0/MyProject/Module/MySomeModule/MySomeModule.php`.


#### Part 3 - Middleware configuration

See [middleware docs](middleware.md#middleware-configuration).

#### Part 4 - Modules configuration

See [modules docs](modules.md#modules-configuration).

#### Part 5A - Routing configuration - only for HTTP access

(Todo).

#### Part 5B - Commands configuration - only for shell access (console)

(Todo).

#### Part 6 - Custom variables (pseudo global variables)

Configuration defined in this file (full variable `$cfg`) is automatically available from our app instance in all parts of our code. When we have config variable `$cfg['my.some.configuration.variable']`, we can simple accessing this variable from wherever by calling `main()->cfg()->get('my.some.configuration.variable')`. So is very usefull store some specific data into configuration. This principe so allows defining our custom pseudo global variable by this example:

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

We can have shared configuration file (e.g. `{projectRoot}/conf/_shared.php`) and this file can be included into concrete configuration file. Concrete configuration then only appends or overrides some specific data. This extends our base configuration in this case with specific configuration data.
