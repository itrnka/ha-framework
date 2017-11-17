<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Error;


/**
 * Class HTTP415Error.
 * HTTP 415 - Unsupported Media Type:
 * The server is refusing to service the request because the entity of the request is in a format not supported by the
 * requested resource for the requested method. For example, the client uploads an image as image/svg+xml, but the
 * server requires that images use a different format.
 *
 * @package ha\Access\HTTP\HTTPException
 */
class HTTP415Error extends HTTPErrorDefaultAbstract
{

    /** @var string[] Required response headers. */
    protected $requiredResponseHeaders = [];

    /** @var string HTTP status code description. */
    protected $descriptionMessage = 'Unsupported media type (your content in request is not in valid format).';

    /** @var int HTTP status code. */
    protected $HTTPStatusCode = 415;

}