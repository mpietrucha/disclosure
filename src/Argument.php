<?php

namespace Mpietrucha\Support\Disclosure;

use Closure;
use Mpietrucha\Support\Disclosure\Contracts\ArgumentInterface;

class Argument implements ArgumentInterface
{
    public function __construct(protected mixed $value, protected array $arguments = [])
    {
    }

    public function __invoke(): mixed
    {
        return $this->get();
    }

    public static function value(mixed $value, array $arguments = []): mixed
    {
        if ($value instanceof ArgumentInterface) {
            return $value->get();
        }

        if (! $value instanceof Closure) {
            return $value;
        }

        return $value(...collect($arguments)->map(function (mixed $value) {
            return self::value($value);
        }));
    }

    public function get(): mixed
    {
        return $this->value = self::value($this->value, $this->arguments);
    }
}
