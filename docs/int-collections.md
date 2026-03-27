# Int Collections

This document covers all integer-typed collection classes and the methods specific to them.

For methods shared by all collections see [Common Methods](common-methods.md).

## Available Classes


| Class             | Type           | Description                                     |
| ----------------- | -------------- | ----------------------------------------------- |
| `IntList`         | Immutable list | List of integers                                |
| `IntSet`          | Immutable set  | List of **unique** integers                     |
| `IntNotEmptyList` | Immutable list | Same as `IntList`, throws on empty construction |
| `IntNotEmptySet`  | Immutable set  | Same as `IntSet`, throws on empty construction  |
| `IntMap`          | Immutable map  | Associative (key → int) map                     |
| `IntMutableList`  | Mutable list   | Mutable list of integers                        |
| `IntMutableMap`   | Mutable map    | Mutable associative map of integers             |
| `IntMutableSet`   | Mutable set    | Mutable list of **unique** integers             |


### Immutable vs Mutable

**Immutable** classes (`IntList`, `IntSet`, `IntNotEmptyList`, `IntNotEmptySet`, `IntMap`) never modify their internal state. Every transforming method returns a **new** instance.

**Mutable** classes (`IntMutableList`, `IntMutableMap`, `IntMutableSet`) modify their internal state in place and return `$this` for fluent chaining.

### Not-empty Variants

`IntNotEmptyList` and `IntNotEmptySet` throw `InvalidArgumentException` if constructed without arguments:

```php
new IntNotEmptyList();        // throws InvalidArgumentException
new IntNotEmptyList(1, 2, 3); // ok
```

---

## Construction

```php
use Purr\Collection\IntList;
use Purr\Collection\IntSet;
use Purr\Collection\IntMap;
use Purr\Collection\IntMutableList;

$list    = new IntList(1, 2, 3, 4, 5);
$set     = new IntSet(1, 2, 2, 3);          // duplicates removed → [1, 2, 3]
$map     = new IntMap(...['a' => 1, 'b' => 2]);
$mutable = new IntMutableList(1, 2, 3);
```

---

## Static Factory

### `fromString(string $string, string $separator): static`

Parses a delimited string into a collection of integers.

```php
IntList::fromString('1,2,3', ','); // IntList(1, 2, 3)
IntSet::fromString('1-2-3', '-');  // IntSet(1, 2, 3)
```

---

## Aggregation

### `sum(): int`

Returns the sum of all elements. Returns `0` for an empty collection.

```php
(new IntList(1, 2, 3))->sum(); // 6
```

### `avg(): ?float`

Returns the arithmetic mean. Returns `null` for an empty collection.

```php
(new IntList(1, 2, 3))->avg(); // 2.0
```

### `min(): ?int`

Returns the smallest value. Returns `null` for an empty collection.

```php
(new IntList(3, 1, 2))->min(); // 1
```

### `max(): ?int`

Returns the largest value. Returns `null` for an empty collection.

```php
(new IntList(3, 1, 2))->max(); // 3
```

### `median(): ?float`

Returns the median value. For even-sized collections returns the average of the two middle values. Returns `null` for an empty collection.

```php
(new IntList(1, 2, 3, 4))->median(); // 2.5
(new IntList(1, 2, 3))->median();    // 2.0
```

### `product(): int`

Returns the product of all elements. Returns `1` for an empty collection (identity element for multiplication).

```php
(new IntList(2, 3, 4))->product(); // 24
```

### `range(): ?int`

Returns `max - min`. Returns `null` for an empty collection.

```php
(new IntList(1, 5, 3))->range(); // 4
```

---

## Transformation

### `abs(): static`

Returns a collection where every element is replaced by its absolute value.

```php
(new IntList(-1, 2, -3))->abs(); // IntList(1, 2, 3)
```

For **mutable** collections the operation is applied in place.

### `multiply(int $factor): static`

Returns a collection where every element is multiplied by `$factor`.

```php
(new IntList(1, 2, 3))->multiply(3); // IntList(3, 6, 9)
```

---

## Filtering by Sign

### `positiveValues(): static`

Keeps only elements strictly greater than zero.

```php
(new IntList(-1, 0, 2, 3))->positiveValues(); // IntList(2, 3)
```

### `negativeValues(): static`

Keeps only elements strictly less than zero.

```php
(new IntList(-2, -1, 0, 1))->negativeValues(); // IntList(-2, -1)
```

### `notZeroValues(): static`

Removes all zero elements, keeping both positive and negative values.

```php
(new IntList(-1, 0, 1))->notZeroValues(); // IntList(-1, 1)
```

---

## Sorting

### `sortAsc(): static`

Sorts the collection in ascending order.

