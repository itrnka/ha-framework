<?php
declare(strict_types = 1);

namespace ha\Middleware\Session;

use ha\Component\Configuration\Configuration;
use ha\Middleware\MiddlewareDefaultAbstract;


/**
 * Class SessionDefault.
 *
 * Default implementation.
 */
class SessionDefault extends MiddlewareDefaultAbstract implements Session
{

    /** @var bool */
    protected $destroyed = false;

    /**
     * SessionDefault constructor.
     *
     * @param \ha\Component\Configuration\Configuration $configuration
     *
     * @throws \Error
     */
    public function __construct(Configuration $configuration)
    {
        if (session_status() !== PHP_SESSION_NONE) {
            throw new \Error('Session already started or disabled');
        }
        if ($configuration->get('session.name', false)) {
            @session_name($configuration->get('session.name'));
        }
        session_start();
        parent::__construct($configuration);
    }

    /**
     * Determine whether storage has record stored under provided key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key) : bool
    {
        if ($this->destroyed === true) return false;
        return isSet($_SESSION[$key]);
    }

    /**
     * Get record value from storage by key.
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed Stored value or default value
     */
    public function get(string $key, $default = null)
    {
        if ($this->has($key)) return $_SESSION[$key];
        return $default;
    }

    /**
     * Get record values from storage by keys provided in array.
     *
     * @param array $keys
     * @param null $default
     *
     * @return array ['key' => val, 'key' => val, ...]
     */
    public function getMulti(array $keys, $default = null) : array
    {
        $return = [];
        foreach ($keys AS $key) {
            if ($this->has($key)) {
                $return[$key] = $_SESSION[$key];
            } else {
                $return[$key] = $default;
            }
        }
        return $return;
    }

    /**
     * Create new record.
     *
     * @param string $key
     * @param $value
     * @param bool $overwriteOldValue
     *
     * @return Session
     */
    public function add(string $key, $value, bool $overwriteOldValue = true) : Session
    {
        if ($this->destroyed === true) return $this;
        if ($overwriteOldValue === true) {
            $_SESSION[$key] = $value;
            return $this;
        } else {
            if (!$this->has($key)) {
                $_SESSION[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Change record value and TTL in storage by key.
     *
     * @param string $key
     * @param $value
     * @param bool $autoInsertIfNotFound
     *
     * @return Session
     */
    public function set(string $key, $value, bool $autoInsertIfNotFound = true) : Session
    {
        if ($this->destroyed === true) return $this;
        if ($autoInsertIfNotFound === true) {
            $_SESSION[$key] = $value;
            return $this;
        } else {
            if ($this->has($key)) {
                $_SESSION[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Delete value from storage by key.
     *
     * @param string $key
     *
     * @return Session
     */
    public function delete(string $key) : Session
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
        return $this;
    }

    /**
     * Delete values from storage by multiple string keys provided in array.
     *
     * @param array $keys
     *
     * @return Session
     */
    public function deleteMulti(array $keys) : Session
    {
        foreach ($keys AS $key) {
            if ($this->has($key)) {
                unset($_SESSION[$key]);
            }
        }
        return $this;
    }

    /**
     * Clear session data.
     *
     * @return Session
     */
    public function destroy() : Session
    {
        $this->destroyed = true;
        @session_destroy();
        @session_unset();
        unset($_COOKIE[session_name()]);
        setcookie(session_name(), '', -1, '/');
        return $this;
    }

}