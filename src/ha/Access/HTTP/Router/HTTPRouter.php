<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Router;

use ha\Access\HTTP\Error\Handler\HTTPErrorHandler;
use ha\Access\HTTP\IO\Request\HTTPInputRequest;
use ha\Access\HTTP\IO\Response\HTTPOutputResponse;
use ha\Access\HTTP\Router\Route\HTTPRoute;

/**
 * Interface HTTPRouter.
 * Collection of HTTP routes with HTTP IO handling functionality.
 *
 */
interface HTTPRouter
{

    /**
     * HTTPRouter constructor.
     *
     * @param HTTPInputRequest $request
     * @param HTTPOutputResponse $response
     * @param HTTPErrorHandler $errHandler
     */
    public function __construct(HTTPInputRequest $request, HTTPOutputResponse $response, HTTPErrorHandler $errHandler);

    /**
     * Function addRoute
     *
     * @param HTTPRoute $route
     */
    public function addRoute(HTTPRoute $route) : void;

    /**
     * Convert request to response and send response to client with exit.
     *
     */
    public function handleInputRequest() : void;

}