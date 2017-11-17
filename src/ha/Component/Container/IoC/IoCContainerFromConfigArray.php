<?php
declare(strict_types = 1);

namespace ha\Component\Container\IoC;

use ha\Component\Configuration\Simple\ConfigurationFromArray;

class IoCContainerFromConfigArray extends IoCContainerDefaultAbstract
{

    /**
     * IoCContainerFromConfigArray constructor.
     *
     * @param array $configuration Key is className, value is array with configuration
     *
     * @throws \Error
     */
    public function __construct(array $configuration) {
        foreach ($configuration AS $moduleClass => $moduleConfiguration) {
            if (!isSet($moduleConfiguration[0])) {
                throw new \Error('Error in configuration: first item on index 0 not found');
            }
            if (!is_string($moduleConfiguration[0])) {
                throw new \Error('Error in configuration: first item on index 0 is not string');
            }
            if (!class_exists($moduleConfiguration[0])) {
                throw new \Error('Error in configuration: class "' . $moduleConfiguration[0] . '" does not exists ');
            }
            if (!isSet($moduleConfiguration[1]) || !is_array($moduleConfiguration[1])) {
                throw new \Error('Error in configuration: container configuration not found or is not an array');
            }
            $moduleClass = $moduleConfiguration[0];
            $moduleConfiguration = new ConfigurationFromArray($moduleConfiguration[1], "{$moduleClass}.cfg");
            $reflection = new \ReflectionClass($moduleClass);
            $moduleName = $moduleConfiguration->get('name');
            $this->$moduleName = $reflection->newInstanceArgs([$moduleConfiguration]);
        }
        parent::__construct($configuration);
    }

}