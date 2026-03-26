<?php

declare(strict_types=1);

namespace Purr\Collection;

trait IntCollectionTrait
{
    /**
     * @param non-empty-string $separator
     */
    public static function fromString(string $string, string $separator): self
    {
        if ('' === $string) {
            return new self();
        }

        $values = explode($separator, $string);

        return new static(...array_map('intval', $values));
    }

    public function avg(): ?float
    {
        /** @var int[] $this->collection */
        if ([] === $this->collection) {
            return null;
        }

        return array_sum($this->collection) / count($this->collection);
    }

    public function max(): ?int
    {
        // @var int[] $this->collection
        return [] === $this->collection ? null : max($this->collection);
    }

    public function median(): ?float
    {
        if ([] === $this->collection) {
            return null;
        }

        $sorted = $this->collection;
        sort($sorted);
        $count = count($sorted);
        $mid = (int) ($count / 2);

        if (0 === $count % 2) {
            return ($sorted[$mid - 1] + $sorted[$mid]) / 2;
        }

        return (float) $sorted[$mid];
    }

    public function min(): ?int
    {
        // @var int[] $this->collection
        return [] === $this->collection ? null : min($this->collection);
    }

    public function product(): int
    {
        return (int) array_product($this->collection);
    }

    public function range(): ?int
    {
        if ([] === $this->collection) {
            return null;
        }

        return max($this->collection) - min($this->collection);
    }

    public function sum(): int
    {
        // @var int[] $this->collection
        return array_sum($this->collection);
    }

    public function join(string $separator = ''): string
    {
        return implode($separator, $this->collection);
    }

    public function implode(string $separator = ''): string
    {
        return $this->join($separator);
    }

    public function toStringList(): StringList
    {
        return StringList::fromInts(...$this->collection);
    }

    public function toStringSet(): StringSet
    {
        return StringSet::fromInts(...$this->collection);
    }

    protected function filterUniqValues(array $items): array
    {
        return array_unique($items);
    }
}
