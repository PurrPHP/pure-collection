<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @phpstan-consistent-constructor
 */
class IntMutableSet extends IntMutableList
{
    use IntCollectionTrait;

    public function __construct(int ...$numbers)
    {
        $unique = $this->filterUniqValues($numbers);

        parent::__construct(...$unique);
    }

    public function abs(): static
    {
        parent::abs();
        $this->normalizeSet();

        return $this;
    }

    public function add(int ...$numbers): static
    {
        if (empty($numbers)) {
            return $this;
        }

        parent::add(...$numbers);
        $this->normalizeSet();

        return $this;
    }

    /**
     * @param int ...$values
     */
    public function insert(int $offset, ...$values): static
    {
        parent::insert($offset, ...$values);
        $this->normalizeSet();

        return $this;
    }

    private function normalizeSet(): void
    {
        /** @var array<int<0, max>, int> $unique */
        $unique = array_values($this->filterUniqValues($this->collection));

        $this->collection = $unique;
    }
}
