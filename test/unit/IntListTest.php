<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractList;
use Purr\Collection\IntCollectionTrait;
use Purr\Collection\IntImmutableCollectionTrait;
use Purr\Collection\IntList;

#[CoversClass(IntList::class)]
#[CoversClass(AbstractList::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(IntImmutableCollectionTrait::class)]
#[CoversClass(IntCollectionTrait::class)]
class IntListTest extends TestCase
{
    public function testConstructor_MapProvided_ReturnsTargetList(): void
    {
        $list = new IntList(...[1, 2]);

        self::assertSame([1, 2], $list->toArray());
    }

    #[DataProvider('providerFindFirst')]
    public function testFindFirst_WithOptionalPredicate_ReturnsTargetValue(
        ?int $result,
        ?callable $predicate,
        array $source
    ): void {
        $list = new IntList(...$source);

        self::assertSame($result, $list->findFirst($predicate));
    }

    public static function providerFindFirst(): array
    {
        return [
            'empty' => [null, null, []],
            'empty predicate' => [null, static fn (int $i): bool => 0 === $i % 2, []],
            '1' => [1, null, [1]],
            '1,2' => [1, null, [1, 2]],
            'first even' => [4, static fn (int $i): bool => 0 === $i % 2, [1, 4, 2]],
        ];
    }

    #[DataProvider('providerFindFirstAfter')]
    public function testFindFirstAfter_WithNeedle_ReturnsTargetValue(
        ?int $result,
        int $needle,
        array $source
    ): void {
        $list = new IntList(...$source);

        self::assertSame($result, $list->findFirstAfter($needle));
    }

    public static function providerFindFirstAfter(): array
    {
        return [
            'empty' => [null, 1, []],
            '1' => [null, 1, [1]],
            '1,2 - needle 1' => [2, 1, [1, 2]],
            '1,2 - needle 2' => [null, 2, [1, 2]],
        ];
    }

    #[DataProvider('providerFindLast')]
    public function testFindLast_Constructed_ReturnsTargetValue(?int $result, array $source): void
    {
        $list = new IntList(...$source);

        self::assertSame($result, $list->findLast());
    }

    public static function providerFindLast(): array
    {
        return [
            'empty' => [null, []],
            '1' => [1, [1]],
            '1,2' => [2, [1, 2]],
        ];
    }

    #[DataProvider('providerContains')]
    public function testContains_WithNeedle_ReturnsTargetBool(bool $result, int $needle, array $source): void
    {
        $list = new IntList(...$source);

        self::assertSame($result, $list->contains($needle));
    }

    public static function providerContains(): array
    {
        return [
            'empty' => [false, 0, []],
            'contains 1' => [true, 1, [1]],
            'contains 2' => [true, 2, [1, 2]],
            'not' => [false, 3, [1, 2]],
        ];
    }

    public function testFilter_MultipleFilters_ReturnsFilteredList(): void
    {
        $list = new IntList(1, 2, 3, 4, 5);

        $filter1 = fn (int $n): bool => 2 !== $n;
        $filter2 = fn (int $n): bool => 4 !== $n;

        self::assertSame([1, 3, 5], $list->filter($filter1, $filter2)->toArray());
    }

    public function testFilter_OneItemRemains_ReturnsOneItemList(): void
    {
        $list = new IntList(1, 2);

        $filter1 = fn (int $n): bool => 2 !== $n;

        self::assertSame([1], $list->filter($filter1)->toArray());
    }

    public function testFilter_AllRemoved_ReturnsEmptyList(): void
    {
        $list = new IntList(1);

        $filter1 = fn (int $n): bool => 1 !== $n;

        self::assertSame([], $list->filter($filter1)->toArray());
    }

    public function testFilterNot_MultipleFilters_ReturnsFilteredList(): void
    {
        $list = new IntList(1, 2, 3, 4, 5);

        $filter1 = fn (int $n): bool => 2 === $n;
        $filter2 = fn (int $n): bool => 4 === $n;

        self::assertSame([1, 3, 5], $list->filterNot($filter1, $filter2)->toArray());
    }

    public function testFilterNot_OneItemRemains_ReturnsOneItemList(): void
    {
        $list = new IntList(1, 2);

        $filter1 = fn (int $n): bool => 2 === $n;

        self::assertSame([1], $list->filterNot($filter1)->toArray());
    }

    public function testFilterNot_AllRemoved_ReturnsEmptyList(): void
    {
        $list = new IntList(1);

        $filter1 = fn (int $n): bool => 1 === $n;

        self::assertSame([], $list->filterNot($filter1)->toArray());
    }

