<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\String;

/**
 * A string whose value is computed lazily by a callback.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class LazyString implements \Stringable, \JsonSerializable
{
<<<<<<< HEAD
    private $value;

    /**
     * @param callable|array $callback A callable or a [Closure, method] lazy-callable
     *
     * @return static
     */
    public static function fromCallable($callback, ...$arguments): self
    {
        if (!\is_callable($callback) && !(\is_array($callback) && isset($callback[0]) && $callback[0] instanceof \Closure && 2 >= \count($callback))) {
            throw new \TypeError(sprintf('Argument 1 passed to "%s()" must be a callable or a [Closure, method] lazy-callable, "%s" given.', __METHOD__, get_debug_type($callback)));
        }

        $lazyString = new static();
        $lazyString->value = static function () use (&$callback, &$arguments, &$value): string {
            if (null !== $arguments) {
                if (!\is_callable($callback)) {
                    $callback[0] = $callback[0]();
                    $callback[1] = $callback[1] ?? '__invoke';
                }
                $value = $callback(...$arguments);
                $callback = self::getPrettyName($callback);
=======
    private \Closure|string $value;

    /**
     * @param callable|array $callback A callable or a [Closure, method] lazy-callable
     */
    public static function fromCallable(callable|array $callback, mixed ...$arguments): static
    {
        if (\is_array($callback) && !\is_callable($callback) && !(($callback[0] ?? null) instanceof \Closure || 2 < \count($callback))) {
            throw new \TypeError(sprintf('Argument 1 passed to "%s()" must be a callable or a [Closure, method] lazy-callable, "%s" given.', __METHOD__, '['.implode(', ', array_map('get_debug_type', $callback)).']'));
        }

        $lazyString = new static();
        $lazyString->value = static function () use (&$callback, &$arguments): string {
            static $value;

            if (null !== $arguments) {
                if (!\is_callable($callback)) {
                    $callback[0] = $callback[0]();
                    $callback[1] ??= '__invoke';
                }
                $value = $callback(...$arguments);
                $callback = !\is_scalar($value) && !$value instanceof \Stringable ? self::getPrettyName($callback) : 'callable';
>>>>>>> 682f476e052bf0e537f9b7d2b42ef35fd30ef410
                $arguments = null;
            }

            return $value ?? '';
        };

        return $lazyString;
    }

<<<<<<< HEAD
    /**
     * @param string|int|float|bool|\Stringable $value
     *
     * @return static
     */
    public static function fromStringable($value): self
    {
        if (!self::isStringable($value)) {
            throw new \TypeError(sprintf('Argument 1 passed to "%s()" must be a scalar or a stringable object, "%s" given.', __METHOD__, get_debug_type($value)));
        }

        if (\is_object($value)) {
            return static::fromCallable([$value, '__toString']);
=======
    public static function fromStringable(string|int|float|bool|\Stringable $value): static
    {
        if (\is_object($value)) {
            return static::fromCallable($value->__toString(...));
>>>>>>> 682f476e052bf0e537f9b7d2b42ef35fd30ef410
        }

        $lazyString = new static();
        $lazyString->value = (string) $value;

        return $lazyString;
    }

    /**
     * Tells whether the provided value can be cast to string.
     */
<<<<<<< HEAD
    final public static function isStringable($value): bool
    {
        return \is_string($value) || $value instanceof self || (\is_object($value) ? method_exists($value, '__toString') : \is_scalar($value));
=======
    final public static function isStringable(mixed $value): bool
    {
        return \is_string($value) || $value instanceof \Stringable || \is_scalar($value);
>>>>>>> 682f476e052bf0e537f9b7d2b42ef35fd30ef410
    }

    /**
     * Casts scalars and stringable objects to strings.
     *
<<<<<<< HEAD
     * @param object|string|int|float|bool $value
     *
     * @throws \TypeError When the provided value is not stringable
     */
    final public static function resolve($value): string
=======
     * @throws \TypeError When the provided value is not stringable
     */
    final public static function resolve(\Stringable|string|int|float|bool $value): string
>>>>>>> 682f476e052bf0e537f9b7d2b42ef35fd30ef410
    {
        return $value;
    }

<<<<<<< HEAD
    /**
     * @return string
     */
    public function __toString()
=======
    public function __toString(): string
>>>>>>> 682f476e052bf0e537f9b7d2b42ef35fd30ef410
    {
        if (\is_string($this->value)) {
            return $this->value;
        }

        try {
            return $this->value = ($this->value)();
        } catch (\Throwable $e) {
<<<<<<< HEAD
            if (\TypeError::class === \get_class($e) && __FILE__ === $e->getFile()) {
=======
            if (\TypeError::class === $e::class && __FILE__ === $e->getFile()) {
>>>>>>> 682f476e052bf0e537f9b7d2b42ef35fd30ef410
                $type = explode(', ', $e->getMessage());
                $type = substr(array_pop($type), 0, -\strlen(' returned'));
                $r = new \ReflectionFunction($this->value);
                $callback = $r->getStaticVariables()['callback'];

                $e = new \TypeError(sprintf('Return value of %s() passed to %s::fromCallable() must be of the type string, %s returned.', $callback, static::class, $type));
            }

<<<<<<< HEAD
            if (\PHP_VERSION_ID < 70400) {
                // leverage the ErrorHandler component with graceful fallback when it's not available
                return trigger_error($e, \E_USER_ERROR);
            }

=======
>>>>>>> 682f476e052bf0e537f9b7d2b42ef35fd30ef410
            throw $e;
        }
    }

    public function __sleep(): array
    {
        $this->__toString();

        return ['value'];
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    private function __construct()
    {
    }

    private static function getPrettyName(callable $callback): string
    {
        if (\is_string($callback)) {
            return $callback;
        }

        if (\is_array($callback)) {
            $class = \is_object($callback[0]) ? get_debug_type($callback[0]) : $callback[0];
            $method = $callback[1];
        } elseif ($callback instanceof \Closure) {
            $r = new \ReflectionFunction($callback);

            if (str_contains($r->name, '{closure') || !$class = \PHP_VERSION_ID >= 80111 ? $r->getClosureCalledClass() : $r->getClosureScopeClass()) {
                return $r->name;
            }

            $class = $class->name;
            $method = $r->name;
        } else {
            $class = get_debug_type($callback);
            $method = '__invoke';
        }

        return $class.'::'.$method;
    }
}
