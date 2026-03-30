# PurrPHP/collection

Type-safe collections for PHP 8. Type safety based on native PHP features, not static analyzers. Inspired by [Kotlin collections](https://kotlinlang.org/docs/collections-overview.html).

## Documentation

- [Common Methods](docs/common-methods.md) — methods available on every collection
- [Int Collections](docs/int-collections.md) — integer-typed classes and their specific API
- [String Collections](docs/string-collections.md) — string-typed classes and their specific API
- [Custom Collections](docs/custom-collections.md) — step-by-step guide for implementing your own typed collection
- [Architecture Decisions](docs/adr.md) — key design decisions and their rationale

### Problem scope

PHP has no native typed collections — only plain arrays. This makes it easy to accidentally mix types or corrupt key structure:

```php
$uuids = [];
$uuids[] = Uuid::fromInteger('1');
$uuids[] = '00000000-0000-0000-0000-000000000001'; // wrong type, no error

$uuids[] = Uuid::fromInteger('2');
$uuids['validUuid'] = Uuid::fromInteger('3');       // mixed int/string keys
```

This library solves these problems by providing type-safe, semantically clear collections that reduce boilerplate and prevent mistakes at runtime.

## Code structure

Inspired by [Kotlin collections](https://kotlinlang.org/docs/collections-overview.html), the library provides three collection types:

- **List** — ordered sequence with integer keys, duplicates allowed
- **Set** — ordered sequence of unique values
- **Map** — associative array (key → value)

Collections are immutable and return new object after each manipulation.

Hierarchy:

```
CollectionInterface
└── AbstractCollection
    ├── AbstractList               — ordered list (sequential integer keys)
    │   ├── AbstractSet            — deduplicates values on construction
    └── AbstractMap                — associative map (string/int keys)
```

## Available Classes

| Class                | Description |
|----------------------|-------------|
| `IntList`            | Ordered list of integers |
| `IntSet`             | Ordered list of unique integers |
| `IntNotEmptyList`    | Same as `IntList`, throws on empty construction |
| `IntNotEmptySet`     | Same as `IntSet`, throws on empty construction |
| `IntMap`             | Associative (key → int) map |
| `StringList`         | Ordered list of strings |
| `StringSet`          | Ordered list of unique strings |
| `StringNotEmptySet`  | Same as `StringSet`, throws on empty construction |
| `StringMap`          | Associative (key → string) map |

## Installation

```bash
composer require purrphp/pure-collection
```

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

### Common Operations

All collections implement `CollectionInterface`. See the full reference:

- [Common Methods](docs/common-methods.md) — searching, filtering, transforming, grouping, sizing, iteration
- [Int Collections](docs/int-collections.md) — aggregation, sorting, set operations variants
- [String Collections](docs/string-collections.md) — alphabetical sorting, set operations variants

## Extending

To create a custom typed collection, extend `AbstractList`, `AbstractSet`, or `AbstractMap`. See [Architecture Decisions](docs/adr.md) for decisions motivation and [detailed guide](docs/custom-collections.md) for custom implementation.

```php
use Purr\Collection\AbstractSet;

/** @template-extends AbstractSet<\DateTimeImmutable> */
class DateTimeSet extends AbstractSet
{
    public function __construct(\DateTimeImmutable ...$dates)
    {
        parent::__construct($dates);
    }
     protected function isSupportedType(mixed $value): bool
    {
        return $value instanceof \DateTimeImmutable;
    }

    protected function getId(mixed $value): int|string
    {
        return $value->getTimeStamp();
    }
}
```

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
