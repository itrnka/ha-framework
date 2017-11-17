<?php
declare(strict_types = 1);

namespace ha\Component\Container\IoC;


interface IoCContainer
{

    /**
     * IoCContainer constructor.
     *
     * @param array $configuration Key is className, value is array with configuration
     */
    public function __construct(array $configuration);

    /**
     * Add instance or other value accessible under key.
     *
     * @param string $key
     * @param $value
     */
    public function __set(string $key, $value) : void;

    /**
     * Get instance or other value by key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key);

    /**
     * Determine whether value exists under key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isSet(string $key) : bool;

}