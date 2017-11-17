<?php
declare(strict_types = 1);

namespace ha\App\Builder;


use ha\App\App;
use ha\App\AppDefault;
use ha\Component\Configuration\Configuration;
use ha\Component\Container\IoC\IoCContainerFromConfigArray;


/**
 * Example of default App instance builder. You can use your custom implementation.
 *
 * @package ha\App
 */
class AppBuilderDefault implements AppBuilder
{

    /** @var Configuration $cfg */
    private $cfg;

    /**
     * AppBuilderDefault constructor.
     *
     * @param Configuration $cfg
     */
    public function __construct(Configuration $cfg)
    {
        $this->cfg = $cfg;
    }

    /**
     * Function buildApp
     *
     * @param string $environmentName
     *
     * @return App
     */
    public function buildApp(string $environmentName) : App
    {
        // create IoC container with middleware instances
        $middleware = new IoCContainerFromConfigArray($this->cfg->get('middleware'));

        // create IoC container with modules
        $modules = new IoCContainerFromConfigArray($this->cfg->get('modules'));

        // build and return app
        $app = new AppDefault($environmentName, $this->cfg, $modules, $middleware);
        return $app;
    }
}