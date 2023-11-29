<?php

namespace Mpietrucha\Support\Disclosure;

use Illuminate\Support\Traits\Macroable;

class Transformer
{
    use Macroable;

    public static function boolean(mixed $response, bool $mode = true): bool
    {
        return (bool) $response === $mode;
    }
}
