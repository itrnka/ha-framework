<?php
declare(strict_types = 1);

namespace ha\Component\Configuration\Simple;


use ha\Component\Configuration\Configuration;

/**
 * Configuration implementation for getting configuration data from an array.
 */
class ConfigurationFromArray implements Configuration
{

    /** @var array Configuration storage. */
    private $configData;

    /** @var string Configuration name. */
    private $instanceName;

    /**
     * ConfigurationFromArray constructor.
     *
     * @param array $configData
     * @param string $instanceName
     */
    public function __construct(array $configData, string $instanceName)
    {
        // set default name for this configuration
        $this->instanceName = $instanceName;
        // set $configData
        $this->configData = $configData;
    }

    /**
     * Get configuration variable by $key.
     *
     * @param string $key
     * @param bool $throwException
     *
     * @return mixed
     */
    public function get(string $key, bool $throwException = true)
    {
        if (!array_key_exists($key, $this->configData)) {
            if ($throwException) {
                throw new \InvalidArgumentException("Key \"{$key}\" not found in configuration with name \"{$this->instanceName}\"");
            }
            return null;
        }
        return $this->configData[$key];
    }

}