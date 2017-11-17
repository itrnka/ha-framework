<?php
declare(strict_types = 1);

namespace ha\Middleware\Cache;

use ha\Middleware\Middleware;


/**
 * Interface Cache.
 *
 * Provides IO access to cache.
 */
interface Cache extends Middleware
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
     * @param mixed $default
     *
     * @return mixed Stored value or default value
     */
    public function get(string $key, $default = null);

    /**
     * Get record values from storage by keys provided in array.
     *
     * @param array $keys
     * @param mixed $default
     *
     * @return array ['key' => val, 'key' => val, ...]
     */
    public function getMulti(array $keys, $default = null) : array;

    /**
     * Create new record.
     *
     * @param string $key
     * @param mixed $value
     * @param int $TTL
     * @param bool $overwriteOldValue
     *
     * @return Cache
     */
    public function add(string $key, $value, int $TTL = 0, bool $overwriteOldValue = true) : Cache;

    /**
     * Change record value and TTL in storage by key.
     *
     * @param string $key
     * @param mixed $value
     * @param int $TTL
     * @param bool $autoInsertIfNotFound
     *
     * @return Cache
     */
    public function set(string $key, $value, int $TTL = 0, bool $autoInsertIfNotFound = true) : Cache;

    /**
     * Delete value from storage by key.
     *
     * @param string $key
     *
     * @return Cache
     */
    public function delete(string $key) : Cache;

    /**
     * Delete values from storage by multiple string keys provided in array.
     *
     * @param array $keys
     *
     * @return Cache
     */
    public function deleteMulti(array $keys) : Cache;

}