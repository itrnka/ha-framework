<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Model;


interface ModelInvokeStdClass
{

    /**
     * Return raw values from model as stdClass
     *
     * @return \stdClass
     */
    public function __invoke() : \stdClass;

}