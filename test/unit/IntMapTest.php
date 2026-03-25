<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractMap;
use Purr\Collection\IntCollectionTrait;
use Purr\Collection\IntImmutableCollectionTrait;
use Purr\Collection\IntMap;

#[CoversClass(IntMap::class)]
#[CoversClass(AbstractMap::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(IntImmutableCollectionTrait::class)]
#[CoversClass(IntCollectionTrait::class)]
final class IntMapTest extends TestCase
{
    public function testConstructor_ListProvided_ReturnsTargetMap(): void
    {
        $map = new IntMap(1, 2);

        self::assertSame([1, 2], $map->toArray());
    }

    #[DataProvider('providerFindFirst')]
    public function testFindFirst_WithOptionalPredicate_ReturnsTargetValue(
        ?int $result,
        ?callable $predicate,
        array $source
    ): void {
        $map = new IntMap(...$source);

        $a = new IntMap(1);

        self::assertSame($result, $map->findFirst($predicate));
    }

    public static function providerFindFirst(): array
    {
        return [
            'empty' => [null, null, []],
            'empty predicate' => [null, static fn (int $i): bool => 0 === $i % 2, []],
            '1' => [1, null, ['a' => 1]],
            '1,2' => [1, null, ['a' => 1, 'b' => 2]],
            'first even' => [4, static fn (int $i): bool => 0 === $i % 2, ['a' => 1, 'b' => 4, 'c' => 2]],
        ];
    }

