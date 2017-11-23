# Models collections

Collection in *ha* framework is native PHP array wrapped with extra usefull functionality, which speeds code writing and code reusability. Collection also provides better data handling and data validation. This functionality is divided to:

- **data type checking**: before appending value to collection is validated, whether new value implements an interface defined in collection as allowed interface
- **data transformation and modification**: every collection has usefull methods to manipulating properties values on self items
- **data filtering**: every collection has usefull methods to extracting self items from collection by properties values of this items
- **access to native data**: every collection can be converted to native array or only some properties from items can be extracted (e.g. get only *id* property as array from items)
- **data binding**: we can add extra methods - most often for data binding independent from datasource, but you can write some other custom logic for items

### Collections in *ha* framework are divided to two main types:

- [objects collections](#objects-collections) - collections of models which implements the same interface
- [scalar values collections](#collections-for-scalar-values) - collections of scalar values of the same type

### About collections

We can use our collection in many cases as native array, e.g. `foreach`, access by index `$coll[]`, `$coll['x']`, etc. Every collection also has some extra functionality described in this chapter. Some functionality was inspired by other popular frameworks.

Collection must implement interface `ha\Internal\DefaultClass\Model\ModelCollection` and so also implements in background standard interfaces `Iterator`, `ArrayAccess`, `SeekableIterator`, `Serializable`, and `Countable`. Described functionality in this document is provided by model collection abstract `ha\Internal\DefaultClass\Model\ModelCollection\ModelCollectionDefaultAbstract`, so collection must be extended from this abstract class. But you can also use your custom implementation, if you want, only interface must be implemented.

## Objects collections



### Working with collections



#### Converting collections to array

We can use magic method `__invoke()`, which allows calling collection as function. It returns array with items as array or as stdClass. 

```php
// prepare collection
$carBrands = new CarBrands(); // please use factory in real code
// add models
$carBrands[] = new CarBrand(['id' => 20, 'name' => 'Volvo']);
$carBrands[] = new CarBrand(['id' => 94, 'name' => 'Peugeot']);
// convert to array
$carBrandsArr = $carBrands();
$carBrandsObj = $carBrands(true);
```

```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $carBrandsArr                                                                │
└──────────────────────────────────────────────────────────────────────────────┘
array (2) [
    0 => array (2) [
        'id' => integer 20
        'name' => string (5) "Volvo"
    ]
    1 => array (2) [
        'id' => integer 94
        'name' => string (7) "Peugeot"
    ]
]
┌──────────────────────────────────────────────────────────────────────────────┐
│ $carBrandsObj                                                                │
└──────────────────────────────────────────────────────────────────────────────┘
array (2) [
    0 => stdClass (2) (
        public 'id' -> integer 20
        public 'name' -> string (5) "Volvo"
    )
    1 => stdClass (2) (
        public 'id' -> integer 94
        public 'name' -> string (7) "Peugeot"
    )
]
```

#### Converting collections to (JSON) string

Magic method `__toString()` allows converting collection to string. This string is in JSON format. Usefull for logging, dumping data, etc.

```php
// prepare collection
$carBrands = new CarBrands(); // please use factory in real code
// add models
$carBrands[] = new CarBrand(['id' => 20, 'name' => 'Volvo']);
$carBrands[] = new CarBrand(['id' => 94, 'name' => 'Peugeot']);
// convert to string
$carBrandsStr = strval($carBrands);
```

```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $carBrandsStr                                                                │
└──────────────────────────────────────────────────────────────────────────────┘
string (53) "[{"id":20,"name":"Volvo"},{"id":94,"name":"Peugeot"}]"
```

#### How to determine, whether collection contains item with concrete property value

Method `contains()` is usefull to determining, whether collection contains item, which property value is some concrete value. For each item in collection is compared property value and entered value in strict mode.

```php
// prepare collection
$carBrands = new CarBrands(); // please use factory in real code
// add models
$carBrands[] = new CarBrand(['id' => 20, 'name' => 'Volvo']);
$carBrands[] = new CarBrand(['id' => 94, 'name' => 'Peugeot']);
// contains test
$result1 = $carBrands->contains('id', 94);
$result2 = $carBrands->contains('id', 95);
```

```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $result1                                                                     │
└──────────────────────────────────────────────────────────────────────────────┘
boolean true
┌──────────────────────────────────────────────────────────────────────────────┐
│ $result2                                                                     │
└──────────────────────────────────────────────────────────────────────────────┘
boolean false

```

#### How to extract some property from items

Method `extract()` can be used, when we need to get set of values from items property. From each item in collection is exctracted property value and is collected in returned array. Values could be returned as unique (removed duplicitated values) and limit for values in returned array is also supported.

```php
// prepare collection
$carBrands = new CarBrands(); // please use factory in real code
// add models
$carBrands[] = new CarBrand(['id' => 20, 'name' => 'Volvo']);
$carBrands[] = new CarBrand(['id' => 94, 'name' => 'Peugeot']);
// extract
$ids = $carBrands->extract('id');
$names = $carBrands->extract('name');
```

```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $ids                                                                         │
└──────────────────────────────────────────────────────────────────────────────┘
array (2) [
    0 => integer 20
    1 => integer 94
]
┌──────────────────────────────────────────────────────────────────────────────┐
│ $names                                                                       │
└──────────────────────────────────────────────────────────────────────────────┘
array (2) [
    0 => string (5) "Volvo"
    1 => string (7) "Peugeot"
]

```

#### How to filter items in collection

Note: this collection is used in filter examples:

```php
// prepare collection
$carBrands = new CarBrands(); // please use factory in real code
// add models
$carBrands[] = new CarBrand(['id' => 20, 'name' => 'Volvo']);
$carBrands[] = new CarBrand(['id' => 94, 'name' => 'Peugeot']);
```

Method `filter()` returns copy of current collection with all items, for closure function `function($item) { ... }` returns `true`. Limit of filtered items is also supported.

```php
$filtered = $carBrands->filter(function(CarBrand $brand): bool {
    return ($brand->id > 30);
});
```

```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $filtered                                                                    │
└──────────────────────────────────────────────────────────────────────────────┘
Examples\Module\CarModule\Model\CarBrand\CarBrands (1) (
    public 1 -> Examples\Module\CarModule\Model\CarBrand\CarBrand (2) (
        protected 'id' -> integer 94
        protected 'name' -> string (7) "Peugeot"
    )
)
```

Method `reject()` returns copy of current collection without all items, for closure function `function($item) { ... }` returns `true`.

```php
$filtered = $carBrands->reject(function(CarBrand $brand): bool {
    return ($brand->id > 30);
});
```

```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $filtered                                                                    │
└──────────────────────────────────────────────────────────────────────────────┘
Examples\Module\CarModule\Model\CarBrand\CarBrands (1) (
    public 0 -> Examples\Module\CarModule\Model\CarBrand\CarBrand (2) (
        protected 'id' -> integer 20
        protected 'name' -> string (5) "Volvo"
    )
)
```

Method `where()` returns copy of current collection with all items, which property has some value. Limit of filtered items is also supported.

```php
$filtered = $carBrands->where('id', 94);
```

```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $filtered                                                                    │
└──────────────────────────────────────────────────────────────────────────────┘
Examples\Module\CarModule\Model\CarBrand\CarBrands (1) (
    public 1 -> Examples\Module\CarModule\Model\CarBrand\CarBrand (2) (
        protected 'id' -> integer 94
        protected 'name' -> string (7) "Peugeot"
    )
)
```

Method `whereNot()` returns copy of current collection with all items, which property has not some value. Limit of filtered items is also supported.

```php
$filtered = $carBrands->whereNot('id', 94);
```

```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $filtered                                                                    │
└──────────────────────────────────────────────────────────────────────────────┘
Examples\Module\CarModule\Model\CarBrand\CarBrands (1) (
    public 0 -> Examples\Module\CarModule\Model\CarBrand\CarBrand (2) (
        protected 'id' -> integer 20
        protected 'name' -> string (5) "Volvo"
    )
)
```

Method `whereIn()` returns copy of current collection with all items, which property is contained in provided array. Limit of filtered items is also supported.

```php
$filtered = $carBrands->whereIn('id', [94, 95, 96, 97]);
```

```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $filtered                                                                    │
└──────────────────────────────────────────────────────────────────────────────┘
Examples\Module\CarModule\Model\CarBrand\CarBrands (1) (
    public 1 -> Examples\Module\CarModule\Model\CarBrand\CarBrand (2) (
        protected 'id' -> integer 94
        protected 'name' -> string (7) "Peugeot"
    )
)
```
#### How to get first item from collection

Method `first()` provides this funtionality. If collection is empty, a *NotFoundException* is throwed. Therefore *try-catch* block is required.

```php
$model = $carBrands->first();
```

```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $model                                                                       │
└──────────────────────────────────────────────────────────────────────────────┘
Examples\Module\CarModule\Model\CarBrand\CarBrands (1) (
    public 0 -> Examples\Module\CarModule\Model\CarBrand\CarBrand (2) (
        protected 'id' -> integer 20
        protected 'name' -> string (5) "Volvo"
    )
)
```

#### How to determine, whether collection is empty

Method `isEmpty()` provides this funtionality. If collection is empty, method return `true`, otherwise `false` is returned. 

```php
$result = $carBrands->isEmpty();
```

```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $result                                                                      │
└──────────────────────────────────────────────────────────────────────────────┘
boolean false
```

#### How to create empty copy of collection

Method `newSelf()` creates empty collection of the same type as source collection. Factory call is therefore not required in some cases and also `new` keyword. 

```php
$emptyCollection = $carBrands->newSelf();
```

#### How to split collection by value
`splitToGroups()`

#### How to change items index in collection by item property value
`remap()`

> Please use only primary/unique keys as property name for index in this case. Duplications will be overwriten.

#### How to modify collection items


`modify()`
`modifyItemPropertyValue()`


#### Usefull methods for services

Collection is used in many cases in services, when we converting data recieved from datasources to local models. Very usefull are getters and setters for some metadata, data about pagination and methods to determine name of datasource, when collection was created. Here is list of supported getters and setters:

`getAffectedRows()`,
`getMetaData()`,
`getPageNumber()`,
`getPerPageCount()`,
`getProviderName()`,
`getTotalCount()`,
`setAffectedRows()`,
`setMetaData()`,
`setPageNumber()`,
`setPerPageCount()`,
`setProviderName()`,
`setTotalCount()`.


## Data binding principe (ORM based on real objects)

> This principe could be used in many cases and can be used as ORM like principe, which is independent from datasource. Therefore can be used name "ORM based on real objects" for this principe. Perfect is a fact, that this is also independent from relation types such as *1:n*, *n:m*, *1:1*, ... and therefore are not required methods such as *hasOne()*, *hasNamny()*, etc.
> We can use multiple binding variations (in context *A* we can bind only *X* and *Y*, in context *B* we can bind only *Y*, ...). This significantly speeds up app responses.
> This principe also prevents problems with too many queries to datasources - e.g. single call to datasource and not multiple calls in foreach loops. 

#### Case study

- books are stored in MySQL database without authors
- authors are accessible via API from external resource
- book knows as reference only property authorId (this is known reference "book has author")
- in app logic is required this: we need working with books with known authors

Standard ORM based on anotations is dependent on concrete db connection. This can not be used in this case without data sync to local database, but sync is unavailable. This is big problem. But we can use simple and datasource independent principe described in this chapter. We can use models and models collections, which are independent from datasources. When collection will be have some method for loading data by some key in items, we can use in this method some (also external) service, which is not dependent on datasource.

#### Example implementation

We need create a method in our collection for binding concrete data automatically and that is all. In this example will be used relation "bind authors to books":

```php
class Books extends ModelCollectionDefaultAbstract
{
    /** @var string Class name for collection item type validation. */
    protected $allowOnly = Book::class;

    /**
     * Find authors for books in this collection via single call to datasource and bind loaded authors
     * to books by authorId property in books. Method implementation is absolutely independent from
     * target datasources.
     * @return $this
     */
    public function bindAuthors()
    {
        // extract references
        $authorIds = $this->extract('authorId', true);

        // load data by references via some module service
        $authors = main()->modules->userModule->userService()->getUsersByIds($authorIds);

        // bind loaded data to self items
        foreach ($this AS $item) {
            if (!isset($item->authorId)) {
                continue;
            }
            foreach ($authors AS $author) {
                if ($item->authorId === $author->id) {
                    $item->author = $author;
                }
            }
        }

        // enable chaining
        return $this;
    }

}
```

Usage example:

```php
// collection instance
$books = new Books(); // in real usage use factory

// add items to collection (books in this case)
$books[] = ...
$books[] = ...
$books[] = ...

// load relations
$books->bindAuthors()->bindSomeOtherModels1()->bindSomeOtherModels2();

// everything is already loaded for current context without ORM and many dependencies...
```


#### Steps in data binding


##### 1. Extracting references from self items found in current collection


```php
// extract references
$authorIds = []; // or e.g. integers();
foreach ($this AS $item) {
    if (isset($item->authorId)) {
        $authorIds[] = $item->authorId;
    }
}
$authorIds = array_unique($authorIds);
```

This can be writen also as (the same functionality, but code is shorten):

```php
// extract references
$authorIds = $this->extract('authorId', true);
```


##### 2. Loading data by references via service

We can use some service accessible via some module to load values by references. If this service is an application service (IO logic is invisible from this context and is therefore independent from current context), is really simple to load values.

```php
// load data by references
$authors = main()->modules->userModule->userService()->getUsersByIds($authorIds);
```


##### 3. Set loaded data by references to self items in current collection

This is simple mapping. For each book we trying to find author by *authorId* in book and *id* in author. Author is assigned to property *author* in book, if author is found.

```php
foreach ($this AS $item) {
    if (!isset($item->authorId)) {
        continue;
    }
    foreach ($authors AS $author) {
        if ($item->authorId === $author->id) {
            $item->author = $author;
        }
    }
}
```



## Collections for scalar values

Very similary collections are available also for scalar values. These collections are simple simulation of collections/lists in other languages.

Available scalar collections:

```php
$strings = new ha\Internal\DefaultClass\Model\ScalarValues\Strings();
$integers = new ha\Internal\DefaultClass\Model\ScalarValues\Integers();
$floats = new ha\Internal\DefaultClass\Model\ScalarValues\Floats();
$booleans = new ha\Internal\DefaultClass\Model\ScalarValues\Booleans();
```

For creating instances of these collections are accessible also these helpers (result is identical):

```php
$strings = strings();
$integers = integers();
$floats = floats();
$booleans = booleans();
```

### Examples of scalar values colelctions

> In our next examples is used integers collections (functionality of other scalar collections is identical).

Create empty collection:

```php
$integers = integers();
```

Create collection from an array:

```php
$integers = integers([89, 145]);
```
Add item to collection:

```php
$integers[] = 2; // auto index
$integers['x'] = 31245; // defined index
```

```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $integers                                                                    │
└──────────────────────────────────────────────────────────────────────────────┘
ha\Internal\DefaultClass\Model\ScalarValues\Integers (4) (
    public 0 -> integer 89
    public 1 -> integer 145
    public 2 -> integer 2
    public 'x' -> integer 31245
)
```

Create collection from associative array:

```php
$values = [
    'a' => 7,
    'b' => 9,
];
$integers = integers($values);

```
```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $integers                                                                    │
└──────────────────────────────────────────────────────────────────────────────┘
ha\Internal\DefaultClass\Model\ScalarValues\Integers (2) (
    public 'a' -> integer 7
    public 'b' -> integer 9
)
```

Available are also some methods from object collections:

```php
$integers = integers([89, 145]);
$a = $integers->first();
$b = $integers->modify(function (int $value): int {
    return $value * 3;
});

```
```
Dump:
┌──────────────────────────────────────────────────────────────────────────────┐
│ $a                                                                           │
└──────────────────────────────────────────────────────────────────────────────┘
integer 89
┌──────────────────────────────────────────────────────────────────────────────┐
│ $b                                                                           │
└──────────────────────────────────────────────────────────────────────────────┘
ha\Internal\DefaultClass\Model\ScalarValues\Integers (2) (
    public 0 -> integer 267
    public 1 -> integer 435
)
```


