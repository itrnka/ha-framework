<?php
declare(strict_types = 1);

namespace ha\App;

use ha\Component\Configuration\Configuration;
use ha\Component\Container\IoC\IoCContainer;

/**
 * Class App.
 *
 * Default implementation of App interface.
 *
 * @property-read float $scriptStartTime
 * @property-read string $environmentName
 * @property-read \ha\Component\Configuration\Configuration $appConfiguration
 * @property-read array $supportedCharsets
 * @property-read IoCContainer $middleware
 * @property-read IoCContainer $modules
 */
class AppDefault implements App
{

    /** @var float */
    private $scriptStartTime;

    /** @var string */
    private $environmentName;

    /** @var Configuration $appConfiguration Instance with configuration. */
    private $appConfiguration;

    /** @var  array Cache */
    private $supportedCharsets;

    /** @var IoCContainer */
    private $middleware;

    /** @var IoCContainer */
    private $modules;

    /**
     * AppDefault constructor.
     *
     * @param string $environmentName
     * @param \ha\Component\Configuration\Configuration $cfg
     * @param IoCContainer $modules
     * @param IoCContainer $middleware
     */
    public function __construct(string $environmentName, Configuration $cfg, IoCContainer $modules, IoCContainer $middleware)
    {
        $this->setScriptStartTime(microtime(true)); // you can override this with real value in your script
        $this->environmentName = $environmentName;
        $this->appConfiguration = $cfg;
        $this->modules = $modules;
        $this->middleware = $middleware;
        $this->supportedCharsets = mb_list_encodings();
    }

    /**
     * Readonly accessor to declared properties.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->$name;
    }

    /**
     * Set script start time. Useful for process length measuring.
     *
     * @param float $start Unix timestamp with miliseconds
     */
    public function setScriptStartTime(float $start) : void
    {
        $this->scriptStartTime = $start;
    }

    /**
     * Get configuration variable by $key from main configuration.
     *
     * @param string $key
     * @param bool $throwException
     *
     * @return mixed
     */
    public function cfg($key, bool $throwException = true)
    {
        return $this->appConfiguration->get($key, $throwException);
    }

    /**
     * Determine whether App supports charset by name.
     *
     * @param string $charset
     *
     * @return bool
     */
    public function supportsCharset(string $charset) : bool
    {
        foreach ($this->supportedCharsets AS $refCharset) {
            if (strcasecmp($charset, $refCharset) === 0) {
                return true;
                break;
            }
        }
        return false;
    }

}