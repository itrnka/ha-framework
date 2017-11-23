<?php
declare(strict_types=1);

namespace ha\Internal\DefaultClass\Model;

interface Model
{

    /**
     * Magic method for get $value = $this->$name.
     * $name is case insensitive and CamelCase/undersored format insensitive.
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name);

    /**
     * __invoke
     *
     * @param bool $asObject
     * @param array $excludeKeys
     *
     * @return mixed
     */
    public function __invoke(bool $asObject = false, array $excludeKeys = []);

    /**
     * Magic method for isset($this->{name}).
     * $name is case insensitive and CamelCase/undersored format insensitive.
     *
     * @param $name
     *
     * @return bool
     */
    public function __isSet($name): bool;

    /**
     * Magic method for set $this->$name = $value.
     * $name is case insensitive and CamelCase/undersored format insensitive.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value): void;

    /**
     * Creates new collection associated to this model.
     * @return \ha\Internal\DefaultClass\Model\ModelCollection
     */
    public function createAssociatedCollection(): ModelCollection;

    /**
     * Auto set properties from associative key-value array (array key are property names).
     *
     * @param array $data
     */
    public function fillFromArray(array $data): void;

    /**
     * Get model as native array.
     *
     * @param bool $keysInUnderscoredFormat Whether property names will be converted to underscore format.
     * @param array $excludeKeys List of keys to exclude.
     * @param bool $onlyNullOrScalarValues Whether will be objects and arrays ignored.
     *
     * @return array
     */
    public function getAsArray(bool $keysInUnderscoredFormat = false, array $excludeKeys = [],
        bool $onlyNullOrScalarValues = false
    ): array;

    /**
     * Creates new collection associated to this model, appends $this to created collection and returns created
     * collection.
     *
     * @param string|int $keyInCollection
     *
     * @return \ha\Internal\DefaultClass\Model\ModelCollection
     */
    public function getAsCollection($keyInCollection = null): ModelCollection;

    /**
     * Convert model to stdClass.
     *
     * @param bool $keysInUnderscoredFormat Whether property names will be converted to underscore format.
     * @param array $excludeKeys List of keys to exclude.
     * @param bool $onlyNullOrScalarValues Whether will be objects and arrays ignored.
     *
     * @return \stdClass
     */
    public function getAsStdClass(bool $keysInUnderscoredFormat = false, array $excludeKeys = [],
        bool $onlyNullOrScalarValues = false
    ): \stdClass;

}