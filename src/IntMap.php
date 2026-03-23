<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template-extends AbstractMap<int>
 */
class IntMap extends AbstractMap implements IntCollectionInterface
{
    public function __construct(int ...$numbers)
    {
        parent::__construct($numbers);
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
