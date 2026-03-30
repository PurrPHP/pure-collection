<?php

declare(strict_types=1);

namespace Purr\Collection;

trait IntCollectionTrait
{
    /** @var int[] */
    protected array $collection = [];

    /**
     * @param non-empty-string $separator
     */
    public static function fromString(string $string, string $separator): static
    {
        if ('' === $string) {
            return new static();
        }

        $values = explode($separator, $string);

        return new static(...array_map('intval', $values));
    }

    public function avg(): ?float
    {
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

    public function add(int ...$numbers): static
    {
        $newItems = [];

        foreach ($numbers as $k => $number) {
            if (!$this->has($number)) {
                $newItems[$k] = $number;
            }
        }

        return new static(...$this->collection, ...$newItems);
    }

    public function prepend(int ...$numbers): static
    {
        $newItems = [];

        foreach ($numbers as $k => $number) {
            if (!$this->has($number)) {
                $newItems[$k] = $number;
            }
        }

        return new static(...$newItems, ...$this->collection);
    }

    public function remove(int ...$numbers): static
    {
        $newItems = [];

        foreach ($this->collection as $k => $number) {
            if (in_array($number, $numbers, true)) {
                continue;
            }

            $newItems[$k] = $number;
        }

        return new static(...$newItems);
    }

    public function diff(IntCollectionInterface $collection): static
    {
        return new static(...array_diff($this->collection, $collection->toArray()));
    }

    public function intersect(IntCollectionInterface $collection): static
    {
        return new static(...array_intersect($this->collection, $collection->toArray()));
    }

    public function toStringList(): StringList
    {
        return StringList::fromInts(...$this->collection);
    }

    public function toStringSet(): StringSet
    {
        return StringSet::fromInts(...$this->collection);
    }

    protected function isSupportedType(mixed $value): bool
    {
        return is_int($value);
    }

    protected function getId(mixed $value): int|string
    {
        return $value;
    }
}
