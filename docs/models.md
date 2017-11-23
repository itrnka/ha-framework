# Models in *ha* framework

Model in ha framework is simple class, which holds only raw data. This is very important for many cases, when we create very complex and large models from multiple datasources. Primary object can be loaded from RDBMS database (such as MySQL) and children can be loaded e.g. from cache. Result in one complex model, which in application logic is single object.

This principe is very usefull in cases, when we use multiple storages for the same data. For exmple, when primary database is MySQL (slow) and secondary database is Elasticsearch (high speed). We can store data in MySQL and the same data are replicated in Elasticsearch. Multiple models for each storage are not good, one model is enough (less time to development, better clarity, ...). For example, also cache is  other datasource.

When we execute read operation on datasource, read method in service transforms loaded row into our model, write method in service converts model to datasource row and writes it.

Model in *ha* framework is extended from abstract `ha\Internal\DefaultClass\Model\ModelDefaultAbstract` (and so implements interface `ha\Internal\DefaultClass\Model\ModelDefaultAbstract\Model`). This provides default model functionality. Extending is recommended, but you can use your custom impleementation by interface (but next complex functionality described in this document will be not implemented automatically).

Note: ORM based functionality for models is wrong way. ORM is very slow and is directly and inseparably depended on concrete datasource instance. Principe used for models in *ha* framework is datasource independent and is very flexible. So models are absolutely separated from datasources and can be used in future for other datasources without code changes. Also datasource can be removed from application without model changes. This is very important for code reusability. The same models can be used accross multiple projects.


## Working with models

**Note: examples used in this chapter are based on [model class example](#model-class-example) at end of this document.**

### How model properties works

Please define your properties scope to *protected* or *private*. If property has *public* scope, type checking does not working. If your model is extended from abstract `ha\Internal\DefaultClass\Model\ModelDefaultAbstract`, direct public access to model property is translated via `__get()` and `__set()` magic methods to defined getter or setter. This principe provides also camelCase/dash_case insensitivity and case insensitivity for direct access to model properties. Name translation is cached and is very fast.

So if getter is defined, property is directly accessible for reading. If setter is defined, property is directly accessible for writing. E.g.:

```php
$model->id = 5;
// is translated to
$model->setId(5);
```

Public access to model properties is therefore depenent on defining getters/setters and can be combined, e.g. when we define only getter, property is read only from public scope. Please see section [Model class example](#model-class-example) to better understanding at end of this document.

Please use camelCase format for model properties, some optional functionality is based on camelCase format.


### How to set model properties

Automatically set properties on creating model from native array:

```php
$rawData = [
    'id' => 75,
    'name' => 'My car',
    'ENGINE_VOLUME' => 1.6, // camelCase/dash_case + case insensitivity example
];
$car = new Car($rawData);
```
```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $car                                                                         │
└──────────────────────────────────────────────────────────────────────────────┘
Examples\Module\CarModule\Model\Car\Car (7) (
    protected 'brand' -> null
    protected 'brandId' -> null
    protected 'color' -> null
    protected 'engineVolume' -> double 1.6
    protected 'name' -> string (6) "My car"
    protected 'year' -> null
    private 'id' -> integer 75
)
```

Filling model properties from native array:

```php
$rawData = [
    'id' => 75,
    'name' => 'My car',
    'ENGINE_VOLUME' => 1.6, // camelCase/dash_case + case insensitivity example
];
$car = new Car();
$car->fillFromArray($rawData);
```
```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $car                                                                         │
└──────────────────────────────────────────────────────────────────────────────┘
Examples\Module\CarModule\Model\Car\Car (7) (
    protected 'brand' -> null
    protected 'brandId' -> null
    protected 'color' -> null
    protected 'engineVolume' -> double 1.6
    protected 'name' -> string (6) "My car"
    protected 'year' -> null
    private 'id' -> integer 75
)
```

Setting properties manually:

```php
$car = new Car();
$car->id = 75;
$car->engineVolume = 1.6;
```
```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $car                                                                         │
└──────────────────────────────────────────────────────────────────────────────┘
Examples\Module\CarModule\Model\Car\Car (7) (
    protected 'brand' -> null
    protected 'brandId' -> null
    protected 'color' -> null
    protected 'engineVolume' -> double 1.6
    protected 'name' -> null
    protected 'year' -> null
    private 'id' -> integer 75
)
```

Access to property name is dash_case/camelCase insensitive:

```php
$car->engineVolume = 1.6;
// is the same as
$car->engine_volume = 1.6;
```
This is very very usefull, when we filled model properties from array with keys in dash_case format and when properties are in camelCase format. Properties are also case insensitive:

```php
$car->engineVolume = 1.6;
// is the same as
$car->eNgInEvOlUmE = 1.6;
// and is the same as
$car->eNgInE_vOlUmE = 1.6;
```
Property change can be also executed via setter (all previous examples was translated to setter methods)

```php
$car->setEngineVolume(1.6);
```

### How to get model properties

Model properties are accessible directly, if getter for concrete property is defined. Access is similary as in previous examples and is also camelCase/dashCase + case insensitive.

```php
$value = $car->engineVolume;
// is the same as
$value = $car->engine_volume;
// and is the same as
$value = $car->getEngineVolume();
```

### Special constant for working with collections

Every model can be converted to associated collection. For this functionality must be defined special constant `COLLECTION_CLASS`. Value of this constant is collection class name, which is default for this model. This collection can have extra functionality for data binding and manipulation with models (see [Models collections](models-collections.md)). Example:

```php
const COLLECTION_CLASS = Cars::class;
```

### How to convert models

Model can be converted to some other types. This is usefull, when you need e.g. get model as raw data or as native array.

#### Invoking models

First usefull method is `__invoke()`. This magic method allows calling model as function. It returns properties as array or as stdClass, but this method **ignores private properties**. 

```php
$car = new Car();
$car->name = 'My car';
$valueAsArray = $car();
$valueAsObject = $car(true);
```
```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $valueAsArray                                                                │
└──────────────────────────────────────────────────────────────────────────────┘
array (6) [
    'brand' => null
    'brandId' => null
    'color' => null
    'engineVolume' => null
    'name' => string (6) "My car"
    'year' => null
]
┌──────────────────────────────────────────────────────────────────────────────┐
│ $valueAsObject                                                               │
└──────────────────────────────────────────────────────────────────────────────┘
stdClass (6) (
    public 'brand' -> null
    public 'brandId' -> null
    public 'color' -> null
    public 'engineVolume' -> null
    public 'name' -> string (6) "My car"
    public 'year' -> null
)
```will be converted
Ignoring properties is also supported:

```php
$car = new Car();
$car->name = 'My car';
$valueAsArray = $car(false, ['color', 'year', 'engineVolume']);
$valueAsObject = $car(true, ['color', 'year', 'engineVolume']);
```
```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $valueAsArray                                                                │
└──────────────────────────────────────────────────────────────────────────────┘
array (3) [
    'brand' => null
    'brandId' => null
    'name' => string (6) "My car"
]
┌──────────────────────────────────────────────────────────────────────────────┐
│ $valueAsObject                                                               │
└──────────────────────────────────────────────────────────────────────────────┘
stdClass (3) (
    public 'brand' -> null
    public 'brandId' -> null
    public 'name' -> string (6) "My car"
)
```

#### Converting model to array or stdClass

Method `getAsArray()` and `getAsStdClass()` returns similar output as `__invoke()` method, but we can define, whether property names will be converted to camel_case format (camelCase format is predefined for model property names) and we can also ignore properties, which are not scalar values (this is required for some cases, when defined property is recursive  to same model).

```php
$car = new Car();
$car->name = 'My car';
$valueAsArray1 = $car->getAsArray(false, ['color', 'year'], false);
$valueAsArray2 = $car->getAsArray(true, ['color', 'year'], false);
$valueAsObject1 = $car->getAsStdClass(false, ['color', 'year'], false);
$valueAsObject2 = $car->getAsStdClass(true, ['color', 'year'], false);
```
```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $valueAsArray1                                                               │
└──────────────────────────────────────────────────────────────────────────────┘
array (4) [
    'brand' => null
    'brandId' => null
    'engineVolume' => null
    'name' => string (6) "My car"
]
┌──────────────────────────────────────────────────────────────────────────────┐
│ $valueAsArray2                                                               │
└──────────────────────────────────────────────────────────────────────────────┘
array (4) [
    'brand' => null
    'brand_id' => null
    'engine_volume' => null
    'name' => string (6) "My car"
]
┌──────────────────────────────────────────────────────────────────────────────┐
│ $valueAsObject1                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
stdClass (4) (
    public 'brand' -> null
    public 'brandId' -> null
    public 'engineVolume' -> null
    public 'name' -> string (6) "My car"
)
┌──────────────────────────────────────────────────────────────────────────────┐
│ $valueAsObject2                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
stdClass (4) (
    public 'brand' -> null
    public 'brand_id' -> null
    public 'engine_volume' -> null
    public 'name' -> string (6) "My car"
)
```

#### Converting model to collection

Method `createAssociatedCollection()` creates a new collection by model constant `COLLECTION_CLASS` class name and appends source model to this collection. Also key in collection can be defined. This collection can have extra functionality for data binding and has special methods to manipulation with models (see [Models collections](models-collections.md)). So we can add functionality only to collection and this functionality is accessible also for model(s). Trick is in conversion to collection, which has required functionality. Collection methods can be called then from model.


```php
$car = new Car();
$car->name = 'My car';
$collection1 = $car->getAsCollection();
$collection2 = $car->getAsCollection('mySomeKey');
```
```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $collection1                                                                 │
└──────────────────────────────────────────────────────────────────────────────┘
Examples\Module\CarModule\Model\Car\Cars (1) (
    public 0 -> Examples\Module\CarModule\Model\Car\Car (7) (
        protected 'brand' -> null
        protected 'brandId' -> null
        protected 'color' -> null
        protected 'engineVolume' -> null
        protected 'name' -> string (6) "My car"
        protected 'year' -> null
        private 'id' -> null
    )
)
┌──────────────────────────────────────────────────────────────────────────────┐
│ $collection2                                                                 │
└──────────────────────────────────────────────────────────────────────────────┘
Examples\Module\CarModule\Model\Car\Cars (1) (
    public 'mySomeKey' -> Examples\Module\CarModule\Model\Car\Car (7) (
        protected 'brand' -> null
        protected 'brandId' -> null
        protected 'color' -> null
        protected 'engineVolume' -> null
        protected 'name' -> string (6) "My car"
        protected 'year' -> null
        private 'id' -> null
    )
)
```

When we can only create empty collection (without placing model to collection), we can use:

```php
$car = new Car();
$car->name = 'My car';
$collection = $car->createAssociatedCollection();
```
```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $collection                                                                  │
└──────────────────────────────────────────────────────────────────────────────┘
Examples\Module\CarModule\Model\Car\Cars (0) ()
```


## Model class example

```php
<?php
declare(strict_types=1);

namespace Examples\Module\CarModule\Model\Car;

use Examples\Module\CarModule\Model\CarBrand\CarBrand;
use ha\Internal\DefaultClass\Model\ModelDefaultAbstract;

/**
 * Model Car.
 * This model can be collected in collection Cars with child type checking functionality.
 * @property int $id
 * @property string $name
 * @property int $brandId
 * @property CarBrand $brand
 * @property float $engineVolume
 * @property string $color
 * @property int $year
 */
class Car extends ModelDefaultAbstract
{
    /** Associated collection class. */
    const COLLECTION_CLASS = Cars::class;

    /** @var CarBrand */
    protected $brand;

    /** @var int */
    protected $brandId;

    /** @var string */
    protected $color;

    /** @var float */
    protected $engineVolume;

    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var int */
    protected $year;

    /**
     * Property 'brand' getter.
     * @return CarBrand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Property 'brandId' getter.
     * @return int
     */
    public function getBrandId()
    {
        return $this->brandId;
    }

    /**
     * Property 'color' getter.
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Property 'engineVolume' getter.
     * @return float
     */
    public function getEngineVolume()
    {
        return $this->engineVolume;
    }

    /**
     * Property 'id' getter.
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Property 'name' getter.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Property 'year' getter.
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Property 'brand' isset checker.
     * @return bool
     */
    public function hasBrand(): bool
    {
        return isset($this->brand);
    }

    /**
     * Property 'brandId' isset checker.
     * @return bool
     */
    public function hasBrandId(): bool
    {
        return isset($this->brandId);
    }

    /**
     * Property 'color' isset checker.
     * @return bool
     */
    public function hasColor(): bool
    {
        return isset($this->color);
    }

    /**
     * Property 'engineVolume' isset checker.
     * @return bool
     */
    public function hasEngineVolume(): bool
    {
        return isset($this->engineVolume);
    }

    /**
     * Property 'id' isset checker.
     * @return bool
     */
    public function hasId(): bool
    {
        return isset($this->id);
    }

    /**
     * Property 'name' isset checker.
     * @return bool
     */
    public function hasName(): bool
    {
        return isset($this->name);
    }

    /**
     * Property 'year' isset checker.
     * @return bool
     */
    public function hasYear(): bool
    {
        return isset($this->year);
    }

    /**
     * Property 'brand' (un)setter.
     *
     * @param CarBrand $brand
     *
     * @return Car
     */
    public function setBrand(CarBrand $brand = null): Car
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * Property 'brandId' (un)setter.
     *
     * @param int $brandId
     *
     * @return Car
     */
    public function setBrandId(int $brandId = null): Car
    {
        $this->brandId = $brandId;
        return $this;
    }

    /**
     * Property 'color' (un)setter.
     *
     * @param string $color
     *
     * @return Car
     */
    public function setColor(string $color = null): Car
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Property 'engineVolume' (un)setter.
     *
     * @param float $engineVolume
     *
     * @return Car
     */
    public function setEngineVolume(float $engineVolume = null): Car
    {
        $this->engineVolume = $engineVolume;
        return $this;
    }

    /**
     * Property 'id' (un)setter.
     *
     * @param int $id
     *
     * @return Car
     */
    public function setId(int $id = null): Car
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Property 'name' (un)setter.
     *
     * @param string $name
     *
     * @return Car
     */
    public function setName(string $name = null): Car
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Property 'year' (un)setter.
     *
     * @param int $year
     *
     * @return Car
     */
    public function setYear(int $year = null): Car
    {
        $this->year = $year;
        return $this;
    }

}
```

As we can see, model uses protected properties. In class anotation are defined this properties as `@property int $id`. This helps autocompleting property names in your IDE editor.

Very important and required is constant `COLLECTION_CLASS`. This defines which class name is used, when we converting model to models collection and when is appended model to collection. Model and collection is so protected to other types (e.g. integer could not be added to collection and many developer errors are prevented with this principe). This is typehinting implementation.

Next step is defining our *protected* or *private* properties and access methods for this properties. Private properties are ignored in moel conversion methods. Access methods must be decalred in camelCase format. Dash_case format is not supported.

Note: getters, setters and checkers can be defined in your IDE via templates or live templates and usage is then very fast. E.g. in *PHP Storm* try shorcut `ALT` + `INSERT` in class body. Template in IDE editors can be edited. In this case, we define only properties and then we apply templates for generating access methods by pressing shortcut (usefull templates will be added in future to separated repository).

**Setter:**

`public function setEngineVolume(float $engineVolume = null): Car`

Name is constructed as *set* + *{propertyName}*. Argument is always typed (e.g. `int $id`) - this provides full protection for input. Default value is `NULL` - this provides reset property functionality, when is called `$model-> id = null;`. Last step is defining return value. This must be `void` or model class name (when we returning `$this` for method chaining).

**Getter:**

`public function getEngineVolume()`

Name is constructed as *get* + *{propertyName}*. Return type is not defined, return value can be `NULL` or of type defined in setter.

**Checker** (This method checks whether value is set or is `NULL`):

`public function hasEngineVolume(): bool`

Name is constructed as *has* + *{propertyName}*. Method returns always bool value, return type is therefore always defined. Checkers are not required, but are nice. Functionality is the same as `isset($model->id)` or `is_null($model->id)`. This method is optional, but is very usefull.

**Other methods**

We can also add other methods (strategy setter for IO operations, methods for using strategies, etc.), but this is already special implementation outside default model funcionality in framework.
