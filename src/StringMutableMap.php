<?php

declare(strict_types=1);

namespace Purr\Collection;

use Purr\Collection\Exception\InvalidArgumentTypeException;

/**
 * @phpstan-consistent-constructor
 *
 * @template-extends AbstractMutableMap<string>
 */
class StringMutableMap extends AbstractMutableMap implements StringCollectionInterface
{
    use StringCollectionTrait;

    public function sortAlphabetically(): static
    {
        asort($this->collection);

        return $this;
    }

    public function diff(StringCollectionInterface $collection): static
    {
        $this->collection = array_diff($this->collection, $collection->toArray());

        return $this;
    }

    public function intersect(StringCollectionInterface $collection): static
    {
        $this->collection = array_intersect($this->collection, $collection->toArray());

        return $this;
    }

    protected function ensureType(mixed $value): void
    {
        if (!is_string($value)) {
            throw new InvalidArgumentTypeException(type: gettype($value), expects: 'string');
        }
    }
}
