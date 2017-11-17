<?php
declare(strict_types = 1);

namespace ha\Middleware\Session;

use ha\Middleware\Middleware;


/**
 * Interface Session.
 * Access to session.
 */
interface Session extends Middleware
{

    /**
     * Determine whether storage has record stored under provided key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key) : bool;

    /**
     * Get record value from storage by key.
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed Stored value or default value
     */
    public function get(string $key, $default = null);

    /**
     * Get record values from storage by keys provided in array.
     *
     * @param array $keys
     * @param null $default
     *
     * @return array ['key' => val, 'key' => val, ...]
     */
    public function getMulti(array $keys, $default = null) : array;

    /**
     * Create new record.
     *
     * @param string $key
     * @param $value
     * @param bool $overwriteOldValue
     *
     * @return Session
     */
    public function add(string $key, $value, bool $overwriteOldValue = true) : Session;

    /**
     * Change record value and TTL in storage by key.
     *
     * @param string $key
     * @param $value
     * @param bool $autoInsertIfNotFound
     *
     * @return Session
     */
    public function set(string $key, $value, bool $autoInsertIfNotFound = true) : Session;

    /**
     * Delete value from storage by key.
     *
     * @param string $key
     *
     * @return Session
     */
    public function delete(string $key) : Session;

    /**
     * Delete values from storage by multiple string keys provided in array.
     *
     * @param array $keys
     *
     * @return Session
     */
    public function deleteMulti(array $keys) : Session;

    /**
     * Clear session data.
     *
     * @return Session
     */
    public function destroy() : Session;

}