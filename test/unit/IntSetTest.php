<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractList;
use Purr\Collection\AbstractSet;
use Purr\Collection\IntCollectionTrait;
use Purr\Collection\IntList;
use Purr\Collection\IntSet;

#[CoversClass(IntSet::class)]
#[CoversClass(AbstractSet::class)]
#[CoversClass(AbstractList::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(IntCollectionTrait::class)]
class IntSetTest extends TestCase
{
    public function testFromString_EmptyString_ReturnsEmptySet(): void
    {
        self::assertSame([], IntSet::fromString('', ',')->toArray());
    }

    public function testFromString_IntString_ReturnsIntSet(): void
    {
        self::assertSame([1, 2], IntSet::fromString('1,2', ',')->toArray());
    }

    public function testToArray_Constructed_ReturnsUniqueValues(): void
    {
        $set = new IntSet(1, 3, 2, 3, 1);

        self::assertSame([1, 3, 2], $set->toArray());
    }

    public function testAvg_Constructed_ReturnsAverage(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertSame(2.0, $set->avg());
    }

    public function testAvg_Empty_ReturnsNull(): void
    {
        $set = new IntSet();

        self::assertNull($set->avg());
    }

    public function testMedian_OddCount_ReturnsMiddle(): void
    {
        $set = new IntSet(3, 1, 2);

        self::assertSame(2.0, $set->median());
    }

    public function testMedian_EvenCount_ReturnsAverageOfMiddleTwo(): void
    {
        $set = new IntSet(4, 1, 3, 2);

        self::assertSame(2.5, $set->median());
    }

    public function testMax_Constructed_ReturnsMax(): void
    {
        $list = new IntSet(1, 2, 3, 4, 2, 3);

        self::assertSame(4, $list->max());
    }

    public function testMin_Constructed_ReturnsMin(): void
    {
        $list = new IntSet(2, 3, 4, 2, 1);

        self::assertSame(1, $list->min());
    }

    public function testProduct_Constructed_ReturnsProduct(): void
    {
        $set = new IntSet(2, 3, 4);

        self::assertSame(24, $set->product());
    }

    public function testRange_Constructed_ReturnsRange(): void
    {
        $set = new IntSet(1, 5, 3);

        self::assertSame(4, $set->range());
    }

    public function testSum_Constructed_ReturnsSum(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertSame(6, $set->sum());
    }

    public function testJoin_WithSeparator_ReturnsJoinedString(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertSame('1,2,3', $set->join(','));
    }

    public function testImplode_WithSeparator_ReturnsJoinedString(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertSame('1-2-3', $set->implode('-'));
    }

    public function testToStringList_Constructed_ReturnsStringList(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertSame(['1', '2', '3'], $set->toStringList()->toArray());
    }

    public function testToStringSet_Constructed_ReturnsStringSet(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertSame(['1', '2', '3'], $set->toStringSet()->toArray());
    }

    public function testToStringSet_WithDuplicatesInSource_ReturnsDeduplicatedStringSet(): void
    {
        $set = new IntSet(1, 2, 1, 3, 2);

        self::assertSame(['1', '2', '3'], $set->toStringSet()->toArray());
    }

    public function testAbs_WithNegativeValues_ReturnsAbsoluteSet(): void
    {
        $set = new IntSet(-3, 0, 2, -1);

        self::assertSame([3, 0, 2, 1], $set->abs()->toArray());
    }

    public function testAbs_ReturnsIntSetInstance(): void
    {
        $set = new IntSet(1, 2);

        self::assertInstanceOf(IntSet::class, $set->abs());
    }

    public function testMultiply_ByFactor_ReturnsMultipliedSet(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertSame([3, 6, 9], $set->multiply(3)->toArray());
    }

    public function testSortAsc_Constructed_ReturnsSortedAscending(): void
    {
        $set = new IntSet(3, 1, 2);

        self::assertSame([1, 2, 3], $set->sortAsc()->toArray());
    }

    public function testSortAsc_ReturnsIntSetInstance(): void
    {
        $set = new IntSet(2, 1);

        self::assertInstanceOf(IntSet::class, $set->sortAsc());
    }

