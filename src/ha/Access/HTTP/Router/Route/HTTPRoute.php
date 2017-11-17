<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Router\Route;


use ha\Access\HTTP\IO\Request\HTTPInputRequest;
use ha\Access\HTTP\IO\Response\HTTPOutputResponse;
use ha\Access\HTTP\Authorization\Authorization;


/**
 * Interface HTTPRoute.
 * Provides HTTP output from response by associated URL or URL pattern.
 * Contains functinality for checking headers, request method, ... and converts HTTPInputRequest to HTTPOutputResponse.
 */
interface HTTPRoute
{

    /**
     * HTTPRoute constructor.
     *
     * @param HTTPInputRequest $request
     * @param HTTPOutputResponse $response
     * @param Authorization $authorization
     */
    public function __construct(HTTPInputRequest $request, HTTPOutputResponse $response, Authorization $authorization);

    /**
     * Get HTTPInputRequest
     *
     * @return HTTPInputRequest
     */
    public function getRequest() : HTTPInputRequest;

    /**
     * Get HTTPOutputResponse
     *
     * @return HTTPOutputResponse
     */
    public function getResponse() : HTTPOutputResponse;

    /**
     * Get Authorization
     *
     * @return Authorization
     */
    public function getAuthorization() : Authorization;

    /**
     * Determine whether this route is compatible with URL. If false, router skips this route.
     *
     * @return bool
     */
    public function URLIsCompatible() : bool;

    /**
     * Determine whether Route supports request method.
     *
     * @return bool
     */
    public function checkRequestMethod() : bool;

    /**
     * Get collection of allowed request methods for this route URL. It is very important for handling HTTP405 error.
     *
     * @return array
     */
    public function getAllowedRequestMethods() : array;

    /**
     * Determine whether Route accepts mime type from Request headers (Accept compatibility).
     *
     * @return bool
     */
    public function checkAcceptRequestHeader() : bool;

    /**
     * Determine whether Route accepts charset from Request headers (Accept-Charset compatibility).
     *
     * @return bool
     */
    public function checkAcceptCharsetRequestHeader() : bool;

    /**
     * Determine whether Route accepts encoding type from Request headers (Accept-Encoding compatibility).
     *
     * @return bool
     */
    public function checkAcceptEncodingRequestHeader() : bool;

    /**
     * Determine whether Route accepts language from Request headers (Accept-Language compatibility).
     *
     * @return bool
     */
    public function checkAcceptLanguageRequestHeader() : bool;

    /**
     * Determine whether Route supports content encoding from Request headers (Content-Encoding compatibility).
     *
     * @return bool
     */
    public function checkContentEncodingRequestHeader() : bool;

    /**
     * Determine whether Route supports content charset from Request headers (Content-Encoding compatibility).
     *
     * @return bool
     */
    public function checkContentCharsetRequestHeader() : bool;

    /**
     * Determine whether Route has valid content from Request headers (Content-Length compatibility).
     *
     * @return bool
     */
    public function checkContentLengthRequestHeader() : bool;

    /**
     * Determine whether Route supports content type from Request headers (Content-Type compatibility).
     *
     * @return bool
     */
    public function checkContentTypeRequestHeader() : bool;

    /**
     * Determine whether Route is authorized. If not, you can use custom logic (generate HTTP401Error, redirect, ...).
     * If false is returned, default router functionality is used for handling unauthorized state.
     *
     * @return bool
     */
    public function isAuthorized() : bool;

    /**
     * Setup response headers and body by your controller or other logic.
     *
     */
    public function prepareResponse() : void;

}