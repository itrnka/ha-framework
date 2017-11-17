<?php
declare(strict_types = 1);

namespace ha\Middleware\Session;

use ha\Middleware\MiddlewareDefaultAbstract;

/**
 * Class SessionDisabled.
 *
 * Implementation for use cases, when session is not available r can not be available.
 */
class SessionDisabled extends MiddlewareDefaultAbstract implements Session
{

    /**
     * Determine whether storage has record stored under provided key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key) : bool
    {
        return false;
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
            $return[$key] = $default;
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
        return $this;
    }

    /**
     * Clear session data.
     *
     * @return Session
     */
    public function destroy() : Session
    {
        return $this;
    }
}