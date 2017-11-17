<?php
declare(strict_types = 1);

namespace ha\Component\HTTP;

use ha\Internal\DefaultClass\Model\ModelDefaultAbstract;
use ha\Internal\Exception\ReadOnlyException;

/**
 * Class URLDefault
 * @package ha\Component\URL
 * @property string $scheme
 * @property string $host
 * @property int $port
 * @property string $user
 * @property string $pass
 * @property string $path
 * @property array $query
 * @property string $fragment
 * @property string $originalUrl
 */
class URL extends ModelDefaultAbstract
{

    /** @var string */
    protected $fragment;

    /** @var string */
    protected $host;

    /** @var string */
    protected $originalUrl;

    /** @var string */
    protected $pass;

    /** @var string */
    protected $path;

    /** @var int */
    protected $port;

    /** @var array */
    protected $query;

    /** @var string */
    protected $scheme;

    /** @var string */
    protected $user;

    /**
     * URLDefault constructor.
     *
     * @param string $url
     */
    public function __construct(string $url = null)
    {
        if (is_string($url)) {
            $this->originalUrl = $url;
            $this->_parseUrl($url);
        }
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function __invoke(bool $asObject = false, array $excludeKeys = [])
    {
        $data = parent::__invoke(false, $excludeKeys);
        if (!in_array('url', $excludeKeys)) {
            $data['url'] = $this->getAsString();
        }
        if (!in_array('urlForHTML', $excludeKeys)) {
            $data['urlForHTML'] = $this->getAsHTMLString();
        }
        if ($asObject) {
            $data = (object) $data;
        }
        return $data;
    }

    /**
     * Get presentable value as string.
     * @return string Url
     */
    public function __toString(): string
    {
        return $this->getAsString();
    }

    /**
     * Get presentable value as string valid by W3C validator.
     * @return string Url
     */
    public function getAsHTMLString(): string
    {
        return $this->getAsString('&amp;');
    }

    /**
     * Get presentable value as string.
     *
     * @param string $querySeparator
     *
     * @return string Url
     */
    public function getAsString(string $querySeparator = '&'): string
    {
        $url = '';
        if (!is_null($this->host) && $this->host !== '') {
            if (!is_null($this->scheme) && $this->scheme !== '') {
                $url .= $this->scheme . '://';
            } else {
                $url .= '//';
            }
            if (!is_null($this->user) && $this->user !== '') {
                $url .= $this->user . ':' . $this->pass . '@';
            }
            $url .= $this->host;
            if (!is_null($this->port) && intval($this->port) > 0) {
                $url .= ':' . $this->port;
            }
            $url .= '/';
        }
        if (!is_null($this->path) && $this->path !== '') {
            if (is_null($this->host) || $this->host === '') {
                $url .= '/';
            }
            $url .= ltrim($this->path, '/');
        }
        if (isset($this->query) && count($this->query)) {
            $url .= '?' . http_build_query($this->query, '', $querySeparator);
        }
        if (!is_null($this->fragment) && $this->fragment !== '') {
            $url .= '#' . $this->fragment;
        }
        return $url;
    }

    /**
     * Property 'fragment' getter.
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Property 'host' getter.
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Property 'originalUrl' getter.
     * @return string
     */
    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    /**
     * Property 'pass' getter.
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Property 'path' getter.
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get path as array
     *
     * @param bool $lowerCaseMode When true, path elements are converted to lowercase
     *
     * @return array
     */
    public function getPathSegments(bool $lowerCaseMode = false): array
    {
        if ($lowerCaseMode) {
            return explode('/', trim(strtolower(strval($this->path)), '/'));
        }
        return explode('/', trim(strval($this->path), '/'));
    }

    /**
     * Get path segments count
     * @return int
     */
    public function getPathSegmentsCount(): int
    {
        return count($this->getPathSegments());
    }

    /**
     * Property 'port' getter.
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Property 'query' getter.
     * @return array
     */
    public function getQuery(): array
    {
        if (!isset($this->query)) {
            return [];
        }
        return $this->query;
    }

    /**
     * Property 'scheme' getter.
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Property 'user' getter.
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Determine whether path represents a directory
     * @return bool
     */
    public function pathIsDirectory(): bool
    {
        if (is_null($this->path)) {
            return false;
        }
        return (substr($this->path, -1) === '/');
    }

    /**
     * Determine whether path represents a file
     * @return bool
     */
    public function pathIsFile(): bool
    {
        if (is_null($this->path)) {
            return false;
        }
        return !$this->pathIsDirectory();
    }

    /**
     * Property 'fragment' (un)setter.
     *
     * @param string $fragment
     *
     * @return URL
     */
    public function setFragment(string $fragment = null): URL
    {
        $this->fragment = $fragment;
        return $this;
    }

    /**
     * Property 'host' (un)setter.
     *
     * @param string $host
     *
     * @return URL
     */
    public function setHost(string $host = null): URL
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Property 'originalUrl' (un)setter.
     *
     * @param string $originalUrl
     *
     * @return \ha\Component\HTTP\URL
     * @throws \ha\Internal\Exception\ReadOnlyException
     */
    public function setOriginalUrl(string $originalUrl = null): URL
    {
        throw new ReadOnlyException("Property 'originalUrl' is read only@" . __METHOD__);
    }

    /**
     * Property 'pass' (un)setter.
     *
     * @param string $pass
     *
     * @return URL
     */
    public function setPass(string $pass = null): URL
    {
        $this->pass = $pass;
        return $this;
    }

    /**
     * Property 'path' (un)setter.
     *
     * @param string $path
     *
     * @return URL
     */
    public function setPath(string $path = null): URL
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Property 'port' (un)setter.
     *
     * @param int $port
     *
     * @return URL
     */
    public function setPort(int $port = null): URL
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Property 'query' (un)setter.
     *
     * @param array $query
     *
     * @return URL
     */
    public function setQuery(array $query = null): URL
    {
        if (isset($query)) {
            $this->_updateQueryArray($query);
        }
        else {
            $this->query = null;
        }
        return $this;
    }

    /**
     * Property 'scheme' (un)setter.
     *
     * @param string $scheme
     *
     * @return URL
     */
    public function setScheme(string $scheme = null): URL
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * Property 'user' (un)setter.
     *
     * @param string $user
     *
     * @return URL
     */
    public function setUser(string $user = null): URL
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Recursive sort array.
     *
     * @param $array
     */
    private function _ksortTree(&$array): void
    {
        if (!is_array($array)) {
            return;
        }
        ksort($array);
        foreach ($array as $k => $v) {
            $this->_ksortTree($array[$k]);
        }
    }

    /**
     * Parse current url to internal variables.
     *
     * @param string $url
     */
    private function _parseUrl(string $url): void
    {
        $this->scheme = null;
        $this->host = null;
        $this->port = null;
        $this->user = null;
        $this->pass = null;
        $this->path = null;
        $this->query = null;
        $this->fragment = null;
        $queryData = parse_url($url);
        foreach ($queryData AS $key => $val) {
            if ($key == 'query') {
                $params = [];
                parse_str($val, $params);
                $this->_updateQueryArray($params);
                continue;
            }
            $this->__set($key, $val);
        }
    }

    /**
     * Normalize query params.
     *
     * @param array $params
     */
    private function _updateQueryArray(array $params): void
    {
        $this->query = [];
        foreach ($params AS $key => $val) {
            if (strpos($key, 'amp;') === 0) {
                unset($params[$key]);
                $key = substr($key, 4);
                $params[$key] = $val;
            }
        }
        $this->_ksortTree($params);
        if (empty($params)) {
            $params = null;
        }
        $this->query = $params;
    }

}