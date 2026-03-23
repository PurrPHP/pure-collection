<?php

declare(strict_types=1);

namespace Purr\Collection;

trait IntCollectionTrait
{
    public function max(): ?int
    {
        /** @var int[] $this->collection */
        return [] === $this->collection ? null : max($this->collection);
    }

    public function min(): ?int
    {
        /** @var int[] $this->collection */
        return [] === $this->collection ? null : min($this->collection);
    }

    public function avg(): ?float
    {
        /** @var int[] $this->collection */
        if ([] === $this->collection) {
            return null;
        }

        return array_sum($this->collection) / count($this->collection);
    }

    public function sum(): int
    {
        /** @var int[] $this->collection */
        return array_sum($this->collection);
    }

    public function notZeroValues(): static
    {
        return new static(...$this->filter(static fn (int $i): bool => 0 !== $i));
    }
}
