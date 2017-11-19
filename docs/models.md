# Models in *ha* framework

Model in ha framework is simple class, which holds only raw data. This is very important for many cases, when we create very complex and large models from multiple datasources. Primary object can be loaded from RDBMS database (such as MySQL) and children can be loaded e.g. from cache. Result in one complex model, which in application logic is single object.

This principe is vry usefull in cases, when we use multiple storages for the same data. For exmple, when primary database is MySQL (slow) and secondary database is Elasticsearch (high speed). We can store data in MySQL and the same data are replicated in Elasticsearch. Multiple models for each storage are not good, one model is enough (less time to development, better clarity, ...).

When we execute read operation on datasource, read method in service transforms loaded row into our model, write method in service converts model to datasource row and writes it.

Model in *ha* framework is extended from abstract `ha\Internal\DefaultClass\Model\ModelDefaultAbstract` (and so implements interface `ha\Internal\DefaultClass\Model\ModelDefaultAbstract\Model`). This provides default model functionality.

## Model functionality

- access to properties is case insensitive
- access to properties is camelCase/dash_case insensitive (we can use `$model->myId` or `$model->my_id` and  accessed property is the same); this is usefull, when data are stored in storage in dash_case format and model has defined properties in camelCase format. Property names are cached, so accees to properties is fast (property name translation is very fast).
- we can convert property name between camelCase and dash_case format via methods `underscoredToProperty()` and `propertyToUnderscored()` on model.
- we can detect property getter/setter method name via methods `propertyToSetter()` and `propertyToGetter()` on model.
- model can be converted to array via `$model->getAsArray()`
- model can be converted to stdClass (raw php object) via `$model->getAsArray()`
- we can fill model properties from array via `$model->fillFromArray()`
- binding data to model via methods defined in models collection (see collections for details)
- usefull configurable `__invoke()` method (we can use `$model()` method for many usefull cases with arguments)

ORM based functionality for models is wrong way. ORM is very slow and is directly and inseparably depended on concrete datasource instance. Principe used for models in *ha* framework is datasource independent and is very flexible. So models are absolutely separated from datasources and can be used in future for other datasources without code changes. Also datasource can be removed from application without model changes. This is very important for code reusability. The same models can be used accross multiple projects.


## Model scheme

Example class:

```php
/**
 * Model Edition.
 *
 * This model can be collected in collection Editions with child type checking functionality.
 *
 * @property int $id
 * @property int $year
 * @property int $month
 * @property int $pagesCount
 */
class Edition extends ModelDefaultAbstract
{
    /** Associated collection class. */ 
    const COLLECTION_CLASS = Editions::class;

    /** @var int */
    protected $id;
    
    /** @var int */
    protected $year;
    
    /** @var int */
    protected $month;
    
    /** @var int */
    protected $pagesCount;
    
    /**
     * Property 'id' getter.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Property 'id' (un)setter.
     *
     * @param int $id
     *
     * @return Edition
     */
    public function setId(int $id = null): Edition
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Property 'id' isset checker.
     *
     * @return bool
     */
    public function hasId(): bool
    {
        return isset($this->id);
    }    

    /**
     * Property 'year' getter.
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }
    
    /**
     * Property 'year' (un)setter.
     *
     * @param int $year
     *
     * @return Edition
     */
    public function setYear(int $year = null): Edition
    {
        $this->year = $year;
        return $this;
    }

    /**
     * Property 'year' isset checker.
     *
     * @return bool
     */
    public function hasYear(): bool
    {
        return isset($this->year);
    }    

    /**
     * Property 'month' getter.
     *
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }
    
    /**
     * Property 'month' (un)setter.
     *
     * @param int $month
     *
     * @return Edition
     */
    public function setMonth(int $month = null): Edition
    {
        $this->month = $month;
        return $this;
    }

    /**
     * Property 'month' isset checker.
     *
     * @return bool
     */
    public function hasMonth(): bool
    {
        return isset($this->month);
    }    

    /**
     * Property 'pagesCount' getter.
     *
     * @return int
     */
    public function getPagesCount()
    {
        return $this->pagesCount;
    }
    
    /**
     * Property 'pagesCount' (un)setter.
     *
     * @param int $pagesCount
     *
     * @return Edition
     */
    public function setPagesCount(int $pagesCount = null): Edition
    {
        $this->pagesCount = $pagesCount;
        return $this;
    }

    /**
     * Property 'pagesCount' isset checker.
     *
     * @return bool
     */
    public function hasPagesCount(): bool
    {
        return isset($this->pagesCount);
    }    
    
}
```

As we can see, model uses protected properties. In class anotation are defined this properties as `@property int $id`. This helps autocompleting property names in your IDE editor.

