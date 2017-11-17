<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Error;


/**
 * Class HTTP405Error.
 * HTTP 405 - Method Not Allowed:
 * The method specified in the Request-Line is not allowed for the resource identified by the Request-URI.
 * The response MUST include an Allow header containing a list of valid methods for the requested resource.
 *
 * @package ha\Access\HTTP\HTTPException
 */
class HTTP405Error extends HTTPErrorDefaultAbstract
{

    /** @var string[] Required response headers. */
    protected $requiredResponseHeaders = ['Allow'];

    /** @var string HTTP status code description. */
    protected $descriptionMessage = 'Your request method is not allowed for this URL.';

    /** @var int HTTP status code. */
    protected $HTTPStatusCode = 405;

}