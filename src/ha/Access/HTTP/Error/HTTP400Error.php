<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Error;


/**
 * Class HTTP400Error.
 * HTTP 400 - Bad Request:
 * The request could not be understood by the server due to malformed syntax. The client SHOULD NOT repeat the request
 * without modifications.
 * @package ha\Access\HTTP\Error
 */
class HTTP400Error extends HTTPErrorDefaultAbstract
{

    /** @var string[] Required response headers. */
    protected $requiredResponseHeaders = [];

    /** @var string HTTP status code description. */
    protected $descriptionMessage = 'Bad Request.';

    /** @var int HTTP status code. */
    protected $HTTPStatusCode = 400;

}