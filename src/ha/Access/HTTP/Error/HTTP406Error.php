<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Error;


/**
 * Class HTTP406Error.
 * HTTP 406 - Not Acceptable:
 * The resource identified by the request is only capable of generating response entities which have content
 * characteristics not acceptable according to the accept headers sent in the request.
 * Unless it was a HEAD request, the response SHOULD include an entity containing a list of available entity
 * characteristics and location(s) from which the user or user agent can choose the one most appropriate. The entity
 * format is specified by the media type given in the Content-Type header field. Depending upon the format and the
 * capabilities of the user agent, selection of the most appropriate choice MAY be performed automatically. However,
 * this specification does not define any standard for such automatic selection.
 *
 * @package ha\Access\HTTP\HTTPException
 */
class HTTP406Error extends HTTPErrorDefaultAbstract
{

    /** @var string[] Required response headers. */
    protected $requiredResponseHeaders = [];

    /** @var string HTTP status code description. */
    protected $descriptionMessage = 'Not acceptable mime type in your Accept header.';

    /** @var int HTTP status code. */
    protected $HTTPStatusCode = 406;

}