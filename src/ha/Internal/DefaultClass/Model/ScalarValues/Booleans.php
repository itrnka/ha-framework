<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Model\ScalarValues;

/**
 * Scalar collection of bool values.
 * This collection can have only boolean children.
 * @method bool current()
 * @method bool first()
 * @method Booleans newSelf(array $array = [])
 * @method Booleans reject(\Closure $function) $function = function ($value, $key): bool { ... }
 * @method Booleans filter(\Closure $filter, int $limit = 0) $filter = function ($value, $key): bool { ... }
 * @method Booleans modify(\Closure $modifier) $modifier = function ($value, $key): bool { ... }
 */
class Booleans extends ScalarValues
{
    /** @var string */
    protected $allowOnly = 'bool';

}