<?php
declare(strict_types=1);

namespace ha\Internal\DefaultClass\Model;

/**
 * Model abstraction.
 * - provides access to private/protected properties via getters/setters with implemented type hinting
 * - public properties are denied for strict type mode (internal restriction)
 * - supports access to properties in case insensitive format
 * - supports access to properties in case CamelCase/underscored insensitive format
 * - model can be get in native format (as array or as stdClass)
 */
abstract class ModelDefaultAbstract implements Model
{

    /** @var array */
    private static $getters = [];

    /** @var array */
    private static $propertyToUnderscored = [];

    /** @var array */
    private static $setters = [];

    /** @var array */
    private static $underscoredToProperty = [];

    /**
     * Model constructor.
     * $keyValueDefinition is used for set properties by key-value associative array. Array keys will be property names.
     *
     * @param array $keyValueDefinition
     */
    public function __construct(array $keyValueDefinition = null)
    {
        if ($keyValueDefinition !== null) {
            $this->fillFromArray($keyValueDefinition);
        }
    }

    /** @inheritdoc */
    public function __get($name)
    {
        $method = $this->propertyToGetter($name);
        try {
            return $this->$method();
        } catch (\TypeError $e) {
            throw new \Error($e->getMessage());
        } catch (\Throwable $e) {
            throw new \Error("Property '{$name}' not found in model " . get_class($this));
        }
    }

    /** @inheritdoc */
    public function __invoke(bool $asObject = false, array $excludeKeys = [])
    {
        $data = $this->getAsArray(false, $excludeKeys);
        if (!$asObject) {
            return $data;
        }
        return (object) $data;
    }

