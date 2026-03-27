<?php

declare(strict_types=1);

namespace Purr\Collection;

use ArrayAccess;
use Purr\Collection\Exception\IndexOutOfBoundsException;
use Purr\Collection\Exception\InvalidArgumentTypeException;

/**
 * @phpstan-consistent-constructor
 *
 * @template TValue
 *
 * @template-extends AbstractList<TValue>
 *
 * @template-implements ArrayAccess<array-key,TValue>
 */
abstract class AbstractMutableList extends AbstractList implements \ArrayAccess
{
    /**
     * @param int $offset
     *
     * @throws InvalidArgumentTypeException when offset is not int
     */
    final public function offsetExists(mixed $offset): bool
    {
        $this->ensureIntOffset($offset);

        return isset($this->collection[$offset]);
    }

    /**
     * @param int $offset
     *
     * @throws InvalidArgumentTypeException when offset is not int
     */
    final public function offsetGet(mixed $offset): mixed
    {
        $this->ensureIntOffset($offset);

        return $this->collection[$offset];
    }

    /**
     * @param int $offset
     *
     * @throws InvalidArgumentTypeException when offset is not int
     */
    final public function offsetUnset(mixed $offset): void
    {
        $this->ensureIntOffset($offset);

        unset($this->collection[$offset]);
        /** @var array<int<0, max>, TValue> $reindexed */
        $reindexed = array_values($this->collection);
        $this->collection = $reindexed;
    }

    /**
     * @param null|int $offset
     *
     * @throws InvalidArgumentTypeException when offset is not int or value type is invalid
     * @throws IndexOutOfBoundsException    when offset is out of bounds
     */
    final public function offsetSet(mixed $offset, mixed $value): void
    {
        if (null !== $offset) {
            $this->ensureIntOffset($offset);
            $this->ensureIndexInBounds($offset);
        }

        $this->ensureType($value);

        if (null === $offset) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    /**
     * @throws IndexOutOfBoundsException    when offset is out of list bounds
     * @throws InvalidArgumentTypeException when value type is invalid
     */
    final public function insert(int $offset, mixed ...$values): static
    {
        $size = count($this->collection);

        if ($offset < 0 || $offset > $size) {
            throw new IndexOutOfBoundsException($offset, $size);
        }

        foreach ($values as $value) {
            $this->ensureType($value);
        }

        /** @var array<int<0, max>, TValue> $merged */
        $merged = array_values([
            ...array_slice($this->collection, 0, $offset),
            ...$values,
            ...array_slice($this->collection, $offset),
        ]);
        $this->collection = $merged;

        return $this;
    }

    final public function remove(mixed $value): static
    {
        $key = array_search($value, $this->collection, true);

        if (false !== $key) {
            unset($this->collection[$key]);
            /** @var array<int<0, max>, TValue> $reindexed */
            $reindexed = array_values($this->collection);
            $this->collection = $reindexed;
        }

        return $this;
    }

    final public function removeAll(mixed ...$values): static
    {
        /** @var array<int<0, max>, TValue> $filtered */
        $filtered = array_values(
            array_filter(
                $this->collection,
                static fn (mixed $item): bool => !in_array($item, $values, true)
            )
        );
        $this->collection = $filtered;

        return $this;
    }

    final public function clear(): static
    {
        $this->collection = [];

        return $this;
    }

    /**
     * @throws InvalidArgumentTypeException when value type is invalid
     */
    final public function prepend(mixed ...$values): static
    {
        foreach ($values as $value) {
            $this->ensureType($value);
        }

        /** @var array<int<0, max>, TValue> $merged */
        $merged = array_values([...$values, ...$this->collection]);
        $this->collection = $merged;

        return $this;
    }

    final public function reverse(): static
    {
        /** @var array<int<0, max>, TValue> $reversed */
        $reversed = array_values(array_reverse($this->collection));
        $this->collection = $reversed;

        return $this;
    }

    /**
     * @return null|TValue
     */
    final public function pop(): mixed
    {
        if ([] === $this->collection) {
            return null;
        }

        return array_pop($this->collection);
    }

    /**
     * @return null|TValue
     */
    final public function shift(): mixed
    {
        if ([] === $this->collection) {
            return null;
        }

        return array_shift($this->collection);
    }

    /**
     * @throws InvalidArgumentTypeException
     */
    abstract protected function ensureType(mixed $value): void;

    /**
     * @throws InvalidArgumentTypeException when offset is not int
     */
    private function ensureIntOffset(mixed $offset): void
    {
        if (!is_int($offset)) {
            throw new InvalidArgumentTypeException(type: gettype($offset), expects: 'int');
        }
    }

    /**
     * @phpstan-assert int<0, max> $offset
     *
     * @throws IndexOutOfBoundsException when offset is out of list bounds
     */
    private function ensureIndexInBounds(int $offset): void
    {
        if (!array_key_exists($offset, $this->collection)) {
            throw new IndexOutOfBoundsException($offset, count($this->collection));
        }
    }
}
