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

## 3. Set deduplication via `filterUniqValues`

A `Set` must store only unique values. A simple `array_unique()` is not sufficient because collections often hold objects, where uniqueness must be determined by a domain-specific criterion (e.g., `id`, `uuid`...).

For this reason, the abstract method `filterUniqValues()` was introduced in `AbstractCollection`. Each concrete set class implements its own deduplication logic:

```php
protected function filterUniqValues(array $items): array
{
    $seen = [];
    $result = [];

    foreach ($items as $item) {
       $result[$item->getId()] = $item;
    }

    return $result;
}
```

The method is also available on all collections via `unique()`.

---

## 4. Mutable collections implement `ArrayAccess`

Mutable collections implement `\ArrayAccess` to allow familiar array-style mutation syntax:

```php
$map['key'] = 'value';
$list[] = 42;
```

`ArrayAccess` method signatures are fixed by the interface (`offsetSet(mixed $offset, mixed $value): void`), so type enforcement cannot be added at the signature level. Instead, mutable collections define an abstract method:

```php
abstract protected function ensureType(mixed $value): void;
```

This method is called inside `offsetSet()` and other mutating methods before any value is stored, throwing an exception on type mismatch.

---

## 5. Mutable lists guard offset types and bounds

Because a list only has integer indexes, `AbstractMutableList` adds two private guards:

- `ensureIntOffset` — rejects any non-integer key, since `ArrayAccess` accepts `mixed` offsets.
- `ensureIndexInBounds` — rejects writes to indexes that do not yet exist (prevents sparse arrays). Appending with `$list[] = $value` (null offset) is still allowed.

---

## 6. No `AbstractMutableSet`

There is no `AbstractMutableSet` base class. Sets are harder to generalise in the mutable case because:

- Every mutation (add, remove, replace) must re-check uniqueness.
- Uniqueness criteria are domain-specific and vary per set type.
- A domain-specific mutable set typically exposes many more methods related to its domain (e.g. integer arithmetic operations, string transformations) than set-specific methods. A shared base class would impose set constraints on top of domain logic that is hard to generalise.
- The resulting base class would be more restrictive than helpful.

Implement your own mutable set directly, using `IntMutableSet` as a reference. It combines integer-specific operations with the necessary uniqueness guards.

---

## 7. All exceptions come from `src/Exception`

All exceptions thrown by this library extend the types defined in `src/Exception`. Do not throw standard PHP exceptions directly. This allows consumers to catch library-specific exceptions without depending on the PHP standard library exception hierarchy.

---

## How to implement a custom collection

See [Custom Collections](custom-collections.md) for a step-by-step guide with code examples for all collection types.