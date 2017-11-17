<?php
declare(strict_types = 1);

namespace ha\Component\Configuration;

interface Configuration
{

    /**
     * Get configuration variable by $key.
     *
     * @param string $key
     * @param bool $throwException
     *
     * @return mixed Default NULL
     */
    public function get(string $key, bool $throwException = true);

}