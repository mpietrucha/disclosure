<?php

namespace Mpietrucha\Support\Disclosure;

use Closure;
use Spatie\Invade\Invader;
use Mpietrucha\Support\Concerns\Factoryable;
use Mpietrucha\Support\Exception\InvalidArgumentException;

class Disclosure
{
    use Factoryable;

    protected Invader $source;

    protected ?Closure $transformer = null;

    public function __construct(object $source)
    {
        $this->source = new Invader($source);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->source->$name = self::normalize($value, function (mixed $response) use ($name, $value): mixed {
            $this->source->$name = $response;

            $this->source->$name = $value;

            return with($response, $this->transformer);
        });
    }

    public function __call(string $method, array $arguments): self
    {
        $this->transformer = fn (mixed $response) => Transformer::$method($response, ...$arguments);

        return $this;
    }

    public static function __callStatic(string $method, array $arguments): mixed
    {
        [$value] = $arguments + [null];

        return self::normalize($value, function (mixed $response) use ($method, $arguments) {
            $arguments[0] = $response;

            return Transformer::$method(...$arguments);
        });
    }

    public static function normalize(mixed $value, Closure $response): mixed
    {
        if (! $value instanceof Closure) {
            return $response($value);
        }

        return function (mixed ...$arguments) use ($value, $response): mixed {
            $value = value($value, ...$arguments);

            InvalidArgumentException::create()->when(function () use ($value) {
                return $value instanceof Closure;
            })->throw('Nested closures are not allowed in disclosure.');

            return $response($value);
        };
    }
}
