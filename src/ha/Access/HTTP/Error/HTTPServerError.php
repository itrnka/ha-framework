<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Error;


/**
 * Class HTTPServerError.
 * Server error handling is better via HTTP status code 503. It is good e.g. for SEO.
 * This error represent "Service unavailable" case and is a temporary error.
 *
 * @package ha\Access\HTTP\Error
 */
class HTTPServerError extends HTTPErrorDefaultAbstract
{

    /** @var string[] Required response headers. */
    protected $requiredResponseHeaders = [];

    /** @var string HTTP status code description. */
    protected $descriptionMessage = 'Service unavailable.';

    /** @var int HTTP status code. */
    protected $HTTPStatusCode = 503;

}