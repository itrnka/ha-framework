<?php
declare(strict_types = 1);

namespace ha\Access\HTTP\IO\Request;


use ha\Access\HTTP\Error\HTTP415Error;
use ha\Access\HTTP\IO\Response\HTTPOutputResponseDefault;
use ha\Component\HTTP\HeaderValueParser;
use ha\Component\HTTP\URL;

class HTTPInputRequestDefault implements HTTPInputRequest
{

    /** @var URL */
    private $url;

    /** @var array */
    private $headers;

    /** @var array */
    private $accept = [];

    /** @var array */
    private $acceptCharset = [];

    /** @var array */
    private $acceptEncoding = [];

    /** @var array */
    private $acceptLanguage = [];

    /** @var string */
    private $contentEncoding = '';

    /** @var string */
    private $contentLanguage = [];

    /** @var int */
    private $contentLength = -1;

    /** @var string */
    private $contentType = '';

    /** @var string */
    private $contentTypeCharset = '';

    /** @var  string */
    private $requestMethod;

    /** @var string */
    private $clientIPAddress = null;

    /**
     * HTTPInputRequestDefault constructor.
     * @throws \ErrorException
     */
    public function __construct()
    {
        if (!isSet($_SERVER['REQUEST_METHOD'])) {
            throw new \ErrorException("Class " . get_class($this) . " requires \$_SERVER['REQUEST_METHOD']");
        }
        $this->requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->_parseUrl();
        $this->_extractHttpHeaders();
    }

