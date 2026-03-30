# How to Implement a Custom Collection

This guide explains how to create your own type-safe collection by extending the library's abstract base classes.

For the reasoning behind these patterns see [Architecture Decisions](adr.md).

---

## Steps

1. Extend the appropriate abstract base class: `AbstractList`, `AbstractSet`, or `AbstractMap`.
2. Implement a typed variadic constructor:
   ```php
   public function __construct(MyType ...$items)
   {
       parent::__construct($items);
   }
   ```
3. Implement `isSupportedType(mixed $value): bool` — return `true` if `$value` is of the type this collection holds.
4. Implement `getId(mixed $value): int|string` — return a scalar identity key for `$value`. Used internally by `findFirstKey`, `findKeys`, `contains`, `findFirstAfter`, and `filterUniqValues`.
5. Use only exceptions from `src/Exception`.

---

## Examples

### Immutable List

Full implementation.

```php
use Purr\Collection\AbstractList;

/** @template-extends AbstractList<MyType> */
class MyTypeList extends AbstractList
{
    public function __construct(MyType ...$items)
    {
        parent::__construct($items);
    }

    protected function isSupportedType(mixed $value): bool
    {
        return $value instanceof MyType;
    }

    protected function getId(mixed $value): int|string
    {
        return $value->getId();
    }
}
```

### Not-empty variant

Constructor must check it receives items.

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

    protected function isSupportedType(mixed $value): bool
    {
        return $value instanceof MyType;
    }

    protected function getId(mixed $value): int|string
    {
        return $value->getId();
    }
}
```

### Map

```php
use Purr\Collection\AbstractMap;

/** @template-extends AbstractMap<MyType> */
class MyTypeMap extends AbstractMap
{
    public function __construct(MyType ...$items)
    {
        parent::__construct($items);
    }

    protected function isSupportedType(mixed $value): bool
    {
        return $value instanceof MyType;
    }

    protected function getId(mixed $value): int|string
    {
        return $value->getId();
    }
}
```

---

## Reference implementations

- [`IntList`](../src/IntList.php)
- [`StringMap`](../src/StringMap.php)
