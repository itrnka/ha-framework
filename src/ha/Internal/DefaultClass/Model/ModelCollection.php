<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Model;


interface ModelCollection extends \Iterator, /*\Traversable,*/ \ArrayAccess, \SeekableIterator, \Serializable, \Countable
{

    /**
     * Create new self instance.
     *
     * @param array $array
     *
     * @return $this
     */
    public function newSelf(array $array = []);

    /**
     * Returns copy of this collection without items for which the $function($item) returns true.
     *
     * @param \Closure $function function ($item, $key) : bool { ... }
     *
     * @return $this
     */
    public function reject(\Closure $function);

    /**
     * Returns copy of this collection with all items for which the $function($item) returns true.
     *
     * @param \Closure $filter function ($item, $key) : bool { ... }
     * @param int $limit
     *
     * @return $this
     */
    public function filter(\Closure $filter, int $limit = 0);

    /**
     * Modify every item in collection by custom function.
     *
     * @param \Closure $modifier function ($item, $key) : void { ... }
     *
     * @return $this
     */
    public function modify(\Closure $modifier);

    /**
     * Function modifyItemPropertyValue
     *
     * @param string $propertyName
     * @param \Closure|string $modifier $modifier function ($value) : modifiedValue { ... } OR intval, strval, ...
     *
     * @return $this
     */
    public function modifyItemPropertyValue(string $propertyName, $modifier);

    /**
     * Determine whether this collection contains object with $propertyName === $propertyValue.
     *
     * @param string $propertyName
     * @param $propertyValue
     *
     * @return bool Property value found in collection item
     */
    public function contains(string $propertyName, $propertyValue) : bool;

    /**
     * Returns keys of this collection provided as simple native array.
     *
     * @return array Keys list
     */
    public function getKeys() : array;

    /**
     * Determine whether this collection is empty (count = 0).
     *
     * @return bool Collection is empty
     */
    public function isEmpty() : bool;

    /**
     * Returns copy of this collection in which are keys provided as value from property $propertyName
     *
     * @param string $propertyName
     *
     * @return $this
     * @throws \Error When the value is not a string or integer
     */
    public function remap(string $propertyName);

    /**
     * Split this collection to arrays by property value.
     *
     * @param string $propertyName
     *
     * @return array
     * @throws \TypeError When the value is not a string or integer
     */
    public function splitToGroups(string $propertyName) : array;

    /**
     * Returns copy of this collection with all items for which $propertyName=$propertyValue.
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @param int $limit
     *
     * @return $this
     */
    public function where(string $propertyName, $propertyValue, int $limit = 0);

    /**
     * Returns copy of this collection with all items for which in_array($propertyName, $propertyValue, true) === true.
     *
     * @param string $propertyName
     * @param array $propertyValues
     * @param int $limit
     *
     * @return $this
     */
    public function whereIn(string $propertyName, array $propertyValues, int $limit = 0);

    /**
     * Extract property value from every item and return this extracted values as array.
     *
     * @param string $propertyName
     * @param bool $unique
     * @param int $limit
     *
     * @return array
     */
    public function extract(string $propertyName, bool $unique = true, int $limit = 0) : array;

    /**
     * Get first child.
     *
     * @return mixed
     */
    public function first();

    /**
     * Get as raw data as array.
     *
     * @param bool $itemAsObject Return items as object (=true) or as array (=false)
     *
     * @return array
     */
    public function __invoke(bool $itemAsObject = false) : array;

    /**
     * Get as string.
     *
     * @return string
     */
    public function __toString() : string;


    /**
     * Returns meta data provided by service.
     *
     * @return array
     */
    public function getMetaData() : array;


    /**
     * Set meta data provided by service.
     *
     * @param array $metaData
     *
     * @return $this
     */
    public function setMetaData(array $metaData);

    /**
     * Returns meta data provided by service.
     *
     * @return int
     */
    public function getPageNumber() : int;

    /**
     * Set meta data provided by service.
     *
     * @param int $pageNumber
     *
     * @return $this
     */
    public function setPageNumber(int $pageNumber);

    /**
     * Returns meta data provided by service.
     *
     * @return int
     */
    public function getTotalCount() : int;

    /**
     * Set meta data provided by service.
     *
     * @param int $totalCount
     *
     * @return $this
     */
    public function setTotalCount(int $totalCount);

    /**
     * Returns meta data provided by service.
     *
     * @return int
     */
    public function getPerPageCount() : int;

    /**
     * Set meta data provided by service.
     *
     * @param int $perPageCount
     *
     * @return $this
     */
    public function setPerPageCount(int $perPageCount);

    /**
     * Get name of instance creator (e.g. datasource driver name).
     * @return string
     */
    public function getProviderName();

    /**
     * Set meta data provided by service.
     *
     * @param string $providerName
     *
     * @return $this
     */
    public function setProviderName(string $providerName);

    /**
     * Returns meta data provided by service.
     *
     * @return int
     */
    public function getAffectedRows() : int;

    /**
     * Set meta data provided by service.
     *
     * @param int $affectedRows
     *
     * @return $this
     */
    public function setAffectedRows(int $affectedRows);

    /**
     * Call list of methods for binding data.
     *
     * Cals $this->{$list[0]}, $this->{$list[1]}, ...
     *
     * @param string[] $list [method1, method2, ...]
     *
     * @return $this
     *
     * @throws \ErrorException
     */
    public function bindRelations(array $list);

}