<?php
declare(strict_types=1);

namespace ha\Internal\DefaultClass\Model\ScalarValues;

/**
 * Scalar collection of string values.
 * This collection can have only string children.
 * @method float current()
 * @method float first()
 * @method Floats newSelf(array $array = [])
 * @method Floats reject(\Closure $function) $function = function ($value, $key) : bool { ... }
 * @method Floats filter(\Closure $filter, int $limit = 0) $filter = function ($value, $key) : bool { ... }
 * @method Floats modify(\Closure $modifier) $modifier = function ($value, $key): float { ... }
 */
class Floats extends ScalarValues
{
    /** @var string */
    protected $allowOnly = 'float';
}