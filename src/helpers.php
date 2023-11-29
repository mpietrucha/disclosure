<?php

namespace Mpietrucha\Support\Disclosure;

use Closure;

function disclosure(object $source): Disclosure
{
    return new Disclosure($source);
}

function not(mixed $value, bool $mode = false): bool|Closure
{
    return Disclosure::boolean($value, $mode);
}

function lazy(mixed $value, mixed ...$arguments): Argument
{
    return new Argument($value, $arguments);
}

function value(mixed $value, mixed ...$arguments): mixed
{
    return Argument::value($value, $arguments);
}
