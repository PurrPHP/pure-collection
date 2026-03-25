<?php

declare(strict_types=1);

namespace Purr\Collection;

use Purr\Collection\Exception\InvalidArgumentException;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @template-implements CollectionInterface<TValue>
 */
abstract class AbstractCollection implements CollectionInterface
{
    /**
     * @var array<TKey, TValue>
     *
     * @readonly
     *
     * Cannot use native readonly property cause of $this->rewind(), $this->next()
     */
    protected array $collection = [];

    /**
     * @param array<TKey, TValue> $items
     */
    protected function __construct(array $items)
    {
        $this->collection = $items;
    }

    /**
     * @return null|TValue
     */
    final public function findFirst(?callable $predicate = null): mixed
    {
        if (null === $predicate) {
            // @psalm-suppress PossiblyNullArrayOffset
            return $this->collection[array_key_first($this->collection)] ?? null;
        }

        foreach ($this->collection as $item) {
            if ($predicate($item)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param TValue $needle
     */
    public function findFirstAfter(mixed $needle): mixed
    {
        $needleKey = array_search($needle, $this->collection, true);

        if (false === $needleKey) {
            return null;
        }

        $keys = array_keys($this->collection);
        $keyPosition = array_search($needleKey, $keys, true);

        /**
         * Impossible here.
         *
         * @psalm-suppress PossiblyFalseOperand
         */
        $nextKey = $keys[$keyPosition + 1] ?? null;

        return null !== $nextKey ? $this->collection[$nextKey] : null;
    }

    /**
     * @param null|callable(TValue): bool $predicate
     *
     * @return null|TValue
     */
    final public function findLast(?callable $predicate = null): mixed
    {
        if (null === $predicate) {
            // @psalm-suppress PossiblyNullArrayOffset
            return $this->collection[array_key_last($this->collection)] ?? null;
        }

        $result = null;

        foreach ($this->collection as $item) {
            if ($predicate($item)) {
                $result = $item;
            }
        }

        return $result;
    }

    public function contains(mixed $needle): bool
    {
        return in_array($needle, $this->collection, true);
    }

    /**
     * @param callable(TValue $value): bool $predicate
     */
    final public function any(callable $predicate): bool
    {
        foreach ($this->collection as $item) {
            if ($predicate($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param callable(TValue $value): bool $predicate
     */
    final public function all(callable $predicate): bool
    {
        foreach ($this->collection as $item) {
            if (!$predicate($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param callable(TValue $value): bool $predicate
     */
    final public function none(callable $predicate): bool
    {
        foreach ($this->collection as $item) {
            if ($predicate($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param callable(TValue $value): string $keyCallable
     *
     * @return array<string, TValue>
     */
    final public function flattenGroupBy(callable $keyCallable): array
    {
        $result = [];
        foreach ($this->collection as $item) {
            $key = $keyCallable($item);
            $result[$key] = $item;
        }

        return $result;
    }

    /**
     * @param callable(TValue):bool ...$filters
     */
    final public function filter(callable ...$filters): static
    {
        /** @var array<TKey, TValue> $filtered */
        $filtered = $this->collection;

        foreach ($filters as $filter) {
            $filtered = array_filter($filtered, $filter);
        }

        return new static(...$filtered);
    }

    /**
     * @param callable(TValue):bool ...$filters
     */
    final public function filterNot(callable ...$filters): static
    {
        /** @var array<TKey, TValue> $filtered */
        $filtered = $this->collection;

        foreach ($filters as $filter) {
            $filtered = array_filter(
                $filtered,
                /** @param TValue $item */
                static fn ($item) => !$filter($item)
            );
        }

        return new static(...$filtered);
    }

    final public function count(): int
    {
        return count($this->collection);
    }

    final public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    final public function isNotEmpty(): bool
    {
        return $this->count() > 0;
    }

    final public function unique(): static
    {
        $unique = $this->filterUniqValues($this->collection);

        return new static(...$unique);
    }

    /**
     * @template T
     *
     * @param callable(TValue): T $fn
     *
     * @return array<TKey, T>
     */
    final public function map(callable $fn): array
    {
        return array_map($fn, $this->collection);
    }

    /**
     * @template K
     *
     * @param callable(K, TValue): K $fn
     * @param K                      $initial
     *
     * @return K
     */
    final public function reduce(callable $fn, mixed $initial = null): mixed
    {
        return array_reduce($this->collection, $fn, $initial);
    }

    final public function slice(int $offset, int $limit): static
    {
        return new static(...array_slice($this->collection, $offset, $limit, true));
    }

    /**
     * @param callable(TValue,TValue):int $comparator
     */
    final public function sorted(callable $comparator): static
    {
        $sortedCollection = $this->collection;
        uasort($sortedCollection, $comparator);

        return new static(...$sortedCollection);
    }

    /**
     * Returns array of collection elements.
     *
     * @return array<TKey, TValue>
     */
    final public function toArray(): array
    {
        return $this->collection;
    }

    final public function rewind(): void
    {
        reset($this->collection);
    }

    /**
     * @return TValue
     */
    final public function current(): mixed
    {
        return current($this->collection);
    }

    final public function key(): int|string|null
    {
        return key($this->collection);
    }

    final public function next(): void
    {
        next($this->collection);
    }

    final public function valid(): bool
    {
        $key = key($this->collection);
        if (null === $key) {
            return false;
        }

        return array_key_exists($key, $this->collection);
    }

    /**
     * @psalm-suppress InvalidReturnType
     *
     * @return array<int, static>
     */
    final public function chunks(int $size): array
    {
        if ($size <= 0) {
            throw new InvalidArgumentException('Chunk size must be greater than zero.');
        }

        $chunks = array_chunk($this->collection, $size, true);
        $collectionsArray = array_fill(0, count($chunks), null);

        foreach ($chunks as $index => $chunkItem) {
            $collectionsArray[$index] = new static(...$chunkItem);
        }

        return $collectionsArray;
    }

    /**
     * @param TValue[] $items
     *
     * @return TValue[]
     */
    abstract protected function filterUniqValues(array $items): array;
}
