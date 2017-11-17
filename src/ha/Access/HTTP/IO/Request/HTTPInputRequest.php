<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\IO\Request;

use ha\Access\HTTP\Error\HTTP415Error;
use ha\Component\HTTP\URL;

/**
 * Interface HTTPInputRequest.
 * Represents incoming HTTP request to HTTP server.
 */
interface HTTPInputRequest
{


    /**
     * HTTPInputRequest constructor.
     */
    public function __construct();

    /**
     * Get current request method in uppercase
     *
     * @return string
     */
    public function getRequestMethod() : string;

    /**
     * Get client IP address or empty string.
     *
     * @return string
     */
    public function getClientIPAddress() : string;

    /**
     * Get client User-Agent header value.
     *
     * @return string
     */
    public function getClientUserAgent() : string;

    /**
     * Redirect request to another URL
     *
     * @param string $url Target url
     * @param int $HTTPStatusCode 301, 302
     */
    public function redirectToURL(string $url, int $HTTPStatusCode = 302) : void;

    /**
     * Determine whether current request method is equal to argument $requestMethod.
     *
     * @param string $requestMethod
     *
     * @return bool
     */
    public function typeof(string $requestMethod) : bool;

    /**
     * @return URL
     */
    public function getUrl() : URL;

    /**
     * Get list of supported Content-Encoding types.
     *
     * @return array
     */
    public function getSupportedEncodings() : array;

    /**
     * Get request body text.
     *
     * @return string
     * @throws HTTP415Error
     */
    public function getBody() : string;

    /**
     * @return array
     */
    public function getHeaders() : array;

    /**
     * @return array
     */
    public function getAccept() : array;

    /**
     * @return array
     */
    public function getAcceptCharset() : array;

    /**
     * @return array
     */
    public function getAcceptEncoding() : array;

    /**
     * @return array
     */
    public function getAcceptLanguage() : array;

    /**
     * @return string
     */
    public function getContentEncoding() : string;

    /**
     * @return string
     */
    public function getContentLanguage() : string;

    /**
     * @return int
     */
    public function getContentLength() : int;

    /**
     * @return string
     */
    public function getContentType() : string;

    /**
     * @return string
     */
    public function getContentTypeCharset() : string;


}