    /**
     * Get client IP address or empty string.
     *
     * @return string
     */
    public function getClientIPAddress() : string
    {
        if (is_null($this->clientIPAddress)) {
            $addr = '';
            foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] AS $env) {
                $ip = getenv($env);
                if (!filter_var($ip, FILTER_VALIDATE_IP) === false) {
                    $addr = $ip;
                    continue;
                }
                if (isSet($_SERVER[$env]) && !filter_var($_SERVER[$env], FILTER_VALIDATE_IP) === false) {
                    $addr = $_SERVER[$env];
                }
            }
            $this->clientIPAddress = $addr;
        }
        return $this->clientIPAddress;
    }

    /**
     * Get client User-Agent header value or ''.
     *
     * @return string
     */
    public function getClientUserAgent() : string
    {
        return @trim($_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * Get current request method in uppercase
     *
     * @return string
     */
    public function getRequestMethod() : string
    {
        return $this->requestMethod;
    }

    /**
     * Redirect request to another URL
     *
     * @param string $url Target url
     * @param int $HTTPStatusCode 301, 302
     */
    public function redirectToURL(string $url, int $HTTPStatusCode = 302) : void
    {
        $url = trim($url);
        if ($url === '') {
            throw new \InvalidArgumentException('$url could not be empty@' . get_class($this) . '::' . __METHOD__);
        }
        if ($HTTPStatusCode < 301 || $HTTPStatusCode > 399) {
            throw new \InvalidArgumentException('Invalid $HTTPStatusCode@' . get_class($this) . '::' . __METHOD__);
        }
        $response = new HTTPOutputResponseDefault($this);
        $response->setStatusCode($HTTPStatusCode)
                 ->resetHeaders()
                 ->addHeader("Location: {$url}")
                 ->send();
    }

    /**
     * Determine whether current request method is equal to argument $requestMethod.
     *
     * @param string $requestMethod
     *
     * @return bool
     */
    public function typeof(string $requestMethod) : bool
    {
        if (strcasecmp($requestMethod, $this->requestMethod) == 0) {
            return true;
        }
        return false;
    }

    /**
     * @return URL
     */
    public function getUrl() : URL
    {
        // clone URL for preventing changes
        return clone $this->url;
    }

    /**
     * Get list of supported Content-Encoding types.
     *
     * @return array
     */
    public function getSupportedEncodings() : array
    {
        return ['gzip', 'deflate'];
    }

    /**
     * Get request body text.
     *
     * @return string
     * @throws HTTP415Error
     */
    public function getBody() : string
    {
        /*
        $context = stream_context_create([]);
        $fp = fopen('php://input', 'r', false, $context);
        */
        $body = file_get_contents("php://input");

        // decode
        if (strtolower($this->getContentEncoding()) === 'gzip') {
            $body = gzdecode($body);
        }
        if (strtolower($this->getContentEncoding()) === 'deflate') {
            $body = gzinflate($body);
        }

        // convert charset
        $sourceCharset = $this->getContentTypeCharset();
        if ($sourceCharset !== '') {
            if (!main()->supportsCharset($sourceCharset)) {
                throw new HTTP415Error([], 'HTTP 415: Could not convert source charset to working charset.');
            }
            $body = mb_convert_encoding($body, $sourceCharset, mb_internal_encoding());
        }
        return $body;
    }

    /**
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getAccept() : array
    {
        if (!count($this->accept)) return ['*/*'];
        return $this->accept;
    }

    /**
     * @return array
     */
    public function getAcceptCharset() : array
    {
        return $this->acceptCharset;
    }

    /**
     * @return array
     */
    public function getAcceptEncoding() : array
    {
        return $this->acceptEncoding;
    }

    /**
     * @return array
     */
    public function getAcceptLanguage() : array
    {
        return $this->acceptLanguage;
    }

    /**
     * @return string
     */
    public function getContentEncoding() : string
    {
        return $this->contentEncoding;
    }

    /**
     * @return string
     */
    public function getContentLanguage() : string
    {
        return $this->contentLanguage;
    }

    /**
     * @return int
     */
    public function getContentLength() : int
    {
        return $this->contentLength;
    }

    /**
     * @return string
     */
    public function getContentType() : string
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getContentTypeCharset() : string
    {
        return $this->contentTypeCharset;
    }

    /**
     * Parse current url to internal Url object.
     *
     */
    private function _parseUrl()
    {
        $cfg = [
            'REQUEST_SCHEME' => null, 'HTTP_HOST' => null, 'REQUEST_URI' => null,
        ];
        foreach ($_SERVER AS $key => $val) {
            $cfg[$key] = $val;
        }
        $url = '';
        if (isSet($cfg['REQUEST_SCHEME'])) {
            $url .= $cfg['REQUEST_SCHEME'] . '://';
        } else {
            throw new \ErrorException("Class " . get_class($this) . " requires \$_SERVER['REQUEST_SCHEME']");
        }
        if (isSet($cfg['HTTP_HOST'])) {
            $url .= $cfg['HTTP_HOST'];
        } else {
            throw new \ErrorException("Class " . get_class($this) . " requires \$_SERVER['HTTP_HOST']");
        }
        if (isSet($cfg['REQUEST_URI'])) {
            $url .= $cfg['REQUEST_URI'];
        }
        $this->url = new URL($url);
    }

    private function _createHeaderParser(string $headerValue) : HeaderValueParser
    {
        return new HeaderValueParser($headerValue);
    }

    /**
     * Parse current http headers to internal array.
     *
     */
    private function _extractHttpHeaders() : void
    {
        $this->headers = [];
        foreach (getallheaders() AS $key => $val) {
            $this->headers[$key] = $val;
            if (strcasecmp($key, 'Accept') == 0) {
                $this->_extractAcceptHeaderValues($val);
            }
            if (strcasecmp($key, 'Accept-Charset') == 0) {
                $this->_extractAcceptCharsetHeaderValues($val);
            }
            if (strcasecmp($key, 'Accept-Encoding') == 0) {
                $this->_extractAcceptEncodingHeaderValues($val);
            }
            if (strcasecmp($key, 'Accept-Language') == 0) {
                $this->_extractAcceptLanguageHeaderValues($val);
            }
            if (strcasecmp($key, 'Content-Encoding') == 0) {
                $this->_extractContentEncodingHeaderValues($val);
            }
            if (strcasecmp($key, 'Content-Language') == 0) {
                $this->_extractContentLanguageHeaderValues($val);
            }
            if (strcasecmp($key, 'Content-Length') == 0) {
                $this->_extractContentLengthHeaderValues($val);
            }
            #Content-Location URL TODO
            #Content-MD5 string TODO
            #Content-Range TODO
            if (strcasecmp($key, 'Content-Type') == 0) {
                $this->_extractContentTypeHeaderValues($val);
            }
            unset($key, $val);
        }
    }

    private function _extractAcceptHeaderValues(string $headerValue) : void
    {
        #Accept: audio/*; q=0.2, audio/basic
        $parser = $this->_createHeaderParser($headerValue);
        $values = $parser->getValuesOnly(); // TODO sort by level+q param
        if (count($values) > 0) {
            $this->accept = $values;
        }
    }

    private function _extractAcceptLanguageHeaderValues(string $headerValue) : void
    {
        #Accept-Language: da, en-gb;q=0.8, en;q=0.7
        $parser = $this->_createHeaderParser($headerValue);
        $values = $parser->getValuesOnly(); // TODO sort by q param
        if (count($values) > 0) {
            $this->acceptLanguage = $values;
            #$this->acceptLanguageRange = // TODO
        }
    }

    private function _extractAcceptEncodingHeaderValues(string $headerValue) : void
    {
        #Accept-Encoding: gzip;q=1.0, identity; q=0.5, *;q=0
        $parser = $this->_createHeaderParser($headerValue);
        $values = $parser->getValuesOnly(); // TODO sort by q param
        if (count($values) > 0) {
            $this->acceptEncoding = $values;
        }
    }

    private function _extractAcceptCharsetHeaderValues(string $headerValue) : void
    {
        #Accept-Charset: iso-8859-5, unicode-1-1;q=0.8
        $parser = $this->_createHeaderParser($headerValue);
        $values = $parser->getValuesOnly(); // TODO sort by q param
        if (count($values) > 0) {
            $this->acceptCharset = $values;
        }
    }

    private function _extractContentEncodingHeaderValues(string $headerValue) : void
    {
        #Content-Encoding: gzip
        $parser = $this->_createHeaderParser($headerValue);
        $values = $parser->getValuesOnly();
        if (count($values) === 1) {
            $this->contentEncoding = $values[0];
        }
    }

    private function _extractContentLanguageHeaderValues(string $headerValue) : void
    {
        #Content-Language: mi, en
        $parser = $this->_createHeaderParser($headerValue);
        $values = $parser->getValuesOnly();
        if (count($values) > 0) {
            $this->contentLanguage = $values;
        }
    }

    private function _extractContentLengthHeaderValues(string $headerValue) : void
    {
        #Content-Length: 3495
        $parser = $this->_createHeaderParser($headerValue);
        $values = $parser->getValuesOnly();
        if (count($values) === 1 && is_int($values[0]) && $values[0] >= 0) {
            $this->contentLength = $values[0];
        }
    }

    private function _extractContentTypeHeaderValues(string $headerValue) : void
    {
        #Content-Type: text/html; charset=ISO-8859-4
        $parser = $this->_createHeaderParser($headerValue);
        $values = $parser->getValuesOnly();
        if (count($values) === 1) {
            $this->contentType = $values[0];
            $this->contentTypeCharset = $parser->getParamValue('charset');
        }
    }

}