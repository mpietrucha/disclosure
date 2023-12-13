<?php

namespace Mpietrucha\Support\Disclosure;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Support\Disclosure\Contracts\ArgumentInterface;

class Argument implements ArgumentInterface
{
    public function __construct(protected mixed $value, protected array $arguments)
    {
    }

    public function __invoke(): mixed
    {
        return $this->get();
    }

    public static function value(mixed $value, array $arguments): mixed
    {
        return collect([$value, ...$arguments])->lazy()->map(function (mixed $value) {
            if ($value instanceof ArgumentInterface) {
                return $value->get();
            }

            return $value;
        })->pipe(function (LazyCollection $arguments) {
            $value = $arguments->first();

            if (! $value instanceof Closure) {
                return $value;
            }

            return $value(...$arguments->collect()->tap(function (Collection $arguments) {
                $arguments->shift();
            }));
        });
    }

    public function get(): mixed
    {
        return $this->value = self::value($this->value, $this->arguments);
    }
}