    public function testSortDesc_Constructed_ReturnsSortedDescending(): void
    {
        $set = new IntSet(3, 1, 2);

        self::assertSame([3, 2, 1], $set->sortDesc()->toArray());
    }

    public function testDiff_WithAnotherCollection_ReturnsDifference(): void
    {
        $set = new IntSet(1, 2, 3, 4);

        self::assertSame([1, 4], $set->diff(new IntList(2, 3))->toArray());
    }

    public function testIntersect_WithAnotherCollection_ReturnsIntersection(): void
    {
        $set = new IntSet(1, 2, 3, 4);

        self::assertSame([2, 3], $set->intersect(new IntList(2, 3, 5))->toArray());
    }

    public function testNotZeroValues_WithZeroElement_ReturnsNonZeroSet(): void
    {
        $list = new IntSet(2, 3, 0, 2, 1);

        self::assertSame([2, 3, 1], $list->notZeroValues()->toArray());
    }

    public function testNegativeValues_Constructed_ReturnsNegativeOnly(): void
    {
        $set = new IntSet(-2, 0, 1, 3);

        self::assertSame([-2], $set->negativeValues()->toArray());
    }

    public function testPositiveValues_Constructed_ReturnsPositiveOnly(): void
    {
        $set = new IntSet(-2, 0, 1, 3);

        self::assertSame([1, 3], $set->positiveValues()->toArray());
    }

    public function testFindFirst_NoArgs_ReturnsFirstElement(): void
    {
        $set = new IntSet(10, 20, 30);

        self::assertSame(10, $set->findFirst());
    }

