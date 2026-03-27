# Common Collection Methods

All collections implement [`CollectionInterface`](../src/CollectionInterface.php), which extends `Countable` and `Iterator`. The default implementations are spread across a hierarchy of abstract classes:

| Abstract class | Extends | Role |
|----------------|---------|------|
| [`AbstractCollection`](../src/AbstractCollection.php) | — | Base implementation of all `CollectionInterface` methods (searching, predicates, filtering, transforming, grouping, iteration) |
| [`AbstractList`](../src/AbstractList.php) | `AbstractCollection` | Adds list-specific methods (`indexOf`, `lastIndexOf`) and list-flavoured `groupBy` |
| [`AbstractSet`](../src/AbstractSet.php) | `AbstractList` | Extends `AbstractList`; deduplicates values on construction |
| [`AbstractMap`](../src/AbstractMap.php) | `AbstractCollection` | Provides map-flavoured `groupBy` for associative (key → value) collections |
| [`AbstractMutableList`](../src/AbstractMutableList.php) | `AbstractList` | Adds `ArrayAccess` and all mutating operations (`insert`, `remove`, `pop`, `shift`, `reverse`, `clear`, etc.) |
| [`AbstractMutableMap`](../src/AbstractMutableMap.php) | `AbstractCollection` | Adds `ArrayAccess` and mutating operations for maps |

This document covers the methods available on every collection class.

## Searching

> Source: [`CollectionInterface`](../src/CollectionInterface.php) · [`AbstractCollection`](../src/AbstractCollection.php)

### `findFirst(?callable $predicate = null): mixed`

Returns the first element in the collection. If a predicate is provided, returns the first element that satisfies it. Returns `null` if the collection is empty or no element matches.

```php
$list = new IntList(1, 2, 3, 4, 5);

$list->findFirst();                            // 1
$list->findFirst(fn(int $n): bool => $n > 3); // 4
```

### `findLast(?callable $predicate = null): mixed`

Returns the last element in the collection. If a predicate is provided, returns the last element that satisfies it. Returns `null` if nothing matches.

```php
$list->findLast();                            // 5
$list->findLast(fn(int $n): bool => $n < 4); // 3
```

### `findFirstAfter(mixed $needle): mixed`

Returns the element immediately following `$needle`. Returns `null` if `$needle` is not found or is the last element.

```php
$list->findFirstAfter(3); // 4
$list->findFirstAfter(5); // null
```

### `contains(mixed $needle): bool`

Returns `true` if the collection contains the given value (strict comparison).

```php
$list->contains(3); // true
$list->contains(9); // false
```

### `has(mixed $needle): bool`

Alias for `contains()`.

---

## Predicates

> Source: [`CollectionInterface`](../src/CollectionInterface.php) · [`AbstractCollection`](../src/AbstractCollection.php)

### `any(callable $predicate): bool`

Returns `true` if **at least one** element satisfies the predicate.

```php
$list->any(fn(int $n): bool => $n > 4); // true
```

### `all(callable $predicate): bool`

Returns `true` if **all** elements satisfy the predicate.

```php
$list->all(fn(int $n): bool => $n > 0); // true
```

### `none(callable $predicate): bool`

Returns `true` if **no** element satisfies the predicate.

```php
$list->none(fn(int $n): bool => $n < 0); // true
```

---

## Filtering

> Source: [`CollectionInterface`](../src/CollectionInterface.php) · [`AbstractCollection`](../src/AbstractCollection.php)

### `filter(callable ...$filters): static`

Returns a new collection containing only elements that satisfy **all** provided callables (AND logic).

```php
$list->filter(fn(int $n): bool => $n % 2 === 0); // IntList(2, 4)
```

### `filterNot(callable ...$filters): static`

Returns a new collection containing only elements for which **all** callables return `false`.

```php
$list->filterNot(fn(int $n): bool => $n % 2 === 0); // IntList(1, 3, 5)
```

---

## Transforming

> Source: [`CollectionInterface`](../src/CollectionInterface.php) · [`AbstractCollection`](../src/AbstractCollection.php)

### `map(callable $fn): array`

Applies a callable to every element and returns a plain PHP array of results.

```php
$list->map(fn(int $n): int => $n * 10); // [10, 20, 30, 40, 50]
```

### `reduce(callable $fn, mixed $initial = null): mixed`

Reduces the collection to a single value using a callable accumulator.

```php
$list->reduce(fn(int $carry, int $n): int => $carry + $n, 0); // 15
```

### `sorted(callable $comparator): static`

Returns a new collection sorted by the comparator. The comparator must return `-1`, `0`, or `1`.

```php
$list->sorted(fn(int $a, int $b): int => $b <=> $a); // IntList(5, 4, 3, 2, 1)
```

### `slice(int $offset, int $limit): static`

Returns a new collection with `$limit` elements starting from `$offset`.

```php
$list->slice(1, 3); // IntList(2, 3, 4)
```

### `unique(): static`

Returns a new collection with duplicate values removed.

```php
(new IntList(1, 2, 2, 3, 3))->unique(); // IntList(1, 2, 3)
```

---

## Grouping & Chunking

