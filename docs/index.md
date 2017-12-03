![ha framework](img/ha-logo.png "ha framework")

# *ha* framework documentation

## Table of contents

1. [Introduction](introduction.md)
2. [Application structure](app-structure.md)
3. [Middleware](middleware.md)
4. [Modules](modules.md)
   * [Services](services.md)
   * [Models](models.md)
   * [Models collections](models-collections.md)
5. [Application configuration](app-configuration.md)
6. [HTTP application](http-routing.md)
7. [Console application](shell.md)

## Official packages

### Project skeleton

- [Basic skeleton](https://github.com/itrnka/ha-project-skeleton) - skeleton for PHP project without frontend enhancements

- [Skeleton for static files](https://github.com/itrnka/ha-project-skeleton-assets) - extends basic skeleton, provides optimization, compilation and publishing of static files, such as CSS, JS, font and images by using [Grunt](https://gruntjs.com/) and [Bower](https://bower.io/).


### Middleware

- [Twig templates](https://github.com/itrnka/ha-twig-renderer-middleware) - Twig template renderer middleware implementation for *ha* framework based on [Twig](https://twig.symfony.com/).
- [APCu cache](https://github.com/itrnka/ha-apcu-middleware) - [APCu](http://php.net/manual/en/book.apcu.php) cache client middleware implementation for *ha* framework.
- [MySQLi driver](https://github.com/itrnka/ha-mysqli-middleware) - MySQLi middleware implementation for *ha* framework. Provides access to MySQL databases. Query builder is also included.
- [Elasticsearch driver](https://github.com/itrnka/ha-elasticsearch-middleware) - Elasticsearch client middleware implementation for ha framework based on official [elasticsearch PHP API](https://github.com/elastic/elasticsearch-php).

### HTTP access to application

- [Smart HTTP Rest API](https://github.com/itrnka/SmartHTTPRestAPI) - Rest API handling for *ha* framework. Package includes routes and controllers by *ha* framework standards. Currently is supported only *JSON* body in requests and responses.

