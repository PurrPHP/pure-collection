<?php
declare(strict_types=1);

namespace Purr\Collection;

use Countable;
use Iterator;

/**
 * @template TValue
 * @template-extends Iterator<array-key, TValue>
 */
interface CollectionInterface extends Countable, Iterator
{
    /**
     * If predicate provided returns last collection item satisfied predicate.
     * Without predicate returns last item from collection.
     *
     * @param callable(TValue): bool|null $predicate
     * @return TValue|null
     */
    public function findFirst(?callable $predicate = null): mixed;

    /**
     * @param TValue $needle
     * @return TValue|null
     */
    public function findFirstAfter(mixed $needle): mixed;

    /**
     * If predicate provided returns last collection item satisfied predicate.
     * Without predicate returns last item from collection.
     *
     * @param callable(TValue): bool|null $predicate
     * @return TValue|null
     */
    public function findLast(?callable $predicate = null): mixed;

    /** @param TValue $needle */
    public function contains(mixed $needle): bool;

    /** @param callable(TValue): bool $predicate */
    public function any(callable $predicate): bool;

    /** @param callable(TValue): bool $predicate */
    public function all(callable $predicate): bool;

    /**
     * No one item satisfy predicate
     *
     * @param callable(TValue): bool $predicate
     */
    public function none(callable $predicate): bool;

    /**
     * Groups collection items by some callback result.
     * Lists are grouped into lists, maps are grouped into maps.
     *
     *
     * @param callable(TValue):string $keyCallable
     * @return array<string,list<TValue>>|array<string,array<string,TValue>>
     */
    public function groupBy(callable $keyCallable): array;

    /**
     * @param callable(TValue):string $keyCallable
     * @return array<string,TValue>
     */
    public function flattenGroupBy(callable $keyCallable): array;

    /**
     * Callables joins like and expression.
     *
     * "filter(callable1, callable2)" returns collection of items for each "callable1 && callable2" is true.
     *
     * @param callable(TValue):bool ...$filters
     */
    public function filter(callable ...$filters): static;

    /**
     * Callables joins like and expression.
     *
     * "filterNot(callable1, callable2)" returns collection of items for each "callable1 && callable2" is false.
     *
     * @param callable(TValue):bool ...$filters
     */
    public function filterNot(callable ...$filters): static;

    public function isEmpty(): bool;

    public function isNotEmpty(): bool;

    /**
     * @template TReturn
     *
     * @param callable(TValue):TReturn $fn
     * @return TReturn[]
     */
    public function map(callable $fn): array;

    /**
     * @template K
     * @param callable(K, TValue): K $fn
     * @param K $initial
     * @return K
     */
    public function reduce(callable $fn, mixed $initial = null): mixed;

    /**
     * @psalm-type ComparisonResultType -1|0|1
     * @param callable(TValue,TValue):ComparisonResultType $comparator
     */
    public function sorted(callable $comparator): static;

    public function slice(int $offset, int $limit): static;

    public function toArray(): array;

    public function unique(): static;

    /** @return static[] */
    public function chunks(int $size): array;
}
