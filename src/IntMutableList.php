<?php

declare(strict_types=1);

namespace Purr\Collection;

use Purr\Collection\Exception\InvalidArgumentTypeException;

/**
 * @template-extends AbstractMutableList<int>
 */
class IntMutableList extends AbstractMutableList implements IntCollectionInterface
{
    use IntCollectionTrait;

    public function __construct(int ...$numbers)
    {
        parent::__construct($numbers);
    }

    public function abs(): static
    {
        /** @var array<int<0, max>, int> $transformed */
        $transformed = array_values(array_map(static fn (int $i): int => abs($i), $this->collection));
        $this->collection = $transformed;

        return $this;
    }

    public function add(int ...$numbers): static
    {
        /** @var array<int<0, max>, int> $merged */
        $merged = array_values([...$this->collection, ...$numbers]);
        $this->collection = $merged;

        return $this;
    }

    public function multiply(int $factor): static
    {
        /** @var array<int<0, max>, int> $transformed */
        $transformed = array_values(array_map(static fn (int $i): int => $i * $factor, $this->collection));
        $this->collection = $transformed;

        return $this;
    }

    public function negativeValues(): static
    {
        /** @var array<int<0, max>, int> $filtered */
        $filtered = array_values(array_filter($this->collection, static fn (int $i): bool => $i < 0));
        $this->collection = $filtered;

        return $this;
    }

    public function notZeroValues(): static
    {
        /** @var array<int<0, max>, int> $filtered */
        $filtered = array_values(array_filter($this->collection, static fn (int $i): bool => 0 !== $i));
        $this->collection = $filtered;

        return $this;
    }

    public function positiveValues(): static
    {
        /** @var array<int<0, max>, int> $filtered */
        $filtered = array_values(array_filter($this->collection, static fn (int $i): bool => $i > 0));
        $this->collection = $filtered;

        return $this;
    }

    public function sortAsc(): static
    {
        sort($this->collection);

        return $this;
    }

    public function sortDesc(): static
    {
        rsort($this->collection);

        return $this;
    }

    protected function ensureType(mixed $value): void
    {
        if (!is_int($value)) {
            throw new InvalidArgumentTypeException(type: gettype($value), expects: 'int');
        }
    }
}
