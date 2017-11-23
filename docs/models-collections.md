# Models collections

Collection in *ha* framework is native PHP array wrapped with extra usefull functionality, which speeds code writing and code reusability. Collection also provides better data handling and data validation. This functionality is divided to:

- **data type checking**: before appending value to collection is validated, whether new value implements an interface defined in collection as allowed interface
- **data transformation**: every collection has usefull methods to manipulating properties values on self items
- **data filtering**: every collection has usefull methods to extracting self items from collection by properties values of this items
- **access to native data**: every collection can be converted to native array
- **data binding**: we can add extra methods - most often for data binding independent from datasource, but you can write some other custom logic for items

### Collections in *ha* framework are divided to two main types:

- [objects collections](#objects-collections) - collections of objects which implements the same interface
- [scalar values collections](#collections-for-scalar-values) - collections of values of the same type

## Objects collections




## Data binding principe (ORM based on real objects)

> This principe could be used in many cases and can be used as ORM like principe, which is independent from datasource. Therefore can be used name "ORM based on real objects" for this principe.
> We can use multiple variations (in context *A* we can bind only *X* and *Y*, in context *B* we can bind only *Y*, ...). This significantly speeds up app responses.
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


