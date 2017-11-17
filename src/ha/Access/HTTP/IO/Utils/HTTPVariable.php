<?php
declare(strict_types=1);

namespace ha\Access\HTTP\IO\Utils;

use ha\Internal\DefaultClass\Model\ScalarValues\Strings;

class HTTPVariable
{

    public function mustBeSet(array $source, string $key) : void
    {
        if (!array_key_exists($key, $source)) {
            throw new \ArgumentCountError("Array key '{$key}' does not exists");
        }
    }

    public function mustBeSetMulti(array $source, Strings $keys) : void
    {

    }

    public function getAsInt(array $source, string $key) : int
    {
        $this->mustBeSet($source, $key);
        $source[$key] = trim($source[$key]);
        if (!preg_match('/^0$|^[-]?[1-9][0-9]*$/', $source[$key])) {
            throw new \TypeError("Value of '{$key}' is not a string in integer format.");
        }
        return intval($source[$key]);
    }

    public function getAsFloat(array $source, string $key) : float
    {
        $this->mustBeSet($source, $key);
        $source[$key] = trim($source[$key]);
        if (!preg_match('/^-?(?:\d+|\d*\.\d+)$/', $source[$key])) {
            throw new \TypeError("Value of '{$key}' is not a string in float format.");
        }
        return floatval($source[$key]);
    }


    public function getAsIntIfExists(array $source, string $key, $default = null)
    {
        if (!array_key_exists($key, $source) || trim($source[$key]) === '') {
            return $default;
        }
        return $this->getAsInt($source, $key);
    }

    public function getAsFloatIfExists(array $source, string $key, $default = null)
    {
        if (!array_key_exists($key, $source) || trim($source[$key]) === '') {
            return $default;
        }
        return $this->getAsFloat($source, $key);
    }

}