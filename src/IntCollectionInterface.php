<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template-extends CollectionInterface<int>
 */
interface IntCollectionInterface extends CollectionInterface
{
    public static function fromString(string $string, string $separator): static;

    /** Returns the arithmetic mean, or null if the collection is empty. */
    public function avg(): ?float;

    /** Returns the largest value, or null if the collection is empty. */
    public function max(): ?int;

    /**
     * Returns the median value, or null if the collection is empty.
     * For even-sized collections returns the average of the two middle values.
     */
    public function median(): ?float;

    /** Returns the smallest value, or null if the collection is empty. */
    public function min(): ?int;

    /** Returns the product of all elements. Returns 1 for an empty collection (identity element). */
    public function product(): int;

    /**
     * Returns the range (max − min), or null if the collection is empty.
     * Represents the spread between the largest and smallest values.
     */
    public function range(): ?int;

    /** Returns the sum of all elements. Returns 0 for an empty collection. */
    public function sum(): int;

    /** Returns a collection with each element replaced by its absolute value. */
    public function abs(): static;

    /** Returns a collection with each element multiplied by the given factor. */
    public function multiply(int $factor): static;

    /** Returns a new collection sorted in ascending order. */
    public function sortAsc(): static;

    /** Returns a new collection sorted in descending order. */
    public function sortDesc(): static;

    /** Returns a collection containing only values strictly less than zero. */
    public function negativeValues(): static;

    /** Returns a collection with all zero values removed (keeps both positive and negative). */
    public function notZeroValues(): static;

    /** Returns a collection containing only values strictly greater than zero. */
    public function positiveValues(): static;

    /**
     * Returns a collection of values from this collection that are not present in the given collection.
     *
     * $a = new IntList(1,2);
     * $a->diff(new IntList(1,3)) returns "2".
     */
    public function diff(IntCollectionInterface $collection): static;

    /**
     * Returns a collection of values that are present in both this collection and the given collection.
     *
     * $a = new IntList(1,2);
     * $a->intersect(new IntList(1,3)) returns "1".
     */
    public function intersect(IntCollectionInterface $collection): static;

    public function add(int ...$numbers): static;

    public function prepend(int ...$numbers): static;

    public function remove(int ...$numbers): static;

    /**
     * Returns string with merged ints.
     * $a = new IntList(1,2);
     * $a->join(',') returns "1,2".
     */
    public function join(string $separator = ''): string;

    /**
     * Alias for join.
     *
     * @see self::join
     */
    public function implode(string $separator = ''): string;

    public function toStringList(): StringList;

    public function toStringSet(): StringSet;
}
