<?php

declare(strict_types=1);

namespace Purr\Collection;

trait IntImmutableCollectionTrait
{
    use IntCollectionTrait;

    public function abs(): static
    {
        return new static(...array_map(static fn (int $i): int => abs($i), $this->collection));
    }

    public function multiply(int $factor): static
    {
        return new static(...array_map(static fn (int $i): int => $i * $factor, $this->collection));
    }

    public function negativeValues(): static
    {
        return new static(...$this->filter(static fn (int $i): bool => $i < 0));
    }

    public function notZeroValues(): static
    {
        return new static(...$this->filter(static fn (int $i): bool => 0 !== $i));
    }

    public function positiveValues(): static
    {
        return new static(...$this->filter(static fn (int $i): bool => $i > 0));
    }

    public function sortAsc(): static
    {
        $sorted = $this->collection;
        sort($sorted);

        return new static(...$sorted);
    }

    public function sortDesc(): static
    {
        $sorted = $this->collection;
        rsort($sorted);

        return new static(...$sorted);
    }

    public function diff(IntCollectionInterface $collection): static
    {
        return new static(...array_diff($this->collection, $collection->toArray()));
    }

    public function intersect(IntCollectionInterface $collection): static
    {
        return new static(...array_intersect($this->collection, $collection->toArray()));
    }
}
