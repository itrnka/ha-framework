<?php
declare(strict_types=1);

namespace ha\Internal\DefaultClass\Model\ScalarValues;

use ha\Internal\Exception\NotFoundException;

abstract class ScalarValues extends \ArrayIterator
{

    /** @var string */
    protected $allowOnly;

    /**
     * ScalarValues constructor.
     *
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        foreach ($array AS $key => $value) {
            $this->typeCheck($key, $value);
        }
        parent::__construct($array);
    }

    /**
     * Get as raw data as array.
     * @return array
     */
    public function __invoke(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Get as string.
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->getArrayCopy());
    }

    /**
     * Returns copy of this collection with all items for which the $function($item) returns true.
     *
     * @param \Closure $filter function ($value, $key) : bool { ... }
     * @param int $limit
     *
     * @return $this
     */
    public function filter(\Closure $filter, int $limit = 0)
    {
        $ret = $this->newSelf();
        $i = 0;
        foreach ($this AS $key => $value) {
            if ($filter($value, $key) === true) {
                $ret[$key] = $value;
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

    /**
     * Returns keys of this collection provided as simple native array.
     * @return array Keys list
     */
    public function getKeys(): array
    {
        return array_keys($this->getArrayCopy());
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
     * @param \Closure $modifier function ($value, $key): $modifiedValue { ... }
     *
     * @return $this
     */
    public function modify(\Closure $modifier)
    {
        foreach ($this AS $key => $value) {
            $this[$key] = $modifier($value, $key);
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
        $new = new $selfClass($array);
        return $new;
    }

    /**
     * offsetSet
     *
     * @param string $offset
     * @param string $value
     */
    public function offsetSet($offset, $value)
    {
        $this->typeCheck($offset, $value);
        parent::offsetSet($offset, $value);
    }

    /**
     * Returns copy of this collection without items for which the $function($item) returns true.
     *
     * @param \Closure $function function ($value, $key) : bool { ... }
     *
     * @return $this
     */
    public function reject(\Closure $function)
    {
        $ret = $this->newSelf();
        foreach ($this AS $key => $value) {
            if ($function($value, $key) !== true) {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    /**
     * typeCheck
     *
     * @param $key
     * @param $value
     *
     * @throws \Error
     * @throws \TypeError
     */
    private function typeCheck($key, $value)
    {
        if (is_string($this->allowOnly)) {
            $fn = 'is_' . $this->allowOnly;
            if (!$fn($value)) {
                throw new \TypeError(
                    get_class($this) . ' expects value to be ' . $this->allowOnly . ', ' . gettype($value) . ' given'
                );
            }
            return;
        }
        throw new \Error('Undefined or invalid $allowOnly property in ' . get_class($this));
    }

}