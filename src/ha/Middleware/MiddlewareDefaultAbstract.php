<?php
declare(strict_types = 1);

namespace ha\Middleware;

use ha\Component\Configuration\Configuration;

abstract class MiddlewareDefaultAbstract implements Middleware
{

    /** @var Configuration */
    protected $configuration;

    /**
     * MiddlewareDefaultAbstract constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get value from internal configuration by key.
     *
     * @param string $key
     *
     * @return mixed
     */
    final public function cfg(string $key)
    {
        return $this->configuration->get($key);
    }

    /**
     * Get instance name.
     *
     * @return string
     */
    final public function name() : string
    {
        return $this->cfg('name');
    }

}