    public function testMap_Constructed_ReturnsTargetArray(): void
    {
        $list = new IntList(1, 2);

        self::assertSame([2, 3], $list->map(fn (int $i): int => $i + 1));
    }

    public function testGroupBy_Constructed_ReturnsGroupedMap(): void
    {
        $list = new IntList(
            ...[1, 2, 3, 4]
        );

        self::assertSame([
            'odd' => [
                1,
                3,
            ],
            'even' => [
                2,
                4,
            ],
        ], $list->groupBy(fn (int $i): string => 0 === $i % 2 ? 'even' : 'odd'));
    }

    #[DataProvider('providerSorted')]
    public function testSorted_AscToDesc_ReturnsDescList(array $result, array $source): void
    {
        $list = new IntList(...$source);

        self::assertSame($result, $list->sorted(fn (int $i, int $j): int => $j <=> $i)->toArray());
    }

    public static function providerSorted(): array
    {
        return [
            'empty' => [[], []],
            'single value' => [[1], [1]],
            'two uniq values' => [[3, 2, 1], [1, 2, 3]],
        ];
    }

    #[DataProvider('providerSlice')]
    public function testSlice_WithOffsetAndLimit_ReturnsTargetList(
        array $result,
        array $source,
        int $offset,
        int $limit
    ): void {
        $list = new IntList(...$source);

        self::assertSame($result, $list->slice($offset, $limit)->toArray());
    }

    public static function providerSlice(): array
    {
        return [
            'empty' => [[], [], 1, 1],
            '1' => [[1], [1], 0, 1],
            '1, 2, request 1' => [[2], [1, 2], 1, 1],
            '1,2,3 request out of range' => [[2, 3], [1, 2, 3], 1, 3],
        ];
    }

    public function testToArray_Constructed_ReturnsGivenList(): void
    {
        $list = new IntList(1, 2, 3);

        self::assertSame([1, 2, 3], $list->toArray());
    }

    #[DataProvider('providerUnique')]
    public function testUnique_SomeValues_ReturnsUniqueList(array $result, array $source): void
    {
        $list = new IntList(...$source);

        self::assertSame($result, $list->unique()->toArray());
    }

    public static function providerUnique(): array
    {
        return [
            'empty' => [[], []],
            'single value' => [[1], [1, 1]],
            'two uniq values' => [[1, 2], [1, 2, 1, 2]],
        ];
    }

    public function testCount_TwoElements_ReturnsTwo(): void
    {
        $list = new IntList(1, 2);

        self::assertSame(2, $list->count());
    }

    public function testIsEmpty_Empty_ReturnsTrue(): void
    {
        $list = new IntList();

        self::assertTrue($list->isEmpty());
    }

    public function testIsEmpty_NotEmpty_ReturnsFalse(): void
    {
        $list = new IntList(1);

        self::assertFalse($list->isEmpty());
    }

    public function testIsNotEmpty_NotEmpty_ReturnsTrue(): void
    {
        $list = new IntList(1);

        self::assertTrue($list->isNotEmpty());
    }

    public function testIsNotEmpty_Empty_ReturnsFalse(): void
    {
        $list = new IntList();

        self::assertFalse($list->isNotEmpty());
    }

    public function testToStringSet_Constructed_ReturnsTargetStringSet(): void
    {
        $list = new IntList(1, 2, 3, 4);

        self::assertSame(['1', '2', '3', '4'], $list->toStringSet()->toArray());
    }

    public function testToStringSet_WithDuplicates_ReturnsDeduplicatedStringSet(): void
    {
        $list = new IntList(1, 2, 1, 3, 2);

        self::assertSame(['1', '2', '3'], $list->toStringSet()->toArray());
    }

    public function testChunks_Constructed_ReturnsTargetChunks(): void
    {
        $list = new IntList(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);

        $result = $list->chunks(3);

        self::assertSame(
            [[1, 2, 3], [4, 5, 6], [7, 8, 9], [10]],
            array_map(fn (IntList $list) => $list->toArray(), $result)
        );
    }

    public function testChunks_SingleElement_ReturnsIntListInstance(): void
    {
        $list = new IntList(1);

        $result = $list->chunks(1);

        self::assertInstanceOf(IntList::class, $result[0]);
    }

    public function testChunks_ZeroChunkSize_ThrowsInvalidArgumentException(): void
    {
        $list = new IntList(1);

        $this->expectException(\Purr\Collection\Exception\InvalidArgumentException::class);

        $list->chunks(0);
    }

    public function testDestruct_SomeList_ReturnsSourceValues(): void
    {
        $list = new IntList(1, 2, 3);

        self::assertSame([1, 2, 3], [...$list]);
    }

