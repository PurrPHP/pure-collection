<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractMap;
use Purr\Collection\IntCollectionTrait;
use Purr\Collection\IntList;
use Purr\Collection\IntMap;

#[CoversClass(IntMap::class)]
#[CoversClass(AbstractMap::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(IntCollectionTrait::class)]
final class IntMapTest extends TestCase
{
    public function testConstructor_ListProvided_ReturnsTargetMap(): void
    {
        $map = new IntMap(1, 2);

        self::assertSame([1, 2], $map->toArray());
    }

    public function testFromString_ValidString_ReturnsIntMapInstance(): void
    {
        $map = IntMap::fromString('1,2,3', ',');

        self::assertInstanceOf(IntMap::class, $map);
        self::assertSame([1, 2, 3], $map->toArray());
    }

    public function testAvg_Constructed_ReturnsAverage(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(2.0, $map->avg());
    }

    public function testAvg_Empty_ReturnsNull(): void
    {
        $map = new IntMap();

        self::assertNull($map->avg());
    }

    public function testMedian_OddCount_ReturnsMiddle(): void
    {
        $map = new IntMap(...['a' => 3, 'b' => 1, 'c' => 2]);

        self::assertSame(2.0, $map->median());
    }

    public function testMedian_EvenCount_ReturnsAverageOfMiddleTwo(): void
    {
        $map = new IntMap(...['a' => 4, 'b' => 1, 'c' => 3, 'd' => 2]);

        self::assertSame(2.5, $map->median());
    }

    public function testProduct_Constructed_ReturnsProduct(): void
    {
        $map = new IntMap(...['a' => 2, 'b' => 3, 'c' => 4]);

        self::assertSame(24, $map->product());
    }

