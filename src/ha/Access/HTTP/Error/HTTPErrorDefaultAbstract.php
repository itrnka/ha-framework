<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\Error;


abstract class HTTPErrorDefaultAbstract extends \Error implements HTTPError
{

    /** @var string[] Required response headers. */
    protected $requiredResponseHeaders = [];

    /** @var string HTTP status code description. */
    protected $descriptionMessage = 'Undefined error in class ' . __CLASS__;

    /** @var int HTTP status code. */
    protected $HTTPStatusCode = 503;

    /** @var array Response headers collection. */
    private $headers = [];

    /** @var string */
    protected $message;

    /** @var \Throwable */
    protected $previous;

    /**
     * HTTPError constructor.
     *
     * @param array $headers Custom headers for generating HTTPResponse
     * @param string $overrideMessageText Customize error text.
     * @param \Throwable $previous
     *
     * @throws \Error
     */
    public function __construct(array $headers = null, string $overrideMessageText = null, \Throwable $previous = null)
    {
        $this->previous = $previous;
        $this->headers[] = 'Etag: ' . md5(time() . $this->HTTPStatusCode);
        $this->headers[] = "Last-Modified: " . @gmdate('D, d M Y H:i:s \G\M\T', time());
        if (is_array($headers)) {
            foreach ($headers AS $header) {
                $this->headers[] = $header;
            }
        }
        foreach ($this->requiredResponseHeaders AS $requiredHeader) {
            $found = false;
            foreach ($this->headers AS $foundHeader) {
                if ($found) break;
                if (strpos($foundHeader, "{$requiredHeader}:") === 0) {
                    $found = true;
                }
            }
            if (!$found) {
                throw new \Error("Header '$requiredHeader' not defined (please add header on generating this error) in ".get_class($this), 0, $this);
            }
        }
        $this->message = "HTTP {$this->HTTPStatusCode} Error: {$this->descriptionMessage}";
        if (is_string($overrideMessageText)) {
            $this->message = $overrideMessageText;
        }
        #ddd($this->getPreviousThrowable());
    }

    /**
     * Get HTTP status code for generating custom HTTP response.
     *
     * @return int
     */
    public function getHTTPStatusCode() : int
    {
        return $this->HTTPStatusCode;
    }

    /**
     * Get headers collection for generating custom HTTP response.
     *
     * @return array
     */
    public function getResponseHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Send HTTP headers, print simple body and exit.
     *
     */
    public function generateErrorResponse() : void
    {
        @ob_clean();
        http_response_code($this->getHTTPStatusCode());
        header('Content-Type: text/plain');
        foreach ($this->getResponseHeaders() AS $header) {
            header($header);
        }
        echo $this->getMessage();
        exit;
    }

    /** Returns the previous Throwable (self::getPrevious() does not work correctly).
     * @return \Throwable
     */
    public function getPreviousThrowable()
    {
        return $this->previous;
    }

}