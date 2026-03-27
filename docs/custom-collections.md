# How to Implement a Custom Collection

This guide explains how to create your own type-safe collection by extending the library's abstract base classes.

For the reasoning behind these patterns see [Architecture Decisions](adr.md).

---

## Steps

1. Extend the appropriate abstract base class: `AbstractList`, `AbstractSet`, `AbstractMap`, `AbstractMutableList`, or `AbstractMutableMap`.
2. Implement a typed variadic constructor:
   ```php
   public function __construct(MyType ...$items)
   {
       parent::__construct($items);
   }
   ```
3. Implement `filterUniqValues()` — required in every concrete class:
   - For scalars delegate to `array_unique($items)`.
   - For objects use a `foreach` loop and deduplicate by a domain-specific key (e.g. `id`, `uuid`). Cause `array_unique` compare string values.
4. For mutable variants (`AbstractMutableList`, `AbstractMutableMap`) also implement `ensureType()` to validate values on every write.
5. Use only exceptions from `src/Exception`.

---

## Examples

### Immutable List

```php
use Purr\Collection\AbstractList;

/** @template-extends AbstractList<MyType> */
class MyTypeList extends AbstractList
{
    public function __construct(MyType ...$items)
    {
        parent::__construct($items);
    }

    protected function filterUniqValues(array $items): array
    {
        $u = [];

        foreach ($items as $item) {
            $u[$item->getId()] = $item;
        }

        return $u;
    }
}
```

### Not-empty variant

```php
use Purr\Collection\Exception\InvalidArgumentException;

class MyTypeNotEmptyList extends MyTypeList
{
    /** @throws InvalidArgumentException */
    public function __construct(MyType ...$items)
    {
        if (!$items) {
            throw new InvalidArgumentException('Items are empty');
        }
        parent::__construct(...$items);
    }
}
```

### Immutable Set (with object deduplication)

```php
use Purr\Collection\AbstractSet;

/** @template-extends AbstractSet<MyType> */
class MyTypeSet extends AbstractSet
{
    public function __construct(MyType ...$items)
    {
        parent::__construct($items);
    }

    protected function filterUniqValues(array $items): array
    {
        $u = [];

        foreach ($items as $item) {
            $u[$item->getId()] = $item;
        }

        return $u;
    }
}
```

### Immutable Map

```php
use Purr\Collection\AbstractMap;

/** @template-extends AbstractMap<MyType> */
class MyTypeMap extends AbstractMap
{
    public function __construct(MyType ...$items)
    {
        parent::__construct($items);
    }

    protected function filterUniqValues(array $items): array
    {
        $u = [];

        foreach ($items as $item) {
            $u[$item->getId()] = $item;
        }

        return $u;
    }
}
```

### Mutable List

```php
use Purr\Collection\AbstractMutableList;
use Purr\Collection\Exception\InvalidArgumentTypeException;

/** @template-extends AbstractMutableList<MyType> */
class MyTypeMutableList extends AbstractMutableList
{
    public function __construct(MyType ...$items)
    {
        parent::__construct($items);
    }

    protected function ensureType(mixed $value): void
    {
        if (!$value instanceof MyType) {
            throw new InvalidArgumentTypeException(
                type: get_debug_type($value),
                expects: MyType::class,
            );
        }
    }

    protected function filterUniqValues(array $items): array
    {
        $u = [];

        foreach ($items as $item) {
            $u[$item->getId()] = $item;
        }

        return $u;
    }
}
```

---

## Reference implementations

- [`IntList`](../src/IntList.php)
- [`StringMap`](../src/StringMap.php)
- [`IntMutableSet`](../src/IntMutableSet.php)