    public function testRange_Constructed_ReturnsRange(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 5, 'c' => 3]);

        self::assertSame(4, $map->range());
    }

    public function testSum_Constructed_ReturnsSum(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(6, $map->sum());
    }

    public function testJoin_WithSeparator_ReturnsJoinedString(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame('1,2,3', $map->join(','));
    }

    public function testImplode_WithSeparator_ReturnsJoinedString(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame('1-2-3', $map->implode('-'));
    }

    public function testToStringList_Constructed_ReturnsStringList(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(['1', '2', '3'], $map->toStringList()->toArray());
    }

    public function testToStringSet_Constructed_ReturnsStringSet(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(['1', '2', '3'], $map->toStringSet()->toArray());
    }

    public function testToStringSet_WithDuplicateValues_ReturnsDeduplicatedStringSet(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 1, 'd' => 3, 'e' => 2]);

        self::assertSame(['1', '2', '3'], $map->toStringSet()->toArray());
    }

    public function testAbs_WithNegativeValues_ReturnsAbsoluteMap(): void
    {
        $map = new IntMap(...['a' => -1, 'b' => 0, 'c' => 2]);

        self::assertSame(['a' => 1, 'b' => 0, 'c' => 2], $map->abs()->toArray());
    }

    public function testAbs_ReturnsIntMapInstance(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2]);

        self::assertInstanceOf(IntMap::class, $map->abs());
    }

    public function testMultiply_ByFactor_ReturnsMultipliedMap(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(['a' => 3, 'b' => 6, 'c' => 9], $map->multiply(3)->toArray());
    }

    public function testSortAsc_Constructed_ReturnsSortedAscendingValues(): void
    {
        $map = new IntMap(...['a' => 3, 'b' => 1, 'c' => 2]);

        self::assertSame([1, 2, 3], $map->sortAsc()->toArray());
    }

    public function testSortDesc_Constructed_ReturnsSortedDescendingValues(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 3, 'c' => 2]);

        self::assertSame([3, 2, 1], $map->sortDesc()->toArray());
    }

    public function testAdd_AddMap_ReturnsMapWithNewItemsInTheEnd(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 3, 'c' => 2]);

        self::assertSame(['a' => 1, 'b' => 3, 'c' => 2, 'd' => 4, 'e' => 5], $map->add(...['d' => 4, 'e' => 5])->toArray());
    }

    public function testAdd_AddList_ReturnsMapWithNewItemsInTheBeginning(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 3, 'c' => 2]);

        self::assertSame([
            0 => 4,
            1 => 5,
            'a' => 1,
            'b' => 3,
            'c' => 2,
        ], $map->add(4, 5)->toArray());
    }

    public function testAdd_SomeKeyExists_AddOnlyNotExistedItems(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 3, 'c' => 2]);

        self::assertSame(['a' => 1, 'b' => 3, 'c' => 2, 'd' => 4], $map->add(...['d' => 4, 'b' => 1])->toArray());
    }

    public function testPrepend_PrependList_ReturnsMapWithNewItems(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 3, 'c' => 2]);

        self::assertSame([
            0 => 4,
            1 => 5,
            'a' => 1,
            'b' => 3,
            'c' => 2,
        ], $map->prepend(4, 5)->toArray());
    }

    public function testPrepend_SomeKeyExists_PrependOnlyNotExistedItems(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 3, 'c' => 2]);

        self::assertSame(['d' => 4, 'a' => 1, 'b' => 3, 'c' => 2], $map->prepend(...['d' => 4, 'b' => 1])->toArray());
    }

    /**
     * remove ignores keys.
     */
    public function testRemove_SomeElementNotExist_ReturnsMapWithoutProvidedItems(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 3, 'c' => 2]);

        self::assertSame(['b' => 3, 'c' => 2], $map->remove(...['c' => 5, 'b' => 1, 'f' => 6])->toArray());
    }

    public function testDiff_WithAnotherCollection_ReturnsDifference(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(['a' => 1], $map->diff(new IntList(2, 3))->toArray());
    }

    public function testIntersect_WithAnotherCollection_ReturnsIntersection(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(['b' => 2, 'c' => 3], $map->intersect(new IntList(2, 3, 5))->toArray());
    }

    public function testNegativeValues_Constructed_ReturnsNegativeOnly(): void
    {
        $map = new IntMap(...['a' => -1, 'b' => 0, 'c' => 2]);

        self::assertSame(['a' => -1], $map->negativeValues()->toArray());
    }

    public function testPositiveValues_Constructed_ReturnsPositiveOnly(): void
    {
        $map = new IntMap(...['a' => -1, 'b' => 0, 'c' => 2]);

        self::assertSame(['c' => 2], $map->positiveValues()->toArray());
    }

    /** @param array<string, int> $source */
    #[DataProvider('providerFindFirst')]
    public function testFindFirst_WithOptionalPredicate_ReturnsTargetValue(
        ?int $result,
        ?callable $predicate,
        array $source
    ): void {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->findFirst($predicate));
    }

    /** @return array<array-key, array<int, mixed>> */
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

    /** @param array<string, int> $source */
    #[DataProvider('providerFindFirstAfter')]
    public function testFindFirstAfter_WithNeedle_ReturnsTargetValue(
        ?int $result,
        int $needle,
        array $source
    ): void {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->findFirstAfter($needle));
    }

    /** @return array<array-key, array<int, mixed>> */
    public static function providerFindFirstAfter(): array
    {
        return [
            'empty' => [null, 1, []],
            '1' => [null, 1, ['a' => 1]],
            '1,2 - needle 1' => [2, 1, ['a' => 1, 'b' => 2]],
            '1,2 - needle 2' => [null, 2, ['a' => 1, 'b' => 2]],
        ];
    }

    public function testFindFirstKey_NotInt_ReturnsFalse(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2]);

        // @phpstan-ignore argument.type
        self::assertFalse($map->findFirstKey('1'));
    }

    /** @param array<string, int> $source */
    #[DataProvider('providerFindFirstKey')]
    public function testFindFirstKey_WithNeedle_ReturnsTargetResult(false|string $result, int $needle, array $source): void
    {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->findFirstKey($needle));
    }

    /** @return array<int|string, array<int, array<string, int>|bool|int|string>> */
    public static function providerFindFirstKey(): array
    {
        return [
            'empty' => [false, 1, []],
            '1' => ['a', 1, ['a' => 1]],
            '2' => ['b', 2, ['a' => 1, 'b' => 2]],
            '2 same values, returns first key' => ['b', 2, ['a' => 1, 'b' => 2, 'c' => 2]],
            'not' => [false, 3, ['a' => 1, 'b' => 2]],
        ];
    }

    /**
     * @param array<string>      $result
     * @param array<string, int> $source
     */
    #[DataProvider('providerFindKeys')]
    public function testFindKeys_Constructed_ReturnsArrayOfKeys(array $result, int|string $needle, array $source): void
    {
        $map = new IntMap(...$source);

        // @phpstan-ignore argument.type
        self::assertSame($result, $map->findKeys($needle));
    }

    /** @return array<int|string, array<int, array<int|string, int|string>|int|string>> */
    public static function providerFindKeys(): array
    {
        return [
            'empty' => [[], 1, []],
            '1' => [['a'], 1, ['a' => 1]],
            '2' => [['b'], 2, ['a' => 1, 'b' => 2]],
            'try find not int' => [[], 'uuid', ['a' => 1, 'b' => 2]],
            'try find string int' => [[], '2', ['a' => 1, 'b' => 2]],
            '2 same values, returns first key' => [['b', 'c'], 2, ['a' => 1, 'b' => 2, 'c' => 2]],
            'not' => [[], 3, ['a' => 1, 'b' => 2]],
        ];
    }

    /** @param array<string, int> $source */
    #[DataProvider('providerFindLast')]
    public function testFindLast_WithOptionalPredicate_ReturnsTargetValue(?int $result, ?callable $predicate, array $source): void
    {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->findLast($predicate));
    }

    /** @return array<array-key, array<int, mixed>> */
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

    /** @param array<string, int> $source */
    #[DataProvider('providerContains')]
    public function testContains_WithNeedle_ReturnsTargetBool(bool $result, int $needle, array $source): void
    {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->contains($needle));
    }

    /** @return array<array-key, array<int, mixed>> */
    public static function providerContains(): array
    {
        return [
            'empty' => [false, 0, []],
            'contains 1' => [true, 1, ['a' => 1]],
            'contains 2' => [true, 2, ['a' => 1, 'b' => 2]],
            'not' => [false, 3, ['a' => 1, 'b' => 2]],
        ];
    }

    public function testHas_ExistingElement_ReturnsTrue(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2]);

        self::assertTrue($map->has(1));
    }

    public function testAny_MatchingPredicate_ReturnsTrue(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertTrue($map->any(static fn (int $i): bool => $i > 2));
    }

    public function testAny_NoMatch_ReturnsFalse(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertFalse($map->any(static fn (int $i): bool => $i > 10));
    }

    public function testAll_AllMatch_ReturnsTrue(): void
    {
        $map = new IntMap(...['a' => 2, 'b' => 4, 'c' => 6]);

        self::assertTrue($map->all(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testAll_NotAllMatch_ReturnsFalse(): void
    {
        $map = new IntMap(...['a' => 2, 'b' => 3, 'c' => 6]);

        self::assertFalse($map->all(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testNone_NoneMatch_ReturnsTrue(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 3, 'c' => 5]);

        self::assertTrue($map->none(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testNone_SomeMatch_ReturnsFalse(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertFalse($map->none(static fn (int $i): bool => 0 === $i % 2));
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

    public function testCount_Constructed_ReturnsCount(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(3, $map->count());
    }

    public function testIsEmpty_EmptyMap_ReturnsTrue(): void
    {
        $map = new IntMap();

        self::assertTrue($map->isEmpty());
    }

    public function testIsEmpty_Constructed_ReturnsFalse(): void
    {
        $map = new IntMap(...['a' => 1]);

        self::assertFalse($map->isEmpty());
    }

    public function testIsNotEmpty_Constructed_ReturnsTrue(): void
    {
        $map = new IntMap(...['a' => 1]);

        self::assertTrue($map->isNotEmpty());
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

    public function testFlattenGroupBy_Constructed_ReturnsGroupedArray(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        $result = $map->flattenGroupBy(static fn (int $i): string => 0 === $i % 2 ? 'even' : 'odd');

        self::assertSame(['odd' => 3, 'even' => 2], $result);
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

    public function testReduce_Constructed_ReturnsSumViaReduce(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]);

        $result = $map->reduce(static fn (?int $carry, int $item): int => $carry + $item, 0);

        self::assertSame(10, $result);
    }

    /**
     * @param array<string, int> $result
     * @param array<string, int> $source
     */
    #[DataProvider('providerUnique')]
    public function testUnique_SomeValues_ReturnsUniqueMap(array $result, array $source): void
    {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->unique()->toArray());
    }

    /** @return array<array-key, array<int, mixed>> */
    public static function providerUnique(): array
    {
        return [
            'empty' => [[], []],
            'single value' => [['a' => 1], ['a' => 1, 'b' => 1]],
            'two uniq values' => [['a' => 1, 'b' => 2], ['a' => 1, 'b' => 2, 'c' => 1, 'd' => 2]],
        ];
    }

    /**
     * @param array<string, int> $result
     * @param array<string, int> $source
     */
    #[DataProvider('providerSorted')]
    public function testSorted_AscToDesc_ReturnsDescMap(array $result, array $source): void
    {
        $map = new IntMap(...$source);

        self::assertSame($result, $map->sorted(fn (int $i, int $j): int => $j <=> $i)->toArray());
    }

    /** @return array<array-key, array<int, mixed>> */
    public static function providerSorted(): array
    {
        return [
            'empty' => [[], []],
            'single value' => [['a' => 1], ['a' => 1]],
            'two uniq values' => [['c' => 3, 'b' => 2, 'a' => 1], ['a' => 1, 'b' => 2, 'c' => 3]],
        ];
    }

    /**
     * @param array<string, int> $result
     * @param array<string, int> $source
     */
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

    /** @return array<array-key, array<int, mixed>> */
    public static function providerSlice(): array
    {
        return [
            'empty' => [[], [], 1, 1],
            '1' => [['a' => 1], ['a' => 1], 0, 1],
            '1, 2, request 1' => [['b' => 2], ['a' => 1, 'b' => 2], 1, 1],
            '1,2,3 request out of range' => [['b' => 2, 'c' => 3], ['a' => 1, 'b' => 2, 'c' => 3], 1, 3],
        ];
    }

    public function testChunks_Constructed_ReturnsIntMapChunks(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        $result = $map->chunks(2);

        self::assertCount(2, $result);
        self::assertSame(['a' => 1, 'b' => 2], $result[0]->toArray());
        self::assertSame(['c' => 3], $result[1]->toArray());
    }

    public function testChunks_EachChunkIsIntMapInstance(): void
    {
        $map = new IntMap(...['a' => 1, 'b' => 2]);

        $result = $map->chunks(1);

        self::assertInstanceOf(IntMap::class, $result[0]);
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

    public function testIterate_Constructed_ReturnsAllElements(): void
    {
        $map = new IntMap(...['a' => 10, 'b' => 20]);

        self::assertSame(['a' => 10, 'b' => 20], [...$map]);
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
