<?php

namespace Mpietrucha\Support\Disclosure;

use Closure;

function disclosure(object $source): Disclosure
{
    return Disclosure::create($source);
}

function not(mixed $value, bool $mode = false): bool|Closure
{
    return Disclosure::boolean($value, $mode);
}
