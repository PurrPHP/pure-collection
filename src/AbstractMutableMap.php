<?php

declare(strict_types=1);

namespace Purr\Collection;

use ArrayAccess;
use Purr\Collection\Exception\InvalidArgumentTypeException;

/**
 * @phpstan-consistent-constructor
 *
 * @template TValue
 *
 * @template-extends AbstractCollection<array-key,TValue>
 *
 * @template-implements ArrayAccess<array-key,TValue>
 */
abstract class AbstractMutableMap extends AbstractCollection implements ArrayAccess
{
    /**
     * @param callable(TValue):string $keyCallable
     *
     * @return array<string,array<array-key,TValue>>
     */
    final public function groupBy(callable $keyCallable): array
    {
        $result = [];
        foreach ($this->collection as $key => $item) {
            $listKey = $keyCallable($item);
            $result[$listKey][$key] = $item;
        }

        return $result;
    }

    /**
     * @param array-key $offset
     */
    final public function offsetExists(mixed $offset): bool
    {
        return isset($this->collection[$offset]);
    }

    /**
     * @param array-key $offset
     */
    final public function offsetUnset(mixed $offset): void
    {
        unset($this->collection[$offset]);
    }

    /**
     * @param array-key $offset
     *
     * @return TValue
     */
    final public function offsetGet(mixed $offset): mixed
    {
        return $this->collection[$offset];
    }

    /**
     * @param null|array-key $offset
     *
     * @throws InvalidArgumentTypeException when value type is invalid
     */
    final public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->ensureType($value);

        if (null === $offset) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    final public function clear(): static
    {
        $this->collection = [];

        return $this;
    }

    /**
     * @throws InvalidArgumentTypeException
     */
    abstract protected function ensureType(mixed $value): void;
}
