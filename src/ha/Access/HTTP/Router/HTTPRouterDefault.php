<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Router;

use ha\Access\HTTP\Error\HTTP403Error;
use ha\Access\HTTP\Error\HTTP404Error;
use ha\Access\HTTP\Error\HTTP405Error;
use ha\Access\HTTP\Error\HTTP406Error;
use ha\Access\HTTP\Error\HTTP411Error;
use ha\Access\HTTP\Error\HTTP415Error;
use ha\Access\HTTP\Error\Handler\HTTPErrorHandler;
use ha\Access\HTTP\IO\Request\HTTPInputRequest;
use ha\Access\HTTP\IO\Response\HTTPOutputResponse;
use ha\Access\HTTP\Router\Route\HTTPRoute;


/**
 * Class HTTPRouterDefault.
 * Default implementation of HTTPRouter.
 *
 */
class HTTPRouterDefault implements HTTPRouter
{

    /** @var HTTPInputRequest */
    private $request;

    /** @var HTTPOutputResponse */
    private $response;

    /** @var HTTPErrorHandler */
    private $exceptionHandler;

    /** @var array */
    private $routes = [];

    /**
     * RouterDefault constructor.
     *
     * @param HTTPInputRequest $request
     * @param HTTPOutputResponse $response
     * @param HTTPErrorHandler $errHandler
     */
    public function __construct(HTTPInputRequest $request, HTTPOutputResponse $response, HTTPErrorHandler $errHandler)
    {
        $this->request = $request;
        $this->response = $response;
        $this->exceptionHandler = $errHandler;
    }

    /**
     * Function addRoute
     *
     * @param HTTPRoute $route
     */
    public function addRoute(HTTPRoute $route) : void
    {
        $this->routes[] = $route;
    }

    /**
     * Convert request to response and send response to client with exit.
     *
     */
    public function handleInputRequest() : void
    {
        try {
            /** @var HTTPRoute $route */
            foreach ($this->routes AS $route) {
                // if URL is not compatible with route, skip this route
                if (!$route->URLIsCompatible()) continue;

                // if request method is not allowed for this route
                if (!$route->checkRequestMethod()) {
                    $allowedRequestMethods = $route->getAllowedRequestMethods();
                    $allowHeader = 'Allow: ' . implode(',', array_map('strtoupper', $allowedRequestMethods));
                    throw new HTTP405Error([$allowHeader]);
                }

                // if can not be send response in desired format by client (Accept* conflict)
                if (!$route->checkAcceptRequestHeader()) {
                    throw new HTTP406Error([], 'HTTP 406: Unacceptable mime type.'); // server could not return response in this mime type
                }
                if (!$route->checkAcceptCharsetRequestHeader()) {
                    throw new HTTP406Error([], 'HTTP 406: Unacceptable charset.'); // server could not send response in this charset
                }
                if (!$route->checkAcceptEncodingRequestHeader()) {
                    throw new HTTP406Error([], 'HTTP 406: Unacceptable encoding.'); // server could not encode response in this format
                }
                if (!$route->checkAcceptLanguageRequestHeader()) {
                    // nothing - HTTP406Error is not valid (user privacy policy issues)
                }

                // if request body is not in required format (we can not accept Content-Type)
                if (!$route->checkContentEncodingRequestHeader()) {
                    throw new HTTP415Error([], 'HTTP 415: Unsupported request body encoding.'); // server could not decode or decompress request body
                }
                if (!$route->checkContentLengthRequestHeader()) {
                    throw new HTTP411Error([], 'HTTP 411: Content length required.'); // server need Content-Length
                }
                if (!$route->checkContentTypeRequestHeader()) {
                    throw new HTTP415Error([], 'HTTP 415: Unsupported request content type.'); // server could not use request body in this mime type
                }

                // authorize request (when not authorized, this method should execute extra logic, e.g. redirect)
                if (!$route->isAuthorized()) {
                    throw new HTTP403Error();
                }

                // create response headers and response body
                $route->prepareResponse();

                // send prepared response
                $this->response->send();

                // deny continue
                exit;
            }

            // Route not found for current request URL...
            throw new HTTP404Error();

        } catch (\Throwable $e) {
            $this->exceptionHandler->handleError($this->request, $this->response, $e);
        }
    }

}