    #[DataProvider('providerFindFirstAfter')]
    public function testFindFirstAfter_WithNeedle_ReturnsTargetValue(
        ?int $result,
        int $needle,
        array $source
    ): void {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->findFirstAfter($needle));
    }

    public static function providerFindFirstAfter(): array
    {
        return [
            'empty' => [null, 1, []],
            '1' => [null, 1, ['a' => 1]],
            '1,2 - needle 1' => [2, 1, ['a' => 1, 'b' => 2]],
            '1,2 - needle 2' => [null, 2, ['a' => 1, 'b' => 2]],
        ];
    }

    #[DataProvider('providerFindLast')]
    public function testFindLast_WithOptionalPredicate_ReturnsTargetValue(?int $result, ?callable $predicate, array $source): void
    {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->findLast($predicate));
    }

    public static function providerFindLast(): array
    {
        return [
            'empty' => [null, null, []],
            'empty predicate' => [null, static fn (int $i): bool => 0 === $i % 2, []],
            '1' => [1, null, ['a' => 1]],
            '1,2' => [2, null, ['a' => 1, 'b' => 2]],
            'last even' => [2, static fn (int $i): bool => 0 === $i % 2, ['a' => 1, 'b' => 4, 'c' => 2]],
        ];
    }

    #[DataProvider('providerContains')]
    public function testContains_WithNeedle_ReturnsTargetBool(bool $result, int $needle, array $source): void
    {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->contains($needle));
    }

    public static function providerContains(): array
    {
        return [
            'empty' => [false, 0, []],
            'contains 1' => [true, 1, ['a' => 1]],
            'contains 2' => [true, 2, ['a' => 1, 'b' => 2]],
            'not' => [false, 3, ['a' => 1, 'b' => 2]],
        ];
    }

    public function testFilter_MultipleFilters_ReturnsFilteredMap(): void
    {
        $map = new IntMap(
            ...['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]
        );

        $filter1 = fn (int $n): bool => 2 !== $n;
        $filter2 = fn (int $n): bool => 4 !== $n;

        self::assertSame([
            'a' => 1,
            'c' => 3,
            'e' => 5,
        ], $map->filter($filter1, $filter2)->toArray());
    }

    public function testFilter_OneItemRemains_ReturnsOneItemMap(): void
    {
        $map = new IntMap(
            ...['a' => 1, 'b' => 2]
        );

        $filter1 = fn (int $n): bool => 2 !== $n;

        self::assertSame(['a' => 1], $map->filter($filter1)->toArray());
    }

    public function testFilter_AllRemoved_ReturnsEmptyMap(): void
    {
        $map = new IntMap(
            ...['a' => 1]
        );

        $filter1 = fn (int $n): bool => 1 !== $n;

        self::assertSame([], $map->filter($filter1)->toArray());
    }

    public function testFilterNot_MultipleFilters_ReturnsFilteredMap(): void
    {
        $map = new IntMap(
            ...['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]
        );

        $filter1 = fn (int $n): bool => 2 === $n;
        $filter2 = fn (int $n): bool => 4 === $n;

        self::assertSame([
            'a' => 1,
            'c' => 3,
            'e' => 5,
        ], $map->filterNot($filter1, $filter2)->toArray());
    }

    public function testFilterNot_OneItemRemains_ReturnsOneItemMap(): void
    {
        $map = new IntMap(
            ...['a' => 1, 'b' => 2]
        );

        $filter1 = fn (int $n): bool => 2 === $n;

        self::assertSame(['a' => 1], $map->filterNot($filter1)->toArray());
    }

    public function testFilterNot_AllRemoved_ReturnsEmptyMap(): void
    {
        $map = new IntMap(
            ...['a' => 1]
        );

        $filter1 = fn (int $n): bool => 1 === $n;

        self::assertSame([], $map->filterNot($filter1)->toArray());
    }

    public function testMap_Constructed_ReturnsTargetArray(): void
    {
        $map = new IntMap(
            ...['a' => 1, 'b' => 2]
        );

        self::assertSame([
            'a' => 2,
            'b' => 3,
        ], $map->map(fn (int $i): int => $i + 1));

        self::assertSame(['a' => 1, 'b' => 2], $map->toArray(), 'no mutation of source');
    }

    public function testGroupBy_Constructed_ReturnsGroupedMap(): void
    {
        $map = new IntMap(
            ...['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]
        );

        self::assertSame([
            'odd' => [
                'a' => 1,
                'c' => 3,
            ],
            'even' => [
                'b' => 2,
                'd' => 4,
            ],
        ], $map->groupBy(fn (int $i): string => 0 === $i % 2 ? 'even' : 'odd'));
    }

    #[DataProvider('providerSorted')]
    public function testSorted_AscToDesc_ReturnsDescMap(array $result, array $source): void
    {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->sorted(fn (int $i, int $j): int => $j <=> $i)->toArray());
    }

    public static function providerSorted(): array
    {
        return [
            'empty' => [[], []],
            'single value' => [['a' => 1], ['a' => 1]],
            'two uniq values' => [['c' => 3, 'b' => 2, 'a' => 1], ['a' => 1, 'b' => 2, 'c' => 3]],
        ];
    }

    #[DataProvider('providerSlice')]
    public function testSlice_WithOffsetAndLimit_ReturnsTargetMap(
        array $result,
        array $source,
        int $offset,
        int $limit
    ): void {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->slice($offset, $limit)->toArray());
    }

    public static function providerSlice(): array
    {
        return [
            'empty' => [[], [], 1, 1],
            '1' => [['a' => 1], ['a' => 1], 0, 1],
            '1, 2, request 1' => [['b' => 2], ['a' => 1, 'b' => 2], 1, 1],
            '1,2,3 request out of range' => [['b' => 2, 'c' => 3], ['a' => 1, 'b' => 2, 'c' => 3], 1, 3],
        ];
    }

    public function testToArray_Constructed_ReturnsGivenMap(): void
    {
        $map = new IntMap(
            ...['a' => 1, 'b' => 2]
        );

        self::assertSame([
            'a' => 1,
            'b' => 2,
        ], $map->toArray());
    }

    #[DataProvider('providerUnique')]
    public function testUnique_SomeValues_ReturnsUniqueMap(array $result, array $source): void
    {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->unique()->toArray());
    }

    public static function providerUnique(): array
    {
        return [
            'empty' => [[], []],
            'single value' => [['a' => 1], ['a' => 1, 'b' => 1]],
            'two uniq values' => [['a' => 1, 'b' => 2], ['a' => 1, 'b' => 2, 'c' => 1, 'd' => 2]],
        ];
    }

    public function testMax_Constructed_ReturnsMax(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 2, 'f' => 3]);

        self::assertSame(4, $map->max());
    }

    public function testMin_Constructed_ReturnsMin(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 2, 'f' => 3]);

        self::assertSame(1, $map->min());
    }

    public function testNotZeroValues_WithZeroElement_ReturnsNonZeroMap(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 0, 'c' => 3]);

        self::assertSame(['a' => 1, 'c' => 3], $map->notZeroValues()->toArray());
    }
}
