<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Error;


/**
 * Class HTTP404Error.
 * HTTP 404 - Not Found:
 * The server has not found anything matching the Request-URI. No indication is given of whether the condition
 * is temporary or permanent. The 410 (Gone) status code SHOULD be used if the server knows, through some internally
 * configurable mechanism, that an old resource is permanently unavailable and has no forwarding address. This status
 * code is commonly used when the server does not wish to reveal exactly why the request has been refused, or when no
 * other response is applicable.
 *
 * @package ha\Access\HTTP\HTTPException
 */
class HTTP404Error extends HTTPErrorDefaultAbstract
{

    /** @var string[] Required response headers. */
    protected $requiredResponseHeaders = [];

    /** @var string HTTP status code description. */
    protected $descriptionMessage = 'Not Found.';

    /** @var int HTTP status code. */
    protected $HTTPStatusCode = 404;

}