> Source: [`CollectionInterface`](../src/CollectionInterface.php) · [`AbstractCollection`](../src/AbstractCollection.php) · [`AbstractList`](../src/AbstractList.php) · [`AbstractMap`](../src/AbstractMap.php)

### `groupBy(callable $keyCallable): array`

Groups elements by the string key returned by the callable. Lists produce `array<string, list<TValue>>`, maps produce `array<string, array<key, TValue>>`.

```php
$list->groupBy(fn(int $n): string => $n % 2 === 0 ? 'even' : 'odd');
// ['odd' => [1, 3, 5], 'even' => [2, 4]]
```

### `flattenGroupBy(callable $keyCallable): array`

Similar to `groupBy()`, but each key maps to only the **last** element with that key (later elements overwrite earlier ones).

```php
$list->flattenGroupBy(fn(int $n): string => $n % 2 === 0 ? 'even' : 'odd');
// ['odd' => 5, 'even' => 4]
```

### `chunks(int $size): array`

Splits the collection into an array of smaller collections, each of length `$size`. Throws `InvalidArgumentException` if `$size <= 0`.

```php
$list->chunks(2); // [IntList(1, 2), IntList(3, 4), IntList(5)]
```

---

## Sizing & State

> Source: [`CollectionInterface`](../src/CollectionInterface.php) · [`AbstractCollection`](../src/AbstractCollection.php)

### `count(): int`

Returns the number of elements. Fulfils the `Countable` contract, so `count($collection)` also works.

```php
$list->count(); // 5
count($list);   // 5
```

### `isEmpty(): bool`

Returns `true` if the collection has no elements.

### `isNotEmpty(): bool`

Returns `true` if the collection has at least one element.

---

## Converting

> Source: [`CollectionInterface`](../src/CollectionInterface.php) · [`AbstractCollection`](../src/AbstractCollection.php)

### `toArray(): array`

Returns the underlying PHP array. For lists the keys are zero-based integers; for maps the original string/int keys are preserved.

```php
$list->toArray(); // [1, 2, 3, 4, 5]
```

---

## List-only Methods

> Source: [`AbstractList`](../src/AbstractList.php)

The following methods are available on list-based classes (`IntList`, `IntSet`, `StringList`, `StringSet`, and their variants) but not on map-based classes.

### `indexOf(mixed $value): ?int`

Returns the index of the **first** occurrence of `$value`, or `null` if not found.

```php
(new IntList(10, 20, 30))->indexOf(20); // 1
```

### `lastIndexOf(mixed $value): ?int`

Returns the index of the **last** occurrence of `$value`, or `null` if not found.

```php
(new IntList(1, 2, 1))->lastIndexOf(1); // 2
```

---

## Mutable List Methods

> Source: [`AbstractMutableList`](../src/AbstractMutableList.php)

`IntMutableList` additionally implements `ArrayAccess` and provides the following mutating operations. All methods return `$this` for fluent chaining unless otherwise noted.

### `ArrayAccess`

```php
$list = new IntMutableList(1, 2, 3);

isset($list[0]);   // true
$list[0];          // 1
$list[0] = 10;     // replace element at index 0
$list[] = 4;       // append
unset($list[1]);   // remove by index (re-indexes remaining elements)
```

> Offset must be an `int`. Using a non-integer offset throws `InvalidArgumentTypeException`.  
> Setting an out-of-bounds index throws `IndexOutOfBoundsException`.

### `insert(int $offset, mixed ...$values): static`

Inserts one or more values at the given position, shifting existing elements. Throws `IndexOutOfBoundsException` if `$offset` is out of range.

```php
$list->insert(1, 99); // IntMutableList(1, 99, 2, 3)
```

### `prepend(mixed ...$values): static`

Inserts one or more values at the beginning of the list.

```php
$list->prepend(0); // IntMutableList(0, 1, 2, 3)
```

### `remove(mixed $value): static`

Removes the **first** occurrence of `$value` and re-indexes the list. Does nothing if the value is not found.

```php
$list->remove(2); // IntMutableList(1, 3)
```

### `removeAll(mixed ...$values): static`

Removes **all** occurrences of the given values.

```php
$list->removeAll(1, 3); // IntMutableList(2)
```

### `pop(): mixed`

Removes and returns the **last** element. Returns `null` if the list is empty.

```php
$list->pop(); // 3  →  list is now IntMutableList(1, 2)
```

### `shift(): mixed`

Removes and returns the **first** element. Returns `null` if the list is empty.

```php
$list->shift(); // 1  →  list is now IntMutableList(2, 3)
```

### `reverse(): static`

Reverses the order of elements in place.

```php
$list->reverse(); // IntMutableList(3, 2, 1)
```

### `clear(): static`

Removes all elements from the list.

```php
$list->clear(); // IntMutableList()
```

---

## Iterator

> Source: [`AbstractCollection`](../src/AbstractCollection.php)

All collections implement `Iterator`, so they can be used directly in `foreach` loops:

```php
foreach (new IntList(1, 2, 3) as $index => $value) {
    // $index: 0, 1, 2 — $value: 1, 2, 3
}
```

