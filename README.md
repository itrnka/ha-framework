![ha framework](docs/img/ha-logo.png "ha framework")

*ha* framework is PHP 7.1 framework for professionals with strict typing. It is a truly flexible framework without ballast and is based on interfaces and some predefined instances that can be changed at any time. This framework is a vendor package installable via composer with a small default required functionality that sticks together any components to meet service-oriented architecture requirements at the code level. And you can easly add to this concept everything, what you need - custom packages, custom ORM, custom drivers...

Framework architecture is based on access type: Application logic is strictly separate from the approach method and access method (such as HTTP application, Rest API application, mobile page, website, console, ...).

Please read [**framework documentation**](docs/index.md) for more details.

### Installation

Framework can be installed via [ha project skeleton](https://github.com/itrnka/ha-project-skeleton/blob/master/README.md). Framework is only composer package and requires bootstrap from this simple skeleton.

### Framework highlights

- based on PHP 7.1
- always strict typing, everything has interface, everything has also scalar typing and return value typing (automatically reduced >50% developers bugs)
- SEO ready (extra routing on cases, when MVC is bad way)
- precise HTTP handling (headers controll, request method checking, ...)
- ready for multiple data sources (very good support for multiple instances of the same type and also different type of drivers)
- cascade data IO operations (e.g. write to SQL, Elasticsearch, cache vs. read from cache, elasticsearch, SQL)
- default ORM not implemented, manipulation with data is open (we can have very complex objects in which the components are retrieved from other data sources than primary data)
- extreme IDE support (everything is autocompleted, e.g. in *PHP Storm*)
- lightweight and allways reusable code (no useless packages in core functionality)
- based on interfaces (everything can be changed or extended without large code rewrites)
- everything is instance, nowhere are static calls being used (very good dependency injection)
- low memory consuption
- models collections with type protection (e.g. category could not be added to products collection)
- model property typehinting
- access to model properties is case insensitive and camelCase/dash_case insensitive (very useful for cases where db fields are dash_cased and camelCased properties, etc.)
- the application structure is independent of use (the same functionality with different access methods and rendering, such as API, web page, mobile page, shell access, ...; project is not just a website, webiste can be only a small part of our project)
- functionality versioning (the same project can work with versioned classes by environment)
- everything can be configured in config files and application is builded from config file (it works similarly as *docker-compose.yml*)
- simulated string[], int[], float[], bool[] collections for better array type checking in PHP
- this is not wrong Symfony wrapper such as laravel

