<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Error;


/**
 * Class HTTP403Error.
 * HTTP 403 - Forbidden:
 * The server understood the request, but is refusing to fulfill it. Authorization will not help and the request SHOULD
 * NOT be repeated. If the request method was not HEAD and the server wishes to make public why the request has not been
 * fulfilled, it SHOULD describe the reason for the refusal in the entity. If the server does not wish to make this
 * information available to the client, the status code 404 (Not Found) can be used instead.
 *
 * @package ha\Access\HTTP\HTTPException
 */
class HTTP403Error extends HTTPErrorDefaultAbstract
{

    /** @var string[] Required response headers. */
    protected $requiredResponseHeaders = [];

    /** @var string HTTP status code description. */
    protected $descriptionMessage = 'Forbidden.';

    /** @var int HTTP status code. */
    protected $HTTPStatusCode = 403;

}