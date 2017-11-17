<?php
declare(strict_types=1);

namespace ha\Access\HTTP\Error;

/**
 * Interface HTTPError.
 */
interface HTTPError extends \Throwable
{

    /**
     * HTTPError constructor.
     *
     * @param array $headers Custom headers for generating HTTPResponse
     * @param string $overrideMessageText Customize error text.
     */
    public function __construct(array $headers, string $overrideMessageText = null);

    /**
     * Send HTTP headers, print simple body and exit.
     */
    public function generateErrorResponse(): void;

    /**
     * Get HTTP status code for generating custom HTTP response.
     * @return int
     */
    public function getHTTPStatusCode(): int;

    /** Returns the previous Throwable (self::getPrevious() does not work correctly).
     * @return \Throwable
     */
    public function getPreviousThrowable();

    /**
     * Get headers collection for generating custom HTTP response.
     * @return string[]
     */
    public function getResponseHeaders(): array;

}