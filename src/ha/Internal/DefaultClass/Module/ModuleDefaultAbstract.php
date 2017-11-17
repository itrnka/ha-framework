<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Module;


use ha\Component\Configuration\Configuration;

abstract class ModuleDefaultAbstract implements Module
{

    /** @var Configuration  */
    protected $configuration;

    /**
     * Module constructor.
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