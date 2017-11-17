<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Model;


interface Model
{

    #public function __construct(array $data = []);

    /**
     * Magic method for get $value = $this->$name.
     *
     * $name is case insensitive and CamelCase/undersored format insensitive.
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name);

    /**
     * Magic method for set $this->$name = $value.
     *
     * $name is case insensitive and CamelCase/undersored format insensitive.
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value) : void;

    /**
     * Magic method for isset($this->{name}).
     *
     * $name is case insensitive and CamelCase/undersored format insensitive.
     *
     * @param $name
     *
     * @return bool
     */
    public function __isSet($name) : bool;

    /**
     * Get property name in CamelCase format.
     *
     * @param string $property
     *
     * @static
     * @return string
     */
    public static function underscoredToProperty(string $property) : string;

    /**
     * Get property name in underscored format.
     *
     * @param string $property
     *
     * @static
     * @return string
     */
    public static function propertyToUnderscored(string $property) : string;

    /**
     * Convert property name to getter.
     *
     * CamelCase and underscored format will be used, property name is case insensitive.
     *
     * @param string $property
     *
     * @static
     * @return string
     */
    public static function propertyToGetter(string $property) : string;

    /**
     * Convert property name to setter.
     *
     * CamelCase and underscored format will be used, property name is case insensitive.
     *
     * @param string $property
     *
     * @static
     * @return string
     */
    public static function propertyToSetter(string $property) : string;

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
     * Get model as native array.
     *
     * @param bool $keysInUnderscoredFormat Whether property names will be converted to underscore format.
     * @param array $excludeKeys List of keys to exclude.
     * @param bool $onlyNullOrScalarValues Whether will be objects and arrays ignored.
     *
     * @return array
     */
    public function getAsArray(bool $keysInUnderscoredFormat = false, array $excludeKeys = [], bool $onlyNullOrScalarValues = false) : array;

    /**
     * Convert model to stdClass.
     *
     * @param bool $keysInUnderscoredFormat Whether property names will be converted to underscore format.
     * @param array $excludeKeys List of keys to exclude.
     * @param bool $onlyNullOrScalarValues Whether will be objects and arrays ignored.
     *
     * @return \stdClass
     */
    public function getAsStdClass(bool $keysInUnderscoredFormat = false, array $excludeKeys = [], bool $onlyNullOrScalarValues = false) : \stdClass;

    /**
     * Auto set properties from associative key-value array (array key are property names).
     *
     * @param array $data
     */
    public function fillFromArray(array $data): void;


    /**
     * Bind relations.
     *
     * This creates new model collection in background, appends model to this collection and call "bindRelations"
     * method on this collection.
     *
     * @param array $list
     *
     * @return mixed
     */
    public function bindRelations(array $list);

}