<?php

namespace Mpietrucha\Support\Disclosure;

use Closure;
use Spatie\Invade\Invader;

class Disclosure
{
    protected Invader $source;

    protected ?Closure $transformer = null;

    public function __construct(object $source)
    {
        $this->source = new Invader($source);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->source->$name = self::normalize($value, function (mixed $response) use ($name) {
            $response = with($response, $this->transformer);

            $this->source->$name = $response;

            return $this->source->$name;
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

        return function (mixed ...$arguments) use ($value, $response) {
            do {
                $value = Argument::value($value, $arguments);
            } while ($value instanceof Closure);

            return $response($value);
        };
    }
}
