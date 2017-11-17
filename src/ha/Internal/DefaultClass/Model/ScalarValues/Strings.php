<?php
declare(strict_types = 1);

namespace ha\Internal\DefaultClass\Model\ScalarValues;

/**
 * Scalar collection of string values.
 * This collection can have only string children.
 * @method string current()
 * @method string first()
 * @method Strings newSelf(array $array = [])
 * @method Strings reject(\Closure $function) $function = function ($value, $key) : bool { ... }
 * @method Strings filter(\Closure $filter, int $limit = 0) $filter = function ($value, $key) : bool { ... }
 * @method Strings modify(\Closure $modifier) $modifier = function ($value, $key): string { ... }
 */
class Strings extends ScalarValues
{
    /** @var string */
    protected $allowOnly = 'string';

}