<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Controller;

use ha\Access\HTTP\Router\Route\HTTPRoute;

/**
 * Interface HTTPController.
 *
 * Converts input request to output response (call is executed from route).
 */
interface HTTPController
{

    /**
     * Controller constructor.
     *
     * @param HTTPRoute $route
     */
    public function __construct(HTTPRoute $route);

}