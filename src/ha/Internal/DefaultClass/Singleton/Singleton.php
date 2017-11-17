<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Singleton;


/**
 * Interface Singleton
 * 
 * @package ha\Basic\DefaultClass\Singleton
 */
interface Singleton
{

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance();

}