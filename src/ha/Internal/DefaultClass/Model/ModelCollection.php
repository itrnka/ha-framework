<?php
declare(strict_types=1);

namespace ha\Internal\DefaultClass\Model;

interface ModelCollection extends \Iterator, /*\Traversable,*/
    \ArrayAccess, \SeekableIterator, \Serializable, \Countable
{

    /**
     * Get as raw data as array.
     *
     * @param bool $itemAsObject Return items as object (=true) or as array (=false)
     *
     * @return array
     */
    public function __invoke(bool $itemAsObject = false): array;

    /**
     * Get as string.
     * @return string
     */
    public function __toString(): string;

    /**
     * Determine whether this collection contains object with $propertyName === $propertyValue.
     *
     * @param string $propertyName
     * @param $propertyValue
     *
     * @return bool Property value found in collection item
     */
    public function contains(string $propertyName, $propertyValue): bool;

    /**
     * Extract property value from every item and return this extracted values as array.
     *
     * @param string $propertyName
     * @param bool $unique
     * @param int $limit
     *
     * @return array
     */
    public function extract(string $propertyName, bool $unique = true, int $limit = 0): array;

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
     * Get first child.
     * @return mixed
     */
    public function first();

    /**
     * Returns meta data provided by service.
     * @return int
     */
    public function getAffectedRows(): int;

    /**
     * Returns keys of this collection provided as simple native array.
     * @return string[]|int[] Keys list
     */
    public function getKeys(): array;

    /**
     * Returns meta data provided by service.
     * @return array
     */
    public function getMetaData(): array;

    /**
     * Returns meta data provided by service.
     * @return int
     */
    public function getPageNumber(): int;

    /**
     * Returns meta data provided by service.
     * @return int
     */
    public function getPerPageCount(): int;

    /**
     * Get name of instance creator (e.g. datasource driver name).
     * @return string
     */
    public function getProviderName();

    /**
     * Returns meta data provided by service.
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * Determine whether this collection is empty (count = 0).
     * @return bool Collection is empty
     */
    public function isEmpty(): bool;

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
     * Returns copy of this collection in which are keys provided as value from property $propertyName
     *
     * @param string $propertyName
     *
     * @return $this
     * @throws \Error When the value is not a string or integer
     */
    public function remap(string $propertyName);

    /**
     * Set meta data provided by service.
     *
     * @param int $affectedRows
     *
     * @return $this
     */
    public function setAffectedRows(int $affectedRows);

    /**
     * Set meta data provided by service.
     *
     * @param array $metaData
     *
     * @return $this
     */
    public function setMetaData(array $metaData);

    /**
     * Set meta data provided by service.
     *
     * @param int $pageNumber
     *
     * @return $this
     */
    public function setPageNumber(int $pageNumber);

    /**
     * Set meta data provided by service.
     *
     * @param int $perPageCount
     *
     * @return $this
     */
    public function setPerPageCount(int $perPageCount);

    /**
     * Set meta data provided by service.
     *
     * @param string $providerName
     *
     * @return $this
     */
    public function setProviderName(string $providerName);

    /**
     * Set meta data provided by service.
     *
     * @param int $totalCount
     *
     * @return $this
     */
    public function setTotalCount(int $totalCount);

    /**
     * Split this collection to arrays by property value.
     *
     * @param string $propertyName
     *
     * @return array
     * @throws \TypeError When the value is not a string or integer
     */
    public function splitToGroups(string $propertyName): array;

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
     * Returns copy of this collection with all items for which $propertyName!=$propertyValue.
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @param int $limit
     *
     * @return $this
     */
    public function whereNot(string $propertyName, $propertyValue, int $limit = 0);

}