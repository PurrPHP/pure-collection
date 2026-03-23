# purrphp/collection

Type-safe collections for PHP 8. Type safety based on native PHP features, not static analyzers.. Inspired by [Kotlin collections](https://kotlinlang.org/docs/collections-overview.html).

## Installation

```bash
composer require purrphp/collection
```

## Overview

Provides immutable, type-safe collection primitives built around a common interface hierarchy:

```
CollectionInterface
└── AbstractCollection
    ├── AbstractList
    │   └── AbstractSet  (unique values)
    └── AbstractMap      (associative array)
```

## Available Classes

| Class | Description                                 |
|-------|---------------------------------------------|
| `IntList` | List of integers                            |
| `IntSet` | Unique list of integers with set operations |
| `IntNotEmptyList` | Non-empty list of integers                  |
| `IntNotEmptySet` | Non-empty unique list of integers           |
| `IntMap` | Associative map of integers                 |
| `StringList` | List of strings                             |
| `StringSet` | Unique list of strings with set operations  |
| `StringNotEmptySet` | Non-empty unique list of strings            |

## Usage

### Lists

```php
use Purr\Collection\IntList;

$numbers = new IntList(1, 2, 3, 4, 5);

$even    = $numbers->filter(fn(int $n): bool => $n % 2 === 0);
$doubled = $numbers->map(fn(int $n): int => $n * 2); // returns array
$sum     = $numbers->reduce(fn(int $carry, int $n): int => $carry + $n, 0);
$sorted  = $numbers->sorted(fn(int $a, int $b): int => $b <=> $a);

$numbers->count();      // 5
$numbers->isEmpty();    // false
$numbers->isNotEmpty(); // true
$numbers->max();        // 5
$numbers->min();        // 1
```

### Sets (unique values)

```php
use Purr\Collection\IntSet;
use Purr\Collection\StringSet;

$set = new IntSet(1, 2, 3, 2, 1); // duplicates are removed
$set->toArray();                   // [1, 2, 3]

$ids = IntSet::fromString('1,2,3', separator: ',');
$ids->join(',');                   // "1,2,3"

$tags = new StringSet('php', 'oop', 'php');
$tags->toArray();                  // ['php', 'oop']
$tags->sortedAlphabetically()->toArray(); // ['oop', 'php']

$a = new StringSet('a', 'b', 'c');
$b = new StringSet('b', 'c', 'd');
$a->diff($b)->toArray();           // ['a']
```

### Maps

```php
use Purr\Collection\IntMap;

$map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

$map->filter(fn(int $v): bool => $v > 1)->toArray(); // ['b' => 2, 'c' => 3]
$map->groupBy(fn(int $v): string => $v % 2 === 0 ? 'even' : 'odd');
```

### Mutable collections
// todo describe

### Common Operations

All collections implement `CollectionInterface`:

```php
$collection->findFirst();                   // first item (or null)
$collection->findFirst(fn($x) => ...);      // first matching item
$collection->findLast();                    // last item (or null)
$collection->findFirstAfter($needle);       // item after $needle
$collection->contains($value);              // bool

$collection->any(fn($x) => ...);            // bool — any match
$collection->all(fn($x) => ...);            // bool — all match
$collection->none(fn($x) => ...);           // bool — none match

$collection->filter(fn($x) => ...);         // new filtered collection
$collection->filterNot(fn($x) => ...);      // new inverted-filter collection

$collection->map(fn($x) => ...);            // array
$collection->reduce(fn($carry, $x) => ..., $initial); // scalar
$collection->sorted(fn($a, $b) => ...);     // new sorted collection
$collection->slice($offset, $limit);        // new sliced collection
$collection->unique();                      // new deduplicated collection
$collection->chunks($size);                 // array of collections
$collection->groupBy(fn($x) => ...);        // grouped array
$collection->flattenGroupBy(fn($x) => ...); // keyed array
$collection->toArray();                     // plain PHP array
$collection->count();                       // int

$collection->isEmpty();                     // bool
$collection->isNotEmpty();                  // bool
```

## Extending

To create a custom typed collection, extend `AbstractList`, `AbstractSet`, or `AbstractMap`:

```php
use Purr\Collection\AbstractSet;

/** @template-extends AbstractSet<\DateTimeImmutable> */
class DateSet extends AbstractSet
{
    public function __construct(\DateTimeImmutable ...$dates)
    {
        parent::__construct($dates);
    }
    
    /**
     * @inheritDoc
     */
    protected function filterUniqValues(array $items) {
        $u = [];
        
        foreach ($items as $item){
            $u[$item->getTimeStamp()] = $item;
        }
        
        return new self(...$u);
    }
}
```

> **Note:** By default, `array_unique` is used for deduplication. For objects, override `filterUniqValues()` with custom comparison logic.

## Development

```bash
composer install

composer test        # Run tests
composer analyse     # Static analysis
composer cs-check    # Code style check
composer cs-fix      # Fix code style
composer check       # All checks
```

### With Docker / Make

```bash
make test       # Run tests
make analyse    # Static analysis
make cs-check   # Code style check
make check      # All checks
make shell      # Open shell in dev container
```

## License

MIT — see [LICENSE](LICENSE).