    public function testMax_Constructed_ReturnsMax(): void
    {
        $list = new IntList(1, 2, 3, 4, 2, 3);

        self::assertSame(4, $list->max());
    }

    public function testMin_Constructed_ReturnsMin(): void
    {
        $list = new IntList(2, 3, 4, 2, 1);

        self::assertSame(1, $list->min());
    }

    public function testSum_Constructed_ReturnsSum(): void
    {
        $list = new IntList(1, 2, 3);

        self::assertSame(6, $list->sum());
    }

    public function testAvg_Constructed_ReturnsAverage(): void
    {
        $list = new IntList(1, 2, 3);

        self::assertSame(2.0, $list->avg());
    }

    public function testAvg_Empty_ReturnsNull(): void
    {
        self::assertNull((new IntList())->avg());
    }

    public function testProduct_Constructed_ReturnsProduct(): void
    {
        $list = new IntList(2, 3, 4);

        self::assertSame(24, $list->product());
    }

    public function testMedian_OddCount_ReturnsMiddle(): void
    {
        $list = new IntList(3, 1, 2);

        self::assertSame(2.0, $list->median());
    }

    public function testMedian_EvenCount_ReturnsAverageOfMiddleTwo(): void
    {
        $list = new IntList(4, 1, 3, 2);

        self::assertSame(2.5, $list->median());
    }

    public function testMedian_Empty_ReturnsNull(): void
    {
        self::assertNull((new IntList())->median());
    }

    public function testRange_Constructed_ReturnsRange(): void
    {
        $list = new IntList(1, 5, 3);

        self::assertSame(4, $list->range());
    }

    public function testRange_Empty_ReturnsNull(): void
    {
        self::assertNull((new IntList())->range());
    }

    public function testJoin_WithSeparator_ReturnsJoinedString(): void
    {
        $list = new IntList(1, 2, 3);

        self::assertSame('1,2,3', $list->join(','));
    }

    public function testImplode_WithSeparator_ReturnsJoinedString(): void
    {
        $list = new IntList(1, 2, 3);

        self::assertSame('1-2-3', $list->implode('-'));
    }

    public function testFromString_ValidString_ReturnsIntList(): void
    {
        $list = IntList::fromString('1,2,3', ',');

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testFromString_EmptyString_ReturnsEmptyList(): void
    {
        self::assertSame([], IntList::fromString('', ',')->toArray());
    }

    public function testToStringList_Constructed_ReturnsStringList(): void
    {
        $list = new IntList(1, 2, 3);

        self::assertSame(['1', '2', '3'], $list->toStringList()->toArray());
    }

    public function testAbs_WithNegativeValues_ReturnsAbsoluteList(): void
    {
        $list = new IntList(-3, 0, 2, -1);

        self::assertSame([3, 0, 2, 1], $list->abs()->toArray());
    }

    public function testMultiply_ByFactor_ReturnsMultipliedList(): void
    {
        $list = new IntList(1, 2, 3);

        self::assertSame([3, 6, 9], $list->multiply(3)->toArray());
    }

    public function testNegativeValues_Constructed_ReturnsNegativeOnly(): void
    {
        $list = new IntList(-2, 0, 1, 3);

        self::assertSame([-2], $list->negativeValues()->toArray());
    }

    public function testNotZeroValues_Constructed_ReturnsNonZeroOnly(): void
    {
        $list = new IntList(0, 1, 0, 2);

        self::assertSame([1, 2], $list->notZeroValues()->toArray());
    }

    public function testPositiveValues_Constructed_ReturnsPositiveOnly(): void
    {
        $list = new IntList(-2, 0, 1, 3);

        self::assertSame([1, 3], $list->positiveValues()->toArray());
    }

    public function testSortAsc_Constructed_ReturnsSortedAscending(): void
    {
        $list = new IntList(3, 1, 2);

        self::assertSame([1, 2, 3], $list->sortAsc()->toArray());
    }

    public function testSortDesc_Constructed_ReturnsSortedDescending(): void
    {
        $list = new IntList(3, 1, 2);

        self::assertSame([3, 2, 1], $list->sortDesc()->toArray());
    }

    public function testDiff_WithAnotherList_ReturnsDifference(): void
    {
        $list = new IntList(1, 2, 3, 4);

        self::assertSame([1, 4], $list->diff(new IntList(2, 3))->toArray());
    }

    public function testIntersect_WithAnotherList_ReturnsIntersection(): void
    {
        $list = new IntList(1, 2, 3, 4);

        self::assertSame([2, 3], $list->intersect(new IntList(2, 3, 5))->toArray());
    }
}