```php
(new IntList(3, 1, 2))->sortAsc(); // IntList(1, 2, 3)
```

### `sortDesc(): static`

Sorts the collection in descending order.

```php
(new IntList(3, 1, 2))->sortDesc(); // IntList(3, 2, 1)
```

> For immutable classes these methods return a new instance. For mutable classes they sort in place and return `$this`.

---

## Set Operations

### `diff(IntCollectionInterface $collection): static`

Returns a collection of elements present in `$this` but **not** in `$collection`.

```php
$a = new IntList(1, 2, 3, 4);
$b = new IntList(3, 4, 5);

$a->diff($b); // IntList(1, 2)
```

### `intersect(IntCollectionInterface $collection): static`

Returns a collection of elements present in **both** `$this` and `$collection`.

```php
$a->intersect($b); // IntList(3, 4)
```

---

## Joining

### `join(string $separator = ''): string`

Concatenates all elements into a string, separated by `$separator`.

```php
(new IntList(1, 2, 3))->join(',');  // "1,2,3"
(new IntList(1, 2, 3))->join();     // "123"
```

### `implode(string $separator = ''): string`

Alias for `join()`.

---

## Converting to String Collections

### `toStringList(): StringList`

Converts the collection to a `StringList` where each integer becomes its string representation.

```php
(new IntList(1, 2, 3))->toStringList(); // StringList('1', '2', '3')
```

### `toStringSet(): StringSet`

Converts the collection to a `StringSet`.

```php
(new IntList(1, 2, 2))->toStringSet(); // StringSet('1', '2')
```

---

## Mutable Collections

All methods documented in the sections above (aggregation, transformation, filtering, sorting, set operations) work the same way on mutable collections, but **modify the instance in place** instead of returning a new one.

---

### `IntMutableList`

#### `add(int ...$numbers): static`

Appends one or more integers to the end of the list. Returns `$this`.

```php
$list = new IntMutableList(1, 2);
$list->add(3, 4); // IntMutableList(1, 2, 3, 4)
```

For other mutation methods (`insert`, `remove`, `removeAll`, `prepend`, `pop`, `shift`, `clear`, `reverse`) and `ArrayAccess` usage see [Common Methods — Mutable List Methods](common-methods.md#mutable-list-methods).

---

### `IntMutableMap`

`IntMutableMap` implements `ArrayAccess`, allowing map entries to be read, added, updated, and removed using array syntax:

```php
$map = new IntMutableMap(...['a' => 1, 'b' => 2]);

$map['c'] = 3;        // add new key
$map['a'] = 10;       // update existing key
unset($map['b']);      // remove key
isset($map['a']);      // true
$map['a'];            // 10
```

Assigning a non-integer value throws `InvalidArgumentTypeException`.

`IntMutableMap` also provides a `clear()` method that removes all entries and returns `$this`.

```php
$map->clear(); // IntMutableMap([])
```

---

### `IntMutableSet`

`IntMutableSet` extends `IntMutableList` and enforces uniqueness. Duplicate values are silently dropped.

#### `add(int ...$numbers): static`

Appends one or more integers, ignoring any that are already present in the set. Returns `$this`.

```php
$set = new IntMutableSet(1, 2, 3);
$set->add(3, 4, 4); // IntMutableSet(1, 2, 3, 4)
```

#### Uniqueness after `abs()`

Calling `abs()` on a set may produce duplicates (e.g. `-2` and `2` both become `2`). `IntMutableSet` automatically deduplicates after the operation:

```php
$set = new IntMutableSet(-2, -1, 1, 2);
$set->abs(); // IntMutableSet(1, 2)
```

> **Note:** Mutations performed via `ArrayAccess` (`$set[$i] = $v`) or inherited methods like `insert()` and `prepend()` do **not** enforce uniqueness automatically. Use `add()` to safely append values to a set.

---

## Examples

```php
use Purr\Collection\IntList;
use Purr\Collection\IntMutableList;

// Immutable pipeline
$result = (new IntList(-3, -1, 0, 2, 4, 6))
    ->positiveValues()   // IntList(2, 4, 6)
    ->multiply(2)        // IntList(4, 8, 12)
    ->sortDesc();        // IntList(12, 8, 4)

echo $result->sum();    // 24
echo $result->join(','); // "12,8,4"

// Mutable workflow
$list = new IntMutableList(5, 3, 1);
$list->sortAsc()          // [1, 3, 5]  — in place
     ->add(7)             // [1, 3, 5, 7]
     ->multiply(2);       // [2, 6, 10, 14]

echo $list->avg();   // 8.0

// Statistics
$data = new IntList(4, 8, 15, 16, 23, 42);
echo $data->min();     // 4
echo $data->max();     // 42
echo $data->median();  // 15.5
echo $data->range();   // 38
```

