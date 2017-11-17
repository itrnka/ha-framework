<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\IO\Response;
use ha\Access\HTTP\Error\HTTP400Error;
use ha\Access\HTTP\IO\Request\HTTPInputRequest;
use ha\Component\HTTP\HeaderValueParser;


/**
 * Class HTTPOutputResponseDefault.
 * Default simple HTTPOutputResponse with predefined headers and status code.
 *
 * @package ha\Access\HTTP\IO\Response
 */
class HTTPOutputResponseDefault implements HTTPOutputResponse
{

    /** @var array Headers storage */
    private $headers = [];

    /** @var int HTTP Status code. */
    private $statusCode = 200;

    /** @var string */
    private $body = '';

    /** @var HTTPInputRequest */
    private $request;

    /** @var  string */
    private $outputCharset;

    /** @var  string */
    private $outputContentType = 'text/html';

    /**
     * HTTPOutputResponse constructor.
     *
     * @param HTTPInputRequest $request
     */
    public function __construct(HTTPInputRequest $request)
    {
        $this->request = $request;
        $this->outputCharset = mb_internal_encoding();
    }

    /**
     * Get input request object.
     *
     * @return HTTPInputRequest
     */
    public function getRequest() : HTTPInputRequest
    {
        return $this->request;
    }

    /**
     * Add HTTP Header
     *
     * @param string $header
     *
     * @return \ha\Access\HTTP\IO\Response\HTTPOutputResponse
     */
    public function addHeader(string $header) : HTTPOutputResponse
    {
        $this->headers[] = $header;
        return $this;
    }

    /**
     * Reset previous HTTP Headers.
     *
     * @return \ha\Access\HTTP\IO\Response\HTTPOutputResponse
     */
    public function resetHeaders() : HTTPOutputResponse
    {
        $this->headers = [];
        return $this;
    }

    /**
     * Set response body content
     *
     * @param string $responseBody
     *
     * @return \ha\Access\HTTP\IO\Response\HTTPOutputResponse
     */
    public function setBody(string $responseBody) : HTTPOutputResponse
    {
        $this->body = $responseBody;
        return $this;
    }

    /**
     * Set response HTTP status code.
     *
     * @param int $HTTPStatusCode
     *
     * @return \ha\Access\HTTP\IO\Response\HTTPOutputResponse
     */
    public function setStatusCode(int $HTTPStatusCode) : HTTPOutputResponse
    {
        $this->statusCode = $HTTPStatusCode;
        return $this;
    }

    /**
     * Set output charset name.
     *
     * @param string $charsetName
     *
     * @return \ha\Access\HTTP\IO\Response\HTTPOutputResponse
     * @throws \InvalidArgumentException
     */
    public function setCharset(string $charsetName) : HTTPOutputResponse
    {
        if (!main()->supportsCharset($charsetName)) {
            throw new \InvalidArgumentException("Unsupported \$charsetName '{$charsetName}'@".__METHOD__);
        }
        $this->outputCharset = $charsetName;
        return $this;
    }

    /**
     * Set output content type.
     *
     * @param string $contentType
     *
     * @return \ha\Access\HTTP\IO\Response\HTTPOutputResponse
     */
    public function setContentType(string $contentType) : HTTPOutputResponse
    {
        if(preg_match('#^[.+]+/[.+]$#', $contentType))
        {
            throw new \InvalidArgumentException("Invalid \$contentType '{$contentType}'@".__METHOD__);
        }
        $this->outputContentType = $contentType;
        return $this;
    }

    /**
     * Send output to client and exit.
     *
     */
    public function send() : void
    {
        // remove previous output
        @ob_end_clean();

        // set status code
        http_response_code($this->statusCode);

        // get cookies set from sent headers
        $cookieHeaders = [];
        $sentHeaders = headers_list();
        if (is_array($sentHeaders)) {
            foreach ($sentHeaders AS $sentHeader) {
                if (strpos(strtolower($sentHeader), 'set-cookie:') === 0) {
                    $cookieHeaders[] = $sentHeader;
                }
            }
        }

        // remove previous header output
        @header_remove();

        // prepare charset by Accept-Charset header
        $outputEncoding = $this->outputCharset;

        // send Content-Type header
        @header("Content-Type: {$this->outputContentType};charset={$this->outputCharset}");

        // send cookie headers
        foreach ($cookieHeaders AS $header) {
            @header($header);
        }

        // send all other headers
        foreach ($this->headers AS $header) {
            @header($header);
        }

        // change response body charset via buffer and  print
        #$this->body = "ľčťžľčťžľčťžľčťžľčťžľčťž"; // UTF-8 test
        #echo "\n\n". implode("\n ", mb_list_encodings()); // list available charsets
        ob_start(function($str) use ($outputEncoding) {
            return mb_convert_encoding($str, $outputEncoding, mb_internal_encoding());
        });
        echo $this->body;
        ob_end_flush();

        exit;
    }

}