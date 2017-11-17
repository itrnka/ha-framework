<?php
declare(strict_types = 1);

namespace ha\Component\Container\IoC;


abstract class IoCContainerDefaultAbstract implements IoCContainer
{

    /** @var bool */
    protected $locked = false;

    /** @var array Storage */
    protected $objects = [];

    /** @var array */
    protected $configuration;

    /**
     * IoCContainerDefaultAbstract constructor.
     *
     * @param array $configuration Key is className, value is array with configuration
     */
    public function __construct(array $configuration)
    {
        $this->locked = true;
        $this->configuration = $configuration;
    }

    /**
     * Add instance or other value accessible under key.
     *
     * @param string $key
     * @param $value
     *
     * @throws \ErrorException
     */
    final public function __set(string $key, $value) : void
    {
        if (array_key_exists($key, $this->objects) || $this->locked !== false) {
            throw new \ErrorException('Access denied. You could not change property ' . $key);
        }
        $this->objects[$key] = $value;
    }

    /**
     * Get instance or other value by key.
     *
     * @param string $key
     *
     * @return mixed
     * @throws \ErrorException
     */
    final public function __get(string $key)
    {
        if (array_key_exists($key, $this->objects)) {
            return $this->objects[$key];
        }
        throw new \ErrorException('Property ' . $key . ' not found in ' . get_class($this));
    }

    /**
     * Determine whether value exists under key.
     *
     * @param string $key
     *
     * @return bool
     */
    final public function __isSet(string $key) : bool
    {
        return array_key_exists($key, $this->objects);
    }
}