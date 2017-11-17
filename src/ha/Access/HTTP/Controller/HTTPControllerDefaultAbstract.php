<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Controller;

use ha\Access\HTTP\Router\Route\HTTPRoute;

/**
 * Class ControllerDefaultAbstract.
 *
 * Converts input request to output response (call is executed from route).
 */
abstract class HTTPControllerDefaultAbstract implements HTTPController
{

    /** @var HTTPRoute */
    protected $route;

    /** @var \ha\Access\HTTP\IO\Request\HTTPInputRequest */
    protected $request;

    /** @var \ha\Access\HTTP\IO\Response\HTTPOutputResponse */
    protected $response;

    /**
     * ControllerDefaultAbstract constructor.
     *
     * @param HTTPRoute $route
     */
    public function __construct(HTTPRoute $route)
    {
        $this->route = $route;
        $this->request = $route->getRequest();
        $this->response = $route->getResponse();
    }


}