    /** @inheritdoc */
    public function __isSet($name): bool
    {
        $method = $this->propertyToGetter($name);
        try {
            $val = $this->$method();
            if (is_null($val)) {
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** @inheritdoc */
    public function __set($name, $value): void
    {
        $method = $this->propertyToSetter($name);
        try {
            $this->$method($value);
        } catch (\TypeError $e) {
            throw new \Error($e->getMessage());
        } catch (\Throwable $e) {
            throw new \Error("Property '{$name}' not found in model " . get_class($this));
        }
    }

    /** @inheritdoc */
    public function createAssociatedCollection(): ModelCollection
    {
        //        return new class extends ModelCollectionDefaultAbstract {
        //
        //
        //        };
        $selfClass = (get_class($this));
        if (!defined("{$selfClass}::COLLECTION_CLASS")) {
            throw new \ErrorException("Class '{$selfClass}' has not defined constant 'COLLECTION_CLASS'");
        }
        $collectionClass = $selfClass::COLLECTION_CLASS;
        /** @var ModelCollection $collection */
        $collection = new $collectionClass;
        return $collection;
    }

    /** @inheritdoc */
    public function fillFromArray(array $data): void
    {
        foreach ($data AS $property => $value) {
            $method = self::propertyToSetter($property);
            $this->$method($value);
        }
    }

    /** @inheritdoc */
    public function getAsArray(bool $keysInUnderscoredFormat = false, array $excludeKeys = [],
        bool $onlyNullOrScalarValues = false
    ): array {
        $allValues = get_object_vars($this);
        if (is_null($allValues)) {
            return [];
        } // if props does not exists

        $excludeKeysRef = [];
        foreach ($excludeKeys AS $key) {
            $excludeKeysRef[] = self::getNormalizedPropertyNameForComparison($key);
        }

        $returnValues = [];
        foreach ($allValues AS $key => $value) {
            $keyRef = self::getNormalizedPropertyNameForComparison($key);
            if (!in_array($keyRef, $excludeKeysRef)) {
                if ($onlyNullOrScalarValues) {
                    if (is_null($value) || is_scalar($value)) {
                        $returnValues[$key] = $value;
                    }
                    continue;
                }
                $returnValues[$key] = $value;
            }
        }

        return $this->getAsArrayHelper($returnValues, $keysInUnderscoredFormat, false);
    }

    /** @inheritdoc */
    public function getAsCollection($keyInCollection = null): ModelCollection
    {
        $collection = $this->createAssociatedCollection();
        if (isset($keyInCollection)) {
            if (!is_string($keyInCollection) && !is_int($keyInCollection)) {
                throw new \TypeError(
                    get_class($this) . '->' . __FUNCTION__ . '() expects $keyInCollection to be string or integer, '
                    . gettype($keyInCollection) . ' given'
                );
            }
            $collection[$keyInCollection] = $this;
        }
        else {
            $collection[] = $this;
        }
        return $collection;
    }

    /** @inheritdoc */
    public function getAsStdClass(bool $keysInUnderscoredFormat = false, array $excludeKeys = [],
        bool $onlyNullOrScalarValues = false
    ): \stdClass {
        $allValues = get_object_vars($this);
        if (is_null($allValues)) {
            return new \stdClass();
        } // if props does not exists

        $excludeKeysRef = [];
        foreach ($excludeKeys AS $key) {
            $excludeKeysRef[] = self::getNormalizedPropertyNameForComparison($key);
        }

        $returnValues = new \stdClass();
        foreach ($allValues AS $key => $value) {
            $keyRef = self::getNormalizedPropertyNameForComparison($key);
            if (!in_array($keyRef, $excludeKeysRef)) {
                if ($onlyNullOrScalarValues) {
                    if (is_null($value) || is_scalar($value)) {
                        $returnValues->$key = $value;
                    }
                    continue;
                }
                $returnValues->$key = $value;
            }
        }
        return $this->getAsArrayHelper($returnValues, $keysInUnderscoredFormat, true);
    }

    /**
     * Convert property name to getter.
     * CamelCase and underscored format will be used, property name is case insensitive.
     *
     * @param string $property
     *
     * @static
     * @return string
     */
    public static function propertyToGetter(string $property): string
    {
        if (!isset(self::$getters[$property])) {
            self::$getters[$property] = 'get' . ucfirst(self::underscoredToProperty($property));
        }
        return self::$getters[$property];
    }

    /**
     * Convert property name to setter.
     * CamelCase and underscored format will be used, property name is case insensitive.
     *
     * @param string $property
     *
     * @static
     * @return string
     */
    public static function propertyToSetter(string $property): string
    {
        if (!isset(self::$setters[$property])) {
            self::$setters[$property] = 'set' . ucfirst(self::underscoredToProperty($property));
        }
        return self::$setters[$property];
    }

    /**
     * @param $sourceArrayOrStd
     * @param bool $keysInUnderscoredFormat
     * @param bool $asStdClass
     *
     * @return array|object
     */
    private function getAsArrayHelper($sourceArrayOrStd, bool $keysInUnderscoredFormat, bool $asStdClass)
    {
        $ret = [];
        $foundKeys = [];
        $hasStringKey = false;
        foreach ($sourceArrayOrStd AS $key => $value) {
            // parse key
            if (is_string($key)) {
                $hasStringKey = true;
                if ($keysInUnderscoredFormat) {
                    $key = self::propertyToUnderscored($key);
                }
            }
            $foundKeys[] = $key;

            // parse value
            if (is_null($value) || is_scalar($value) || is_resource($value)) {
                $ret[$key] = $value;
                continue;
            }
            if ($value instanceof Model) {
                if ($asStdClass) {
                    $ret[$key] = $value->getAsStdClass($keysInUnderscoredFormat);
                }
                else {
                    $ret[$key] = $value->getAsArray($keysInUnderscoredFormat);
                }
                continue;
            }
            if (is_array($value) || $value instanceof ModelCollection) {
                $ret[$key] = (array) $this->getAsArrayHelper($value, $keysInUnderscoredFormat, $asStdClass);
                continue;
            }
            if (is_object($value)) {
                $ret[$key] = $this->getAsArrayHelper($value, $keysInUnderscoredFormat, $asStdClass);
                continue;
            }
        }
        if ($asStdClass === true && $hasStringKey === true) {
            $ret = (object) $ret;
        }

        return $ret;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private static function getNormalizedPropertyNameForComparison(string $key): string
    {
        // TODO apply cache
        return strtolower(preg_replace('/_/', '', $key));
    }

    /**
     * Get property name in underscored format.
     *
     * @param string $property
     *
     * @static
     * @return string
     */
    private static function propertyToUnderscored(string $property): string
    {
        if (!isset(self::$propertyToUnderscored[$property])) {
            preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9]|[_][A-Za-z]+)|[A-Za-z][a-z0-9]+)!', $property, $matches);
            $ret = $matches[0];
            $ret = strtolower(implode('_', $ret));
            self::$propertyToUnderscored[$property] = $ret;
        }
        return self::$propertyToUnderscored[$property];
    }

    /**
     * Get property name in CamelCase format.
     *
     * @param string $property
     *
     * @static
     * @return string
     */
    private static function underscoredToProperty(string $property): string
    {
        if (!isset(self::$underscoredToProperty[$property])) {
            self::$underscoredToProperty[$property] = str_replace('_', '', lcfirst(ucwords($property, '_')));
        }
        return self::$underscoredToProperty[$property];
    }

}