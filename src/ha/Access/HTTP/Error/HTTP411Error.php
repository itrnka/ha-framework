<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Error;


/**
 * Class HTTP411Error.
 * HTTP 411 - Length Required:
 * The server refuses to accept the request without a defined Content- Length. The client MAY repeat the request if it
 * adds a valid Content-Length header field containing the length of the message-body in the request message.
 * @package ha\Access\HTTP\Error
 */
class HTTP411Error extends HTTPErrorDefaultAbstract
{
    /** @var string[] Required response headers. */
    protected $requiredResponseHeaders = [];

    /** @var string HTTP status code description. */
    protected $descriptionMessage = 'Length Required.';

    /** @var int HTTP status code. */
    protected $HTTPStatusCode = 411;
}