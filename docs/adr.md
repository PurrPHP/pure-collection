# Architecture Decision Records

This document describes the key architectural decisions made in this library and explains the reasoning behind them.

---

## 1. Type enforcement via constructor argument types

PHP does not support typed class properties for collection elements natively. The chosen approach is to enforce types through constructor argument types using variadic parameters:

```php
public function __construct(MyType ...$items)
```

This leverages PHP's built-in type checking and provides clear, IDE-friendly signatures without additional validation boilerplate.

---

## 2. List always reindexes to sequential integer keys

`AbstractList` always wraps its internal array with `array_values()` to guarantee sequential integer keys starting from zero. Maps and other collection types do not do this — they preserve the keys exactly as provided (`[1, 2]`, `['a' => 2, 'b' => 3]`).

**Why:** A list is semantically an ordered sequence. String keys have no meaning in a list context, and non-sequential numeric keys would break index-based access assumptions.

---

## 3. Only immutable collections

Object mutations can produce unexpected, hard-to-debug side effects. So this library doesn't support this feature. Any method related to collection changes returns a new object.

---

## 4. Identity-based equality via `isSupportedType` and `getId`

A simple `===` comparison is not sufficient for collections holding objects, where equality must be determined by a domain-specific criterion (e.g., `id`, `uuid`).

For this reason, two abstract methods were introduced in `AbstractCollection`:

- **`isSupportedType(mixed $value): bool`** — guards against unsupported types; `findFirstKey`, `findKeys`, and `contains` return early when this returns `false`.
- **`getId(mixed $value): int|string`** — returns a scalar identity key for the value.

`AbstractCollection` uses these two methods internally to provide concrete implementations of `findFirstKey`, `findKeys`, `contains`, `findFirstAfter`, and `filterUniqValues`. Concrete classes only need to implement the two abstract methods:

```php
protected function isSupportedType(mixed $value): bool
{
    return $value instanceof MyType;
}

protected function getId(mixed $value): int|string
{
    return $value->getId();
}
```

Deduplication (`filterUniqValues`) and set uniqueness (`unique()`) are also driven by `getId`.

---

## 5. All exceptions come from `src/Exception`

All exceptions thrown by this library extend the types defined in `src/Exception`. Do not throw standard PHP exceptions directly. This allows consumers to catch library-specific exceptions without depending on the PHP standard library exception hierarchy.

---

## How to implement a custom collection

See [Custom Collections](custom-collections.md) for a step-by-step guide with code examples for all collection types.
