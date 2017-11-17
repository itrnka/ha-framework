<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Router\Route;

use ha\Access\HTTP\IO\Request\HTTPInputRequest;
use ha\Access\HTTP\IO\Response\HTTPOutputResponse;
use ha\Access\HTTP\Authorization\Authorization;

/**
 * Class HTTPRouteDefaultAbstract.
 * Predefined functionality for HTTPRoute instances.
 *
 */
abstract class HTTPRouteDefaultAbstract implements HTTPRoute
{

    /** @var HTTPInputRequest */
    protected $request;

    /** @var HTTPOutputResponse */
    protected $response;

    /** @var Authorization */
    protected $authorization;

    /** @var bool Is required Content-Length request header. */
    protected $contentLengthHeaderRequired = false;

    /** @var array */
    protected $allowedRequestMethods = ['GET'];

    /**
     * HTTPRoute constructor.
     *
     * @param HTTPInputRequest $request
     * @param HTTPOutputResponse $response
     * @param Authorization $authorization
     */
    public function __construct(HTTPInputRequest $request, HTTPOutputResponse $response, Authorization $authorization)
    {
        $this->request = $request;
        $this->response = $response;
        $this->authorization = $authorization;
        $this->bootstrap();
    }

    /**
     * Bootstrap internal data.
     */
    protected function bootstrap(): void
    {
    }

    /**
     * Get HTTPInputRequest
     *
     * @return HTTPInputRequest
     */
    public function getRequest() : HTTPInputRequest
    {
        return $this->request;
    }

    /**
     * Get HTTPOutputResponse
     *
     * @return HTTPOutputResponse
     */
    public function getResponse() : HTTPOutputResponse
    {
        return $this->response;
    }

    /**
     * Get Authorization
     *
     * @return Authorization
     */
    public function getAuthorization() : Authorization
    {
        return $this->authorization;
    }

    /**
     * Determine whether this route is compatible with URL. If false, router skips this route.
     *
     * @return bool
     */
    public function URLIsCompatible() : bool
    {
        throw new \LogicException("Override your method '" . __METHOD__ . "' in class " . get_class($this));
    }

    /**
     * Determine whether Route supports request method.
     *
     * @return bool
     */
    public function checkRequestMethod() : bool
    {
        foreach ($this->getAllowedRequestMethods() AS $method) {
            if ($this->request->typeof($method)) {
                return true;
                break;
            }
            unset($method);
        }
        return false;
    }

    /**
     * Get collection of allowed request methods for this route URL. It is very important for handling HTTP405 error.
     *
     * @return array
     */
    public function getAllowedRequestMethods() : array
    {
        return $this->allowedRequestMethods;
    }

    /**
     * Determine whether Route accepts mime type from Request headers.
     *
     * @return bool
     */
    public function checkAcceptRequestHeader() : bool
    {
        // accept all by default
        return true;
    }

    /**
     * Determine whether Route accepts charset from Request headers (Accept-Charset compatibility).
     *
     * @return bool
     */
    public function checkAcceptCharsetRequestHeader() : bool
    {
        // if not found (any is accepted by client)
        if (count($this->request->getAcceptCharset()) === 0) {
            return true;
        }
        // if any is accepted by client
        if (in_array('*', $this->request->getAcceptCharset())) {
            return true;
        }
        // check all available charsets
        foreach ($this->request->getAcceptCharset() AS $item) {
            if (main()->supportsCharset($item)) {
                return true;
            }
            unset($item);
        }
        return false;
    }

    /**
     * Determine whether Route accepts encoding type from Request headers (Accept-Encoding compatibility).
     *
     * @return bool
     */
    public function checkAcceptEncodingRequestHeader() : bool
    {
        $reqBodyEncoding = $this->request->getContentEncoding();
        // if not found
        if ($reqBodyEncoding === '') {
            return true;
        }
        // get request has not body
        if ($this->request->typeof('get')) {
            return true;
        }
        // find match
        foreach ($this->request->getSupportedEncodings() AS $refEncoding) {
            if (strcasecmp($reqBodyEncoding, $refEncoding) === 0) {
                return true;
                break;
            }
        }
        return false;
    }

    /**
     * Determine whether Route accepts language from Request headers (Accept-Language compatibility).
     *
     * @return bool
     */
    public function checkAcceptLanguageRequestHeader() : bool
    {
        // by default accept any or none language
        return true;
    }

    /**
     * Determine whether Route has valid content encoding from Request headers (Content-Encoding compatibility).
     *
     * @return bool
     */
    public function checkContentEncodingRequestHeader() : bool
    {
        // by default in php we can decompress gzip and deflate request body
        if (in_array($this->request->getContentEncoding(), array_map('strtolower', ['', 'gzip', 'deflate']))) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether Route supports content charset from Request headers (Content-Encoding compatibility).
     *
     * @return bool
     */
    public function checkContentCharsetRequestHeader() : bool
    {
        $contentTypeCharset = $this->request->getContentTypeCharset();
        if ($contentTypeCharset === '') return true;
        return main()->supportsCharset($contentTypeCharset);
    }

    /**
     * Determine whether Route has valid content from Request headers (Content-Length compatibility).
     *
     * @return bool
     */
    public function checkContentLengthRequestHeader() : bool
    {
        if ($this->contentLengthHeaderRequired) {
            if ($this->request->getContentLength() < 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Determine whether Route supports content type from Request headers.
     *
     * @return bool
     */
    public function checkContentTypeRequestHeader() : bool
    {
        // by default any request body mime type is allowed
        return true;
    }

    /**
     * Determine whether Route is authorized. If not, you can use custom logic (generate HTTP401Error, redirect, ...).
     * If false is returned, default router functionality is used for handling unauthorized state.
     *
     * @return bool
     */
    public function isAuthorized() : bool
    {
        return $this->authorization->authorize($this->request, $this->response);
    }

    /**
     * Setup response headers and body by your controller or other logic.
     *
     */
    public function prepareResponse() : void
    {
        $this->response->setBody('This is default body defined in class "' . __CLASS__ . '". 
        Please add method "' . __FUNCTION__ . '()" into class "' . get_class($this) . '" 
        for your custom implementation .');
    }

}