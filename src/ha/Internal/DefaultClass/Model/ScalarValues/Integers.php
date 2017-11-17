<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Model\ScalarValues;

/**
 * Scalar collection of integer values.
 * This collection can have only integer children.
 * @method int current()
 * @method int first()
 * @method Integers newSelf(array $array = [])
 * @method Integers reject(\Closure $function) $function = function ($value, $key): bool { ... }
 * @method Integers filter(\Closure $filter, int $limit = 0) $filter = function ($value, $key): bool { ... }
 * @method Integers modify(\Closure $modifier) $modifier = function ($value, $key): int { ... }
 */
class Integers extends ScalarValues
{
    /** @var string */
    protected $allowOnly = 'integer';

}