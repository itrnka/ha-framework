<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Module;


use ha\Component\Configuration\Configuration;

interface Module
{

    /**
     * Module constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration);

    /**
     * Get value from internal configuration by key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function cfg(string $key);

    /**
     * Get instance name.
     *
     * @return string
     */
    public function name() : string;

}