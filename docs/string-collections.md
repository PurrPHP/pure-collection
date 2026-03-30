# String Collections

This document covers all string-typed collection classes and the methods specific to them.

For methods shared by all collections see [Common Methods](common-methods.md).

## Available Classes

| Class | Type | Description |
|-------|------|-------------|
| `StringList` | Immutable list | Ordered list of strings |
| `StringSet` | Immutable set | Ordered list of **unique** strings |
| `StringNotEmptySet` | Immutable set | Same as `StringSet`, throws on empty construction |
| `StringMap` | Immutable map | Associative (key → string) map |
All classes implement both `CollectionInterface` and `StringCollectionInterface`.

### Not-empty Variant

`StringNotEmptySet` throws `InvalidArgumentException` when constructed without arguments:

```php
new StringNotEmptySet();               // throws InvalidArgumentException
new StringNotEmptySet('a', 'b', 'c'); // ok
```

---

## Construction

```php
use Purr\Collection\StringList;
use Purr\Collection\StringSet;
use Purr\Collection\StringNotEmptySet;
use Purr\Collection\StringMap;

$list     = new StringList('foo', 'bar', 'baz');
$set      = new StringSet('php', 'oop', 'php'); // duplicates removed → ['php', 'oop']
$nonempty = new StringNotEmptySet('x', 'y');
$map      = new StringMap(...['lang' => 'php', 'type' => 'oop']);
```

---

## Static Factory

### `fromInts(int ...$numbers): static`

Creates a string collection from integers, converting each number to its string representation.

```php
StringList::fromInts(1, 2, 3);      // StringList('1', '2', '3')
StringSet::fromInts(1, 2, 2, 3);    // StringSet('1', '2', '3')
```

---

## Sorting

### `sortedAlphabetically(bool $desc = false): static`

Returns a new collection sorted in alphabetical (lexicographic) order. Pass `true` to sort in descending order. The original collection is not modified.

```php
$tags = new StringList('banana', 'apple', 'cherry');
$tags->sortedAlphabetically();             // StringList('apple', 'banana', 'cherry')
$tags->sortedAlphabetically(desc: true);   // StringList('cherry', 'banana', 'apple')

$set = new StringSet('php', 'go', 'rust');
$set->sortedAlphabetically(); // StringSet('go', 'php', 'rust')
```

---

## Set Operations

### `diff(StringCollectionInterface $list2): static`

Returns a collection of elements present in `$this` but **not** in `$list2`.

```php
$a = new StringList('a', 'b', 'c');
$b = new StringList('b', 'c', 'd');

$a->diff($b); // StringList('a')
```

### `intersect(StringCollectionInterface $list2): static`

Returns a collection of elements present in **both** `$this` and `$list2`.

```php
$a->intersect($b); // StringList('b', 'c')
```

---

## Joining

### `join(string $separator = ''): string`

Concatenates all elements into a single string, separated by `$separator`.

```php
(new StringList('a', 'b', 'c'))->join(', '); // "a, b, c"
(new StringList('foo', 'bar'))->join();        // "foobar"
```

### `implode(string $separator = ''): string`

Alias for `join()`.

---

## StringMap

`StringMap` is an immutable associative collection mapping string or integer keys to string values. It implements all common methods and `StringCollectionInterface`, but does not have index-based access (`indexOf`, `lastIndexOf`) since it is map-based.

```php
$map = new StringMap(...['lang' => 'php', 'env' => 'prod', 'region' => 'eu']);

$map->contains('php');       // true
$map->toArray();             // ['lang' => 'php', 'env' => 'prod', 'region' => 'eu']
$map->join(', ');            // "php, prod, eu"
$map->sortedAlphabetically()->toArray(); // ['lang' => 'php', 'region' => 'eu', 'env' => 'prod']
```

`diff()` and `intersect()` compare by **value**, not by key, and return a new `StringMap` preserving the original keys.

```php
$a = new StringMap(...['x' => 'php', 'y' => 'go']);
$b = new StringMap(...['z' => 'go']);

$a->diff($b)->toArray();      // ['x' => 'php']
$a->intersect($b)->toArray(); // ['y' => 'go']
```

---

## Examples

```php
use Purr\Collection\StringList;
use Purr\Collection\StringSet;

// Basic usage
$words = new StringList('hello', 'world', 'hello');

$words->count();   // 3
$words->unique();  // StringList('hello', 'world')
$words->contains('world'); // true

// Alphabetical sorting
$fruits = new StringSet('mango', 'apple', 'banana');
$fruits->sortedAlphabetically()->toArray(); // ['apple', 'banana', 'mango']

// Set operations
$backend  = new StringSet('php', 'go', 'rust');
$compiled = new StringSet('go', 'rust', 'c++');

$backend->diff($compiled)->toArray();       // ['php']
$backend->intersect($compiled)->toArray();  // ['go', 'rust']

// Convert from integers
$ids = StringList::fromInts(10, 20, 30);
$ids->join('-'); // "10-20-30"

// Filter and join
$csv = new StringList('alice', 'bob', '', 'carol');
$csv->filter(fn(string $s): bool => $s !== '')
    ->sortedAlphabetically()
    ->join(', ');
// "alice, bob, carol"
```
