<?php
namespace ha\Middleware;

namespace ha\Middleware;


trait MiddlewareSingletonTrait
{
    /** @var int */
    protected static $instancesCount = 0;

    /**
     * Block multiple instances. Call this method from constructor.
     *
     * @throws \Error
     */
    public function denyMultipleInstances() : void
    {
        if (self::$instancesCount > 0) {
            throw new \Error('Instance ' . get_class($this) . ' already exists');
        }
        self::$instancesCount++;
    }

}