Very important and required is constant `COLLECTION_CLASS`. This defines which class name is used, when we converting model to models collection and when is appended model to collection. Model and collection is so protected to other types (e.g. integer could not be added to collection and many developer errors are prevented with this principe). This is typehinting implementation.

Next step is defining our protected properties and access methods for this properties. Access methods must be in camelCase format. Dash_case format is not supported.

Note: getters, setters and checkers can be defined in your IDE via templates or live templates and usage is then very fast. E.g. in *PHP Storm* try shorcut `ALT` + `INSERT` in class body. Template in IDE editors can be edited. In this case, we define only protected properties and then we apply templates for generating access methods by pressing shortcut (usefull templates will be added in future to separated repository).

**Setter:**

`public function setMyVariable(int $value = null): Edition`

Name is constructed as *set* + *{propertyName}*. Argument is always typed (e.g. `int $id`) - this provides full protection for input. Default value is `NULL` - this provides reset property functionality, when is called `$model-> id = null;`. Last step is defining return value. This must be `void` or model class name (when we returning `$this` for method chaining).

**Getter:**

`public function getPagesCount()`

Name is constructed as *get* + *{propertyName}*. Return type is not defined, return value can be `NULL` or of type defined in setter.

**Checker** (This method checks whether value is set or is `NULL`):

`public function hasPagesCount(): bool`

Name is constructed as *has* + *{propertyName}*. Method returns always bool value, return type is therefore always defined. Checkers are not required, but are nice. Functionality is the same as `isset($model->id)` or `is_null($model->id)`.

**Other methods**

We can also add other methods (staregy setter for IO operations, methods for using strategies, etc.).


## Other usefull model methods

### fillFromArray() method - fill properties from an array

Model properties can be automatically filled from native php array:

Schema:

```php
    /**
     * Auto set properties from associative key-value array (array key are property names).
     *
     * @param array $data
     */
    public function fillFromArray(array $data): void;
```

Example:

```php
$modelData = [
    'id' => 124,
    'year' => 124,
];
$model = new Edition();
$model->fillFromArray($modelData);
```

### getAsArray() method - convert model to native php array

Model can be returned as native array.

Schema:

```php
    /**
     * Get model as native array.
     *
     * @param bool $keysInUnderscoredFormat Whether property names will be converted to underscore format.
     * @param array $excludeKeys List of keys to exclude.
     * @param bool $onlyNullOrScalarValues Whether will be objects and arrays ignored.
     *
     * @return array
     */
    public function getAsArray(bool $keysInUnderscoredFormat = false, array $excludeKeys = [], bool $onlyNullOrScalarValues = false) : array;
```
Examples:

```php
$modelData = [
    'id' => 124,
    'year' => 124,
];
$model = new Edition();
$model->fillFromArray($modelData);

// get as array (default):
$modelData = $model->getAsArray();
/* 
RESULT:
$modelData = [
    'id' => 124,
    'year' => 124,
    'month' => null,
    'pagesCount' => null,
];
*/

// get as array and convert properties to dash_case:
$modelData = $model->getAsArray(true);
/* 
RESULT:
$modelData = [
    'id' => 124,
    'year' => 124,
    'month' => null,
    'pages_count' => null,
];
*/

// get as array and exclude fields [year, month]:
$modelData = $model->getAsArray(false, ['year', 'month']);
/* 
RESULT:
$modelData = [
    'id' => 124,
    'pages_count' => null,
];
*/
```

### getAsStdClass() method - convert model to stdClass

The same functionality as in `getAsArray()` method, but return value is `stdClass`.



### __invoke() method - convert model to native php array or stdClass via magic method

Model can be returned as native array.

Schema:

```php
    /**
     * @param bool $asObject Return as stdClass
     * @param string[] $excludeKeys List of keys to exclude
     *
     * @return array|stdClass
     */
    public function __invoke(bool $asObject = false, array $excludeKeys = []);
```
Examples:

```php
$model = new Edition();
$model->id = 124;

// invoke (default):
$modelData = $model();
/* 
RESULT:
$modelData = [
    'id' => 124,
    'year' => null,
    'month' => null,
    'pagesCount' => null,
];
*/

// invoke exclude fields [year, month]:
$modelData = $model(false, ['year', 'month']);
/* 
RESULT:
$modelData = [
    'id' => 124,
    'pages_count' => null,
];
*/
```


### bindRelations() method - binding data to model by references

Models collections have this functionality: Binding submodels to model by references. We can add binding methods to assocciated collection and then we can call this method `$model->bindRelations([ ... ])` on our model. This method converts model to associated models collection and then are called binding methods on this collection. So collection methods are accessible also for model.

Schema:

```php
    /**
     * Bind relations.
     *
     * This creates new models collection in background, appends model to this collection and call "bindRelations"
     * method on this collection.
     *
     * @param string[] $list List of method names in collection
     *
     * @return mixed
     */
    public function bindRelations(array $list);

```
