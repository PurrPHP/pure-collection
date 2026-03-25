<?php

declare(strict_types=1);

namespace Purr\Collection;

use Purr\Collection\Exception\IndexOutOfBoundsException;
use Purr\Collection\Exception\InvalidArgumentTypeException;

/**
 * @template TValue
 *
 * @template-extends AbstractList<TValue>
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
        $this->collection = array_values($this->collection);
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
     * @throws InvalidArgumentTypeException
     */
    abstract protected function ensureType(mixed $value): void;

    /**
     * @throws InvalidArgumentTypeException when offset is not int
     */
    private function ensureIntOffset(mixed $offset): void
    {
        if (!is_int($offset)) {
            throw new InvalidArgumentTypeException(
                sprintf('Invalid offset type. Got %s. Expects int', gettype($offset))
            );
        }
    }

    /**
     * @throws IndexOutOfBoundsException when offset is out of list bounds
     */
    private function ensureIndexInBounds(int $offset): void
    {
        if (!array_key_exists($offset, $this->collection)) {
            throw new IndexOutOfBoundsException(
                sprintf('Index %d is out of bounds for list of size %d', $offset, count($this->collection))
            );
        }
    }
}
