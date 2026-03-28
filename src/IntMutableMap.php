<?php

declare(strict_types=1);

namespace Purr\Collection;

use Purr\Collection\Exception\InvalidArgumentTypeException;

/**
 * @phpstan-consistent-constructor
 *
 * @template-extends AbstractMutableMap<int>
 */
class IntMutableMap extends AbstractMutableMap implements IntCollectionInterface
{
    use IntCollectionTrait;

    public function __construct(int ...$numbers)
    {
        parent::__construct($numbers);
    }

    public function abs(): static
    {
        array_walk($this->collection, static fn (int &$v) => $v = abs($v));

        return $this;
    }

    public function multiply(int $factor): static
    {
        array_walk($this->collection, static fn (int &$v) => $v *= $factor);

        return $this;
    }

    public function negativeValues(): static
    {
        $this->collection = array_filter($this->collection, static fn (int $i): bool => $i < 0);

        return $this;
    }

    public function notZeroValues(): static
    {
        $this->collection = array_filter($this->collection, static fn (int $i): bool => 0 !== $i);

        return $this;
    }

    public function positiveValues(): static
    {
        $this->collection = array_filter($this->collection, static fn (int $i): bool => $i > 0);

        return $this;
    }

    public function sortAsc(): static
    {
        asort($this->collection);

        return $this;
    }

    public function sortDesc(): static
    {
        arsort($this->collection);

        return $this;
    }

    public function diff(IntCollectionInterface $collection): static
    {
        $this->collection = array_diff($this->collection, $collection->toArray());

        return $this;
    }

    public function intersect(IntCollectionInterface $collection): static
    {
        $this->collection = array_intersect($this->collection, $collection->toArray());

        return $this;
    }

    protected function ensureType(mixed $value): void
    {
        if (!is_int($value)) {
            throw new InvalidArgumentTypeException(type: gettype($value), expects: 'int');
        }
    }
}