    public function testFindFirst_WithPredicate_ReturnsMatchingElement(): void
    {
        $set = new IntSet(1, 4, 3);

        self::assertSame(4, $set->findFirst(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testFindFirst_WithPredicate_NoMatch_ReturnsNull(): void
    {
        $set = new IntSet(1, 3, 5);

        self::assertNull($set->findFirst(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testFindFirstAfter_Constructed_ReturnsNextElement(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertSame(2, $set->findFirstAfter(1));
    }

    public function testFindFirstAfter_LastElement_ReturnsNull(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertNull($set->findFirstAfter(3));
    }

    public function testFindFirstAfter_NotFound_ReturnsNull(): void
    {
        $set = new IntSet(1, 2);

        self::assertNull($set->findFirstAfter(99));
    }

    public function testFindFirstKey_NotInt_ReturnsFalse(): void
    {
        $map = new IntSet(1, 2);

        // @phpstan-ignore argument.type
        self::assertFalse($map->findFirstKey('1'));
    }

    /** @param array<string, int> $source */
    #[DataProvider('providerFindFirstKey')]
    public function testFindFirstKey_WithNeedle_ReturnsTargetResult(false|int $result, int $needle, array $source): void
    {
        $map = new IntSet(...$source);

        self::assertSame($result, $map->findFirstKey($needle));
    }

    /** @return array<int|string, array<int, array<int, int>|bool|int>> */
    public static function providerFindFirstKey(): array
    {
        return [
            'empty' => [false, 1, []],
            '1' => [0, 1, [1]],
            '2' => [1, 2, [1, 2]],
            'not' => [false, 3, [1, 2]],
        ];
    }

    /**
     * @param array<string>      $result
     * @param array<string, int> $source
     */
    #[DataProvider('providerFindKeys')]
    public function testFindKeys_Constructed_ReturnsArrayOfKeys(array $result, int|string $needle, array $source): void
    {
        $map = new IntSet(...$source);

        // @phpstan-ignore argument.type
        self::assertSame($result, $map->findKeys($needle));
    }

    /** @return array<int|string, array<int, array<int, int>|int|string>> */
    public static function providerFindKeys(): array
    {
        return [
            'empty' => [[], 1, []],
            '1' => [[0], 1, [1]],
            '2' => [[1], 2, [1, 2]],
            'try find not int' => [[], 'a', [1, 2]],
            'try find string int' => [[], '2', [1, 2]],
            'not' => [[], 3, [1, 2]],
        ];
    }

    public function testFindLast_NoArgs_ReturnsLastElement(): void
    {
        $set = new IntSet(10, 20, 30);

        self::assertSame(30, $set->findLast());
    }

    public function testFindLast_WithPredicate_ReturnsLastMatchingElement(): void
    {
        $set = new IntSet(1, 2, 3, 4);

        self::assertSame(4, $set->findLast(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testContains_ExistingElement_ReturnsTrue(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertTrue($set->contains(2));
    }

    public function testContains_NonExistingElement_ReturnsFalse(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertFalse($set->contains(99));
    }

    public function testHas_ExistingElement_ReturnsTrue(): void
    {
        $set = new IntSet(1, 2);

        self::assertTrue($set->has(1));
    }

    public function testAny_MatchingPredicate_ReturnsTrue(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertTrue($set->any(static fn (int $i): bool => $i > 2));
    }

    public function testAny_NoMatch_ReturnsFalse(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertFalse($set->any(static fn (int $i): bool => $i > 10));
    }

    public function testAll_AllMatch_ReturnsTrue(): void
    {
        $set = new IntSet(2, 4, 6);

        self::assertTrue($set->all(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testAll_NotAllMatch_ReturnsFalse(): void
    {
        $set = new IntSet(2, 3, 6);

        self::assertFalse($set->all(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testNone_NoneMatch_ReturnsTrue(): void
    {
        $set = new IntSet(1, 3, 5);

        self::assertTrue($set->none(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testNone_SomeMatch_ReturnsFalse(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertFalse($set->none(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testFlattenGroupBy_Constructed_ReturnsGroupedArray(): void
    {
        $set = new IntSet(1, 2, 3);

        $result = $set->flattenGroupBy(static fn (int $i): string => 0 === $i % 2 ? 'even' : 'odd');

        self::assertSame(['odd' => 3, 'even' => 2], $result);
    }

    public function testFilter_EvenCheck_ReturnsEvenSet(): void
    {
        $set = new IntSet(1, 2, 3, 4);

        self::assertSame([2, 4], $set->filter(fn (int $a) => 0 === $a % 2)->toArray());
    }

    public function testFilter_EvenCheck_ReturnsIntSetInstance(): void
    {
        $set = new IntSet(1, 2, 3, 4);

        self::assertInstanceOf(IntSet::class, $set->filter(fn (int $a) => 0 === $a % 2));
    }

    public function testFilterNot_EvenCheck_ReturnsOddSet(): void
    {
        $set = new IntSet(1, 2, 3, 4, 5);

        self::assertSame([1, 3, 5], $set->filterNot(static fn (int $n): bool => 0 === $n % 2)->toArray());
    }

    public function testFilterNot_EvenCheck_ReturnsIntSetInstance(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertInstanceOf(IntSet::class, $set->filterNot(static fn (int $n): bool => 0 === $n % 2));
    }

    public function testCount_TwoElements_ReturnsTwo(): void
    {
        $set = new IntSet(1, 2);

        self::assertSame(2, $set->count());
    }

    public function testIsEmpty_EmptySet_ReturnsTrue(): void
    {
        $set = new IntSet();

        self::assertTrue($set->isEmpty());
    }

    public function testIsNotEmpty_Constructed_ReturnsTrue(): void
    {
        $set = new IntSet(1);

        self::assertTrue($set->isNotEmpty());
    }

    public function testUnique_Constructed_ReturnsUniqueValues(): void
    {
        $set = new IntSet(1, 3, 2, 3, 1);

        self::assertSame([1, 3, 2], $set->unique()->toArray());
    }

    public function testUnique_Constructed_ReturnsIntSetInstance(): void
    {
        $set = new IntSet(1, 3, 2, 3, 1);

        self::assertInstanceOf(IntSet::class, $set->unique());
    }

    public function testMap_Constructed_ReturnsArray(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertSame([2, 4, 6], $set->map(static fn (int $i): int => $i * 2));
    }

    public function testReduce_Constructed_ReturnsSumViaReduce(): void
    {
        $set = new IntSet(1, 2, 3, 4);

        $result = $set->reduce(static fn (?int $carry, int $item): int => $carry + $item, 0);

        self::assertSame(10, $result);
    }

    public function testSorted_DescComparator_ReturnsSortedDescSet(): void
    {
        $set = new IntSet(1, 3, 2);

        self::assertSame([3, 2, 1], $set->sorted(static fn (int $i, int $j): int => $j <=> $i)->toArray());
    }

    public function testSorted_ReturnsIntSetInstance(): void
    {
        $set = new IntSet(2, 1);

        self::assertInstanceOf(IntSet::class, $set->sorted(static fn (int $i, int $j): int => $i <=> $j));
    }

    public function testSlice_PositiveOffsetAndLimit_ReturnsSlicedSet(): void
    {
        $list = new IntSet(1, 2, 3, 4, 5);

        $result = $list->slice(1, 2);

        self::assertSame([2, 3], $result->toArray());
    }

    public function testSlice_PositiveOffsetAndLimit_ReturnsIntSetInstance(): void
    {
        $list = new IntSet(1, 2, 3, 4, 5);

        $result = $list->slice(1, 2);

        self::assertInstanceOf(IntSet::class, $result);
    }

    public function testSlice_OffsetZeroLimitTwo_ReturnsFirstTwoElements(): void
    {
        $list = new IntSet(10, 20, 30, 40);

        $result = $list->slice(0, 2);

        self::assertSame([10, 20], $result->toArray());
    }

    public function testSlice_NegativeOffset_ReturnsElementsFromEnd(): void
    {
        $list = new IntSet(1, 2, 3, 4, 5);

        $result = $list->slice(-2, 2);

        self::assertSame([4, 5], $result->toArray());
    }

    public function testSlice_OffsetExceedsSize_ReturnsEmptySet(): void
    {
        $list = new IntSet(1, 2, 3);

        $result = $list->slice(5, 2);

        self::assertSame([], $result->toArray());
    }

    public function testSlice_LimitExceedsRemaining_ReturnsAvailableElements(): void
    {
        $list = new IntSet(1, 2, 3, 4);

        $result = $list->slice(2, 5);

        self::assertSame([3, 4], $result->toArray());
    }

    public function testSlice_ZeroLimit_ReturnsEmptySet(): void
    {
        $list = new IntSet(1, 2, 3, 4);

        $result = $list->slice(1, 0);

        self::assertSame([], $result->toArray());
    }

    public function testSlice_EmptySet_ReturnsEmptySet(): void
    {
        $list = new IntSet();

        $result = $list->slice(0, 2);

        self::assertSame([], $result->toArray());
    }

    public function testChunks_Constructed_ReturnsIntSetChunks(): void
    {
        $set = new IntSet(1, 2, 3, 4, 5);

        $result = $set->chunks(2);

        self::assertCount(3, $result);
        self::assertSame([1, 2], $result[0]->toArray());
        self::assertSame([3, 4], $result[1]->toArray());
        self::assertSame([5], $result[2]->toArray());
    }

    public function testChunks_EachChunkIsIntSetInstance(): void
    {
        $set = new IntSet(1, 2);

        $result = $set->chunks(1);

        self::assertInstanceOf(IntSet::class, $result[0]);
    }

    public function testIterate_Constructed_ReturnsAllElements(): void
    {
        $set = new IntSet(10, 20, 30);

        self::assertSame([10, 20, 30], [...$set]);
    }

    public function testIndexOf_ExistingElement_ReturnsIndex(): void
    {
        $set = new IntSet(10, 20, 30);

        self::assertSame(1, $set->indexOf(20));
    }

    public function testIndexOf_NonExistingElement_ReturnsNull(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertNull($set->indexOf(99));
    }

    public function testLastIndexOf_UniqueElements_ReturnsSameAsIndexOf(): void
    {
        $set = new IntSet(1, 2, 3);

        self::assertSame(0, $set->lastIndexOf(1));
    }

    public function testGroupBy_Constructed_ReturnsGroupedMap(): void
    {
        $set = new IntSet(1, 2, 3, 4);

        $result = $set->groupBy(static fn (int $i): string => 0 === $i % 2 ? 'even' : 'odd');

        self::assertSame(['odd' => [1, 3], 'even' => [2, 4]], $result);
    }
}
