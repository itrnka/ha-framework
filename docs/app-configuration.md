![ha framework](docs/img/ha-logo.png "ha framework")

# Application configuration in *ha* framework

The application based on the *ha* framework works in such a way that everything we need for a given application can be configured through a configuration file. Our application can be used in many different cases and our code can work in various variations and combinations. Specific behavior is defined in a particular configuration, and this configuration is typically stored in a single configuration file. This configuration resolves to one environment. It's not just a *devel*, *stage* or *production*. 

> We can have a lot of environments. One environment can be for a web page, the other can be for a mobile application, the third can be for an API, a fourth for a daemon, etc. And each of these configurations can have its *devel*, *stage* and *production* subversion (each subversion in one separated configuration file).

Configuration in *ha* framework is not just a connection setup. We specify that what application implementation will be used, what modules will be available, what middleware will be available, and our configuration may also contain some values that we can use in the application as pseudo-global variables. We also define what version of our classes will be used and which router implementation is used to process requests.

### Path to configuration files
All configuration files are stored in folder `{projectRoot}/php/conf`. The files are not in *ini* format, but they are php files because we can use here constants (e.g. `__DIR__`), anonymous functions, and so on.

#### File name 

Once we know the name of our environment, the path to the configuration file will be as follows: 
`{projectRoot}/php/conf/{environmentName}.conf.php`. This file is loaded in app bootstrap, when detected environment name is `{environmentName}`.

#### Important notes for environment name

Each configuration defines the behavior of the application in a particular environment, so we need to name the environment. The best way is to use a specific environment variable that will specify the name of the configuration file. Using the environment variable is useful because we can define such a variable, for example, in the docker container, and let the app know how to behave. Environment variable can be set, for example, also in *.htaccess* file by host or by something else. We can also use a constant or some other mechanism, but then we lose the huge flexibility of the configurability.

#### Environment name detection in HTTP appplication

When we run our application over an HTTP server (*apache*, *nginx*, ...), our bootstrap file is `{projectRoot}/public/index.php`. To modify the environment name detection logic, find and edit the following lines in this file:

```php
// detect environment name or use default
$environmentName = getenv('HA_APP_ENV');
if (!is_string($environmentName) || empty($env)) {
    $environmentName = 'web';
}
```
Example of environment name as a constant in *.htaccess* file:

```
<IfModule mod_env.c>
    SetEnv HA_APP_ENV web
</IfModule>
```
Example of environment name detection by host in *.htaccess* file:

```
<IfModule mod_setenvif.c>
    SetEnvIfNoCase Host ^(.*)$ HA_APP_ENV=no-web-host # default envName
    SetEnvIf Host www.example-1.com HA_APP_ENV=example-1 # envName for host1
    SetEnvIf Host www.example-2.com HA_APP_ENV=example-2 # envName for host2
    SetEnvIf Host www.example-n.com HA_APP_ENV=example-n # envName for hostN
</IfModule>
```

#### Environment name detection in console appplication

When we run our application over shell command by calling `{projectRoot}/bin/ha`, the environment name is extracted from ini file `{projectRoot}/bin/ha.ini`. To modify the environment name, find and edit the following line in this file:

```
environment_name = 'console'
```


### Configuration file structure

Configuration file must begining with variable definition:

```php
$cfg = []; 
```

#### Part 1 - Application core	

**App builder definition:** Bootstrap creates an application instance by calling the builder who makes the app. A [default builder](../src/ha/App/Builder/AppBuilderDefault.php) is always sufficient, but we can also write our own (for very special cases). The builder must always implement an interface [`ha\App\App\AppBuilder`](../src/ha/App/Builder/AppBuilder.php). Configuration requires a class definition of this builder:

```php
$cfg['app.builder.className'] = ha\App\App\AppBuilderDefault::class;
```

**Environment name:** You need to add the environment name that is received as `$environmentName `from the bootstrap process to the configuration:

```php
$cfg['app.environmentName'] = $environmentName;
```

**Unique app name:** In some processes, we need to know the unique name or hash of our application in order to recognize this application from other applications. This name can then also be used inside the application, such for cache keys prefix.

```php
$cfg['app.uniqueName'] = md5(__DIR__); // please use unique name or hash
```

**Root directory path for project (project root):** Here is stored `public` and `php` directory. In our examples, we use `{projectRoot}` string for this variable.

```php
$cfg['app.rootDir'] = dirname(__DIR__);
```

**Public directory path (document root):** Here is our `index.php`. By default is it `{projectRoot}/public` (this is highly recommended). This folder is document root in our server (e.g. nginx, apache, ...).

```php
$cfg['app.publicDirHTTP'] = $cfg['app.rootDir'] . '/public';
```

#### Part 2 - Project files (prepare *PSR-4* autoloading)

**Autoloading root path:** We need to define where our project files are stored. This folder will use our application to load the classes by [*PSR-4*](https://en.wikipedia.org/wiki/PHP_Standard_Recommendation) standard. By default, value is `ver-1.0.0` and full path to this directory is `{projectRoot}/php/ver-1.0.0`. 

> This allows us to use our project files in different versions for different environments. For example, we can easily test a new version of our app simply by changing the environment name, or we can create a new version of the files for some environment without affecting other parts of the app and other access methods. When an app is available through 3 different access methods (e.g. API, website, mobile site), and if we need to modify or add another method without changing the code in existing methods, this is a perfect way to do this. So we can simply create new app version and older versions will be unchanged. So API and website can work without changes, and the mobile site can be modified to have the files stored in another folder (as a new app version).

```php
$cfg['app.class.version'] = 'ver-1.0.0';
```

**Supported namespaces:** In order for automatic loading to work properly, we need to define which namespaces are stored in this folder. But we only define the root names of these namespaces. For example, when we have classes `MyProject\Module\MySomeModule\MySomeModule`, `MyProject\Module\MySomeModule\Models\MyModel1`, ..., namespace root is `MyProject`. Its highly recommend using custom namespace for every access method. Access method will be completely independent of the other code and will be removed from your application in the future.

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
