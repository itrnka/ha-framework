<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Service;


use ha\Internal\DefaultClass\Module\Module;

interface ModuleService
{

    /**
     * ModuleService constructor.
     *
     * @param Module $module
     */
    public function __construct(Module $module);

}