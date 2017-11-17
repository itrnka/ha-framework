<?php
declare(strict_types=1);

namespace ha\Internal\DefaultClass\Model;

use ha\Internal\Exception\NotFoundException;

abstract class ModelCollectionDefaultAbstract extends \ArrayIterator implements ModelCollection
{
    /** @var int */
    protected $affectedRows = -1;

    /** @var string Allow appending only instances with  this className */
    protected $allowOnly;

    /** @var array */
    protected $metaData = [];

    /** @var int */
    protected $pageNumber = -1;

    /** @var int */
    protected $perPageCount = -1;

    /** @var string */
    protected $providerName = '';

    /** @var int */
    protected $totalCount = -1;

    /**
     * ModelCollection constructor.
     *
     * @param array $array Items of this array could be key-value associative arrays with definitions or model
     *     instances.
     * @param string|null $providerName
     */
    public function __construct(array $array = [], string $providerName = null)
    {
        $this->providerName = $providerName;
        foreach ($array AS $key => $value) {
            $this->typeCheck($key, $value);
        }
        parent::__construct($array);
    }

    public function __invoke(bool $itemAsObject = false): array
    {
        $ret = [];
        foreach ($this AS $key => $obj) {
            if ($obj instanceof \stdClass) {
                if (!$itemAsObject) {
                    $obj = (array) $obj;
                }
                $ret[$key] = $obj;
            }
            else {
                $ret[$key] = $obj($itemAsObject);
            }
        }
        return $ret;
    }

    public function __toString(): string
    {
        return json_encode($this->__invoke());
    }

    public function bindRelations(array $list)
    {
        foreach ($list AS $method) {
            $this->$method();
        }
        return $this;
    }

    /**
     * Determine whether this collection contains object with $propertyName === $propertyValue.
     *
     * @param string $propertyName
     * @param $propertyValue
     *
     * @return bool Property value found in collection item
     */
    public function contains(string $propertyName, $propertyValue): bool
    {
        $method = $this->getObjectPropertyGetterMethod($propertyName);
        foreach ($this AS $key => $obj) {
            if (is_null($method) && property_exists($obj, $propertyName)) {
                if ($obj->$propertyName === $propertyValue) {
                    return true;
                }
                continue;
            }
            if ($obj->$method() === $propertyValue) {
                return true;
            }
        }
        return false;
    }

    /**
     * Extract property values by key and return it as array.
     *
     * @param string $propertyName
     * @param bool $unique
     * @param int $limit
     *
     * @return array
     */
    public function extract(string $propertyName, bool $unique = true, int $limit = 0): array
    {
        $method = $this->getObjectPropertyGetterMethod($propertyName);
        $values = [];
        $i = 0;
        foreach ($this AS $key => $obj) {
            if (is_null($method) && property_exists($obj, $propertyName)) {
                $value = $obj->$propertyName;
            }
            else {
                $value = $obj->$method();
            }
            if ($unique) {
                if (!in_array($value, $values)) {
                    $values[$key] = $value;
                    $i++;
                    if ($limit === $i) {
                        break;
                    }
                }
            }
            else {
                $values[$key] = $value;
                $i++;
                if ($limit === $i) {
                    break;
                }
            }
        }
        return $values;
    }

    /**
     * Returns copy of this collection with all items for which the $function($item) returns true.
     *
     * @param \Closure $filter function ($item, $key) : bool { ... }
     * @param int $limit
     *
     * @return $this
     */
    public function filter(\Closure $filter, int $limit = 0)
    {
        $ret = $this->newSelf();
        $i = 0;
        foreach ($this AS $key => $obj) {
            if ($filter($obj, $key) === true) {
                $ret[$key] = $obj;
                $i++;
                if ($limit === $i) {
                    break;
                }
            }
        }
        return $ret;
    }

    /**
     * Get first child.
     * @return mixed
     * @throws \ha\Internal\Exception\NotFoundException
     */
    public function first()
    {
        $this->rewind();
        $current = $this->current();
        if (!isset($current)) {
            throw new NotFoundException();
        }
        return $current;
    }

    public function getAffectedRows(): int
    {
        return $this->affectedRows;
    }

    /**
     * Returns keys of this collection provided as simple native array.
     * @return array Keys list
     */
    public function getKeys(): array
    {
        return array_keys($this->getArrayCopy());
    }

    public function getMetaData(): array
    {
        return $this->metaData;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function getPerPageCount(): int
    {
        return $this->perPageCount;
    }

    public function getProviderName(): string
    {
        return strval($this->providerName);
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * Determine whether this collection is empty (count = 0).
     * @return bool Collection is empty
     */
    public function isEmpty(): bool
    {
        return ($this->count() === 0);
    }

    /**
     * Modify every item in collection by custom function.
     *
     * @param \Closure $modifier function ($item, $key) : void { ... }
     *
     * @return $this
     */
    public function modify(\Closure $modifier)
    {
        foreach ($this AS $key => $object) {
            $modifier($object, $key);
        }
        return $this;
    }

    /**
     * Function modifyItemPropertyValue
     *
     * @param string $propertyName
     * @param \Closure|string $modifier $modifier function ($value) : modifiedValue { ... } OR intval, strval, ...
     *
     * @return $this
     */
    public function modifyItemPropertyValue(string $propertyName, $modifier)
    {
        $method = $this->getObjectPropertyGetterMethod($propertyName);
        $setter = $this->getObjectPropertySetterMethod($propertyName);
        foreach ($this AS $key => $obj) {
            if (is_null($method) && property_exists($obj, $propertyName)) {
                $obj->$propertyName = $modifier($obj->$propertyName);
                continue;
            }
            $obj->$setter($modifier($obj->$method()));
        }
        return $this;
    }

    /**
     * Create new self instance.
     *
     * @param array $array
     *
     * @return $this
     */
    public function newSelf(array $array = [])
    {
        $selfClass = get_class($this);
        $new = new $selfClass($array, $this->getProviderName());
        return $new;
    }

    /**
     * Returns copy of this collection without items for which the $function($item) returns true.
     *
     * @param \Closure $function function ($item, $key) : bool { ... }
     *
     * @return $this
     */
    public function reject(\Closure $function)
    {
        $ret = $this->newSelf();
        foreach ($this AS $key => $object) {
            if ($function($object, $key) !== true) {
                $ret[$key] = $object;
            }
        }
        return $ret;
    }

    /**
     * Returns copy of this collection in which are keys provided as value from property $propertyName
     *
     * @param string $propertyName
     *
     * @return $this
     * @throws \Error When the value is not a string or integer
     */
    public function remap(string $propertyName)
    {
        $method = $this->getObjectPropertyGetterMethod($propertyName);
        $ret = $this->newSelf();
        foreach ($this AS $key => $obj) {
            if (is_null($method) && property_exists($obj, $propertyName)) {
                $newKey = $obj->$propertyName;
            }
            else {
                $newKey = $obj->$method();
            }
            if (!is_string($newKey) && !is_int($newKey)) {
                throw new \TypeError(
                    get_class($this) . '::' . __FUNCTION__ . ' expects key to be string or integer, ' . gettype($newKey)
                    . ' given'
                );
            }
            if (isSet($ret[$newKey])) {
                throw new \Error('Key "' . $newKey . '" already exists in collection ' . get_class($this));
            }
            $ret[$newKey] = $obj;
        }
        return $ret;
    }

    public function setAffectedRows(int $affectedRows)
    {
        $this->affectedRows = $affectedRows;
        return $this;
    }

    public function setMetaData(array $metaData)
    {
        $this->metaData = $metaData;
        return $this;
    }

    public function setPageNumber(int $pageNumber)
    {
        $this->pageNumber = $pageNumber;
        return $this;
    }

    public function setPerPageCount(int $perPageCount)
    {
        $this->perPageCount = $perPageCount;
        return $this;
    }

    public function setProviderName(string $providerName)
    {
        $this->providerName = $providerName;
        return $this;
    }

    public function setTotalCount(int $totalCount)
    {
        $this->totalCount = $totalCount;
        return $this;
    }

    /**
     * Split this collection to arrays by property value.
     *
     * @param string $propertyName
     *
     * @return array
     * @throws \TypeError When the value is not a string or integer
     */
    public function splitToGroups(string $propertyName): array
    {
        $method = $this->getObjectPropertyGetterMethod($propertyName);
        $ret = [];
        foreach ($this AS $key => $obj) {
            if (is_null($method) && property_exists($obj, $propertyName)) {
                $newKey = $obj->$propertyName;
            }
            else {
                $newKey = $obj->$method();
            }
            if (!is_string($newKey) && !is_int($newKey)) {
                throw new \TypeError(
                    get_class($this) . '::' . __FUNCTION__ . ' expects key to be string or integer, ' . gettype($newKey)
                    . ' given'
                );
            }
            if (!isSet($ret[$newKey])) {
                $ret[$newKey] = [];
            }
            $ret[$newKey][] = $obj;
        }
        return $ret;
    }

    /**
     * Returns copy of this collection with all items for which $propertyName=$propertyValue.
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @param int $limit
     *
     * @return $this
     */
    public function where(string $propertyName, $propertyValue, int $limit = 0)
    {
        $method = $this->getObjectPropertyGetterMethod($propertyName);
        $ret = $this->newSelf();
        $i = 0;
        foreach ($this AS $key => $obj) {
            if (is_null($method) && property_exists($obj, $propertyName)) {
                $value = $obj->$propertyName;
            }
            else {
                $value = $obj->$method();
            }
            if ($value === $propertyValue) {
                $ret[$key] = $obj;
                $i++;
                if ($limit === $i) {
                    break;
                }
            }
        }
        return $ret;
    }

    /**
     * Returns copy of this collection with all items for which in_array($propertyName, $propertyValue, true) === true.
     *
     * @param string $propertyName
     * @param array $propertyValues
     * @param int $limit
     *
     * @return $this
     */
    public function whereIn(string $propertyName, array $propertyValues, int $limit = 0)
    {
        $method = $this->getObjectPropertyGetterMethod($propertyName);
        $ret = $this->newSelf();
        $i = 0;
        foreach ($this AS $key => $obj) {
            if (is_null($method) && property_exists($obj, $propertyName)) {
                $value = $obj->$propertyName;
            }
            else {
                $value = $obj->$method();
            }
            if (in_array($value, $propertyValues, true)) {
                $ret[$key] = $obj;
                $i++;
                if ($limit === $i) {
                    break;
                }
            }
        }
        return $ret;
    }

    public function offsetSet($offset, $value)
    {
        $this->typeCheck($offset, $value);
        parent::offsetSet($offset, $value);
    }

    /**
     * Returns copy of this collection with all items for which $propertyName!=$propertyValue.
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @param int $limit
     *
     * @return $this
     */
    public function whereNot(string $propertyName, $propertyValue, int $limit = 0)
    {
        $method = $this->getObjectPropertyGetterMethod($propertyName);
        $ret = $this->newSelf();
        $i = 0;
        foreach ($this AS $key => $obj) {
            if (is_null($method) && property_exists($obj, $propertyName)) {
                $value = $obj->$propertyName;
            }
            else {
                $value = $obj->$method();
            }
            if ($value !== $propertyValue) {
                $ret[$key] = $obj;
                $i++;
                if ($limit === $i) {
                    break;
                }
            }
        }
        return $ret;
    }

    private function getObjectPropertyGetterMethod(string $propertyName)
    {
        $modelClass = $this->allowOnly;
        if ($modelClass === \stdClass::class) {
            return null;
        }
        return $modelClass::propertyToGetter($propertyName);
    }

    private function getObjectPropertySetterMethod(string $propertyName)
    {
        $modelClass = $this->allowOnly;
        if ($modelClass === \stdClass::class) {
            return null;
        }
        return $modelClass::propertyToSetter($propertyName);
    }

    /**
     * Child validator.
     *
     * @param $key
     * @param $value
     *
     * @throws \Error
     * @throws \TypeError
     */
    protected function typeCheck($key, $value)
    {
        if (is_string($this->allowOnly)) {
            if (!($value instanceof $this->allowOnly)) {
                throw new \TypeError(
                    get_class($this) . ' expects value to be ' . $this->allowOnly . ', ' . gettype($value) . ' given'
                );
            }
            return;
        }
        throw new \Error('Undefined or invalid $allowOnly property in ' . get_class($this));
    }
}