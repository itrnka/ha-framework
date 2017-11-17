<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Router\Builder;

use ha\Access\HTTP\Router\HTTPRouter;

/**
 * Interface HTTPRouterBuilder.
 * Creates router with routes by specific ENV or usage.
 */
interface HTTPRouterBuilder
{

    /**
     * HTTPRouterBuilder constructor.
     */
    public function __construct();

    /**
     * Build and return Router
     *
     * @return HTTPRouter
     */
    public function buildRouter() : HTTPRouter;

}