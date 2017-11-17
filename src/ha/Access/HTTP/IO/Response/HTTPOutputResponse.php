<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\IO\Response;


use ha\Access\HTTP\IO\Request\HTTPInputRequest;


/**
 * Interface HTTPOutputResponse.
 * Represents output provided by HTTP sever.
 */
interface HTTPOutputResponse
{

    public function __construct(HTTPInputRequest $request);

    /**
     * Get input request object.
     *
     * @return HTTPInputRequest
     */
    public function getRequest() : HTTPInputRequest;

    /**
     * Add HTTP Header
     *
     * @param string $header
     *
     * @return \ha\Access\HTTP\IO\Response\HTTPOutputResponse
     */
    public function addHeader(string $header) : HTTPOutputResponse;

    /**
     * Reset previous HTTP Headers.
     *
     * @return \ha\Access\HTTP\IO\Response\HTTPOutputResponse
     */
    public function resetHeaders() : HTTPOutputResponse;

    /**
     * Set response body content
     *
     * @param string $responseBody
     *
     * @return \ha\Access\HTTP\IO\Response\HTTPOutputResponse
     */
    public function setBody(string $responseBody) : HTTPOutputResponse;

    /**
     * Set response HTTP status code.
     *
     * @param int $HTTPStatusCode
     *
     * @return \ha\Access\HTTP\IO\Response\HTTPOutputResponse
     */
    public function setStatusCode(int $HTTPStatusCode) : HTTPOutputResponse;

    /**
     * Set output charset name.
     *
     * @param string $charsetName
     *
     * @return \ha\Access\HTTP\IO\Response\HTTPOutputResponse
     */
    public function setCharset(string $charsetName) : HTTPOutputResponse;

    /**
     * Set output content type.
     *
     * @param string $contentType
     *
     * @return \ha\Access\HTTP\IO\Response\HTTPOutputResponse
     */
    public function setContentType(string $contentType) : HTTPOutputResponse;

    /**
     * Send output to client and exit.
     *
     */
    public function send() : void;

}