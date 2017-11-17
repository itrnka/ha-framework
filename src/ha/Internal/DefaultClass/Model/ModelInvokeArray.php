<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Model;


interface ModelInvokeArray
{
    /**
     * Return raw values from model as array
     *
     * @return array
     */
    public function __invoke() : array;
}