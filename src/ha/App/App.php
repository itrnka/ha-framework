<?php
declare(strict_types = 1);

namespace ha\App;

use ha\Component\Configuration\Configuration;
use ha\Component\Container\IoC\IoCContainer;


/**
 * Interface App.
 *
 * Application root container which provides access to middleware and modules.
 *
 * @property-read float $scriptStartTime
 * @property-read string $environmentName
 * @property-read \ha\Component\Configuration\Configuration $appConfiguration
 * @property-read array $supportedCharsets
 * @property-read IoCContainer $middleware
 * @property-read IoCContainer $modules
 */
interface App
{

    /**
     * AppDefault constructor.
     *
     * @param string $environmentName
     * @param \ha\Component\Configuration\Configuration $cfg
     * @param IoCContainer $modules
     * @param IoCContainer $middleware
     */
    public function __construct(string $environmentName, Configuration $cfg, IoCContainer $modules, IoCContainer $middleware);

    /**
     * Set script start time. Useful for process length measuring.
     *
     * @param float $start Unix timestamp with miliseconds
     */
    public function setScriptStartTime(float $start) : void;

    /**
     * Get configuration variable by $key from main configuration.
     *
     * @param string $key
     * @param bool $throwException
     *
     * @return mixed
     */
    public function cfg($key,  bool $throwException = true);

    /**
     * Determine whether App supports charset by name.
     *
     * @param string $charset
     *
     * @return bool
     */
    public function supportsCharset(string $charset) : bool;

}