<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Router\Builder;

use ha\Access\HTTP\Authorization\AuthorizationDisabled;
use ha\Access\HTTP\Error\Handler\HTTPErrorHandlerDefault;
use ha\Access\HTTP\IO\Request\HTTPInputRequestDefault;
use ha\Access\HTTP\IO\Response\HTTPOutputResponseDefault;
use ha\Access\HTTP\Router\HTTPRouter;
use ha\Access\HTTP\Router\HTTPRouterDefault;
use ha\Access\HTTP\Router\Route\HTTPRouteExample;
use hac\SmartHTTP\RestAPI\JSONRestAPIControllerTest;
use hac\SmartHTTP\RestAPI\JSONRestAPIRoute;

/**
 * Class HTTPRouterBuilderExample.
 *
 * Example implementation.
 */
class HTTPRouterBuilderExample implements HTTPRouterBuilder
{

    /**
     * HTTPRouterBuilder constructor.
     */
    public function __construct()
    {

    }

    /**
     * Build and return Router.
     *
     * @return HTTPRouter
     */
    public function buildRouter() : HTTPRouter
    {
        // create router dependencies and router instance
        $request = new HTTPInputRequestDefault();
        $response = new HTTPOutputResponseDefault($request);
        $errHandler = new HTTPErrorHandlerDefault();
        $router = new HTTPRouterDefault($request, $response, $errHandler);

        // prepare authorizations
        $authorizationDisabled = new AuthorizationDisabled();

        // add your routes here
        $router->addRoute(new HTTPRouteExample($request, $response, $authorizationDisabled));

        // return
        return $router;
    }

}