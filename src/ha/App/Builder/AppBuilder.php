<?php
declare(strict_types = 1);

namespace ha\App\Builder;


use ha\App\App;
use ha\Component\Configuration\Configuration;

/**
 * Interface for creating (building) App instance.
 *
 * @package ha\App
 */
interface AppBuilder
{

    /**
     * AppBuilderDefault constructor.
     *
     * @param Configuration $cfg
     */
    public function __construct(Configuration $cfg);

    /**
     * Build new App instance by specific use (web, CLI, etc.).
     *
     * @param string $enviromentName
     *
     * @return App
     */
    public function buildApp(string $enviromentName) : App;
}