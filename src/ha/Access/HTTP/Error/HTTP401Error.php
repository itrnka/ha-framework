<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Error;


/**
 * Class HTTP401Error.
 * HTTP 401 - Unauthorized:
 * The request requires user authentication. The response MUST include a WWW-Authenticate header field containing
 * a challenge applicable to the requested resource. The client MAY repeat the request with a suitable Authorization
 * header field (section 14.8). If the request already included Authorization credentials, then the 401 response
 * indicates that authorization has been refused for those credentials. If the 401 response contains the same challenge
 * as the prior response, and the user agent has already attempted authentication at least once, then the user SHOULD
 * be presented the entity that was given in the response, since that entity might include relevant diagnostic
 * information. HTTP access authentication is explained in "HTTP Authentication: Basic and Digest Access
 * Authentication".
 *
 * @package ha\Access\HTTP\HTTPException
 */
class HTTP401Error extends HTTPErrorDefaultAbstract
{

    /** @var string[] Required response headers. */
    protected $requiredResponseHeaders = ['WWW-Authenticate'];

    /** @var string HTTP status code description. */
    protected $descriptionMessage = 'Unauthorized.';

    /** @var int HTTP status code. */
    protected $HTTPStatusCode = 401;

}