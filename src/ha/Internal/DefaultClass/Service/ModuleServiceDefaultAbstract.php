<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Service;


use ha\Internal\DefaultClass\Module\Module;

abstract class ModuleServiceDefaultAbstract implements ModuleService
{

    /** @var Module  */
    protected $module;

    /**
     * ModuleService constructor.
     *
     * @param Module $module
     */
    final public function __construct(Module $module)
    {
        $this->module = $module;
        $this->bootstrap();
    }

    /**
     * Constructor replacement. Setup here class properties.
     *
     */
    abstract protected function bootstrap() : void;


}