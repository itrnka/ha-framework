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

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return json_encode($this->__invoke());
    }

    /**
     * @inheritdoc
     */
    public function contains(string $propertyName, $propertyValue): bool
    {
        foreach ($this AS $key => $obj) {
            if ($obj->$propertyName === $propertyValue) {
                return true;
            }
            continue;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function extract(string $propertyName, bool $unique = true, int $limit = 0): array
    {
        $values = [];
        $i = 0;
        foreach ($this AS $key => $obj) {
            $value = $obj->$propertyName;
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
     * @inheritdoc
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
     * @inheritdoc
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

    /**
     * @inheritdoc
     */
    public function getAffectedRows(): int
    {
        return $this->affectedRows;
    }

    /**
     * @inheritdoc
     */
    public function getKeys(): array
    {
        return array_keys($this->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function getMetaData(): array
    {
        return $this->metaData;
    }

    /**
     * @inheritdoc
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    /**
     * @inheritdoc
     */
    public function getPerPageCount(): int
    {
        return $this->perPageCount;
    }

    /**
     * @inheritdoc
     */
    public function getProviderName(): string
    {
        return strval($this->providerName);
    }

    /**
     * @inheritdoc
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @inheritdoc
     */
    public function isEmpty(): bool
    {
        return ($this->count() === 0);
    }

    /**
     * @inheritdoc
     */
    public function modify(\Closure $modifier)
    {
        foreach ($this AS $key => $object) {
            $modifier($object, $key);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function modifyItemPropertyValue(string $propertyName, $modifier)
    {
        foreach ($this AS $key => $obj) {
            $obj->$propertyName = $modifier($obj->$propertyName);
            continue;
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function newSelf(array $array = [])
    {
        $selfClass = get_class($this);
        $new = new $selfClass($array, $this->getProviderName());
        return $new;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function remap(string $propertyName)
    {
        $ret = $this->newSelf();
        foreach ($this AS $key => $obj) {
            $newKey = $obj->$propertyName;
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

    /**
     * @inheritdoc
     */
    public function setAffectedRows(int $affectedRows)
    {
        $this->affectedRows = $affectedRows;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setMetaData(array $metaData)
    {
        $this->metaData = $metaData;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPageNumber(int $pageNumber)
    {
        $this->pageNumber = $pageNumber;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPerPageCount(int $perPageCount)
    {
        $this->perPageCount = $perPageCount;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setProviderName(string $providerName)
    {
        $this->providerName = $providerName;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTotalCount(int $totalCount)
    {
        $this->totalCount = $totalCount;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function splitToGroups(string $propertyName): array
    {
        $ret = [];
        foreach ($this AS $key => $obj) {
            $newKey = $obj->$propertyName;
            if (!is_string($newKey) && !is_int($newKey)) {
                throw new \TypeError(
                    get_class($this) . '::' . __FUNCTION__ . ' expects $item->$propertyName to be string or integer, '
                    . gettype($newKey) . ' given'
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
     * @inheritdoc
     */
    public function where(string $propertyName, $propertyValue, int $limit = 0)
    {
        $ret = $this->newSelf();
        $i = 0;
        foreach ($this AS $key => $obj) {
            $value = $obj->$propertyName;
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
     * @inheritdoc
     */
    public function whereIn(string $propertyName, array $propertyValues, int $limit = 0)
    {
        $ret = $this->newSelf();
        $i = 0;
        foreach ($this AS $key => $obj) {
            $value = $obj->$propertyName;
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

    /**
     * @inheritdoc
     */
    public function whereNot(string $propertyName, $propertyValue, int $limit = 0)
    {
        $ret = $this->newSelf();
        $i = 0;
        foreach ($this AS $key => $obj) {
            $value = $obj->$propertyName;
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

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->typeCheck($value);
        parent::offsetSet($offset, $value);
    }

    /**
     * Child validator.
     *
     * @param mixed $itemToAppend
     *
     * @throws \Error
     * @throws \TypeError
     */
    protected function typeCheck($itemToAppend)
    {
        // auto create type if type is not set
        if (!isset($this->allowOnly)) {
            if (is_object($itemToAppend)) {
                $this->allowOnly = get_class($itemToAppend);
            } else {
                throw new \TypeError(
                    get_class($this) . '[] expects value to be Model, ' . gettype($itemToAppend) . ' given'
                );
            }
        }
        // validate value
        if (is_string($this->allowOnly)) {
            if (!($itemToAppend instanceof $this->allowOnly)) {
                throw new \TypeError(
                    get_class($this) . ' expects value to be ' . $this->allowOnly . ', ' . gettype($itemToAppend) . ' given'
                );
            }
            return;
        }
        throw new \Error('Undefined or invalid $allowOnly property in ' . get_class($this));
    }
}