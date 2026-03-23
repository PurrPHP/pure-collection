<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template-extends AbstractList<int>
 */
class IntList extends AbstractList implements IntCollectionInterface
{
    public function __construct(int ...$numbers)
    {
        parent::__construct($numbers);
    }

    public function toStringSet(): StringSet
    {
        return StringSet::fromIntList($this->collection);
    }

    public function max(): ?int
    {
        return max($this->collection);
    }

    public function min(): ?int
    {
        return min($this->collection);
    }

    public function notZeroValues(): static
    {
        return new static(...$this->filter(static fn (int $i): bool => 0 !== $i));
    }
}
