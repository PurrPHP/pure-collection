<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractList;
use Purr\Collection\AbstractSet;
use Purr\Collection\Exception\InvalidArgumentException;
use Purr\Collection\IntCollectionTrait;
use Purr\Collection\IntImmutableCollectionTrait;
use Purr\Collection\IntList;
use Purr\Collection\IntNotEmptyList;
use Purr\Collection\IntNotEmptySet;
use Purr\Collection\IntSet;

#[CoversClass(IntNotEmptyList::class)]
#[CoversClass(IntList::class)]
#[CoversClass(IntSet::class)]
#[CoversClass(AbstractSet::class)]
#[CoversClass(AbstractList::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(IntImmutableCollectionTrait::class)]
#[CoversClass(IntCollectionTrait::class)]
final class IntNotEmptyListTest extends TestCase
{
    // region Constructor

    public function testConstructor_EmptyList_ThrowsInvalidArgumentException(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Numbers are empty'));

        new IntNotEmptyList();
    }

    public function testConstructor_WithElements_ReturnsList(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testConstructor_WithDuplicates_SetDeduplicatesValues(): void
    {
        $set = new IntNotEmptySet(1, 2, 2, 3, 1);

        self::assertSame([1, 2, 3], $set->toArray());
    }

    // endregion

    // region IntCollectionTrait: fromString

    public function testFromString_ValidString_ReturnsNotEmptyListInstance(): void
    {
        $list = IntNotEmptyList::fromString('1,2,3', ',');

        self::assertInstanceOf(IntNotEmptyList::class, $list);
        self::assertSame([1, 2, 3], $list->toArray());
    }

    // endregion

    // region IntCollectionTrait: aggregate methods

    public function testAvg_Constructed_ReturnsAverage(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertSame(2.0, $list->avg());
    }

    public function testMedian_OddCount_ReturnsMiddle(): void
    {
        $list = new IntNotEmptyList(3, 1, 2);

        self::assertSame(2.0, $list->median());
    }

    public function testMedian_EvenCount_ReturnsAverageOfMiddleTwo(): void
    {
        $list = new IntNotEmptyList(4, 1, 3, 2);

        self::assertSame(2.5, $list->median());
    }

    public function testMax_Constructed_ReturnsMax(): void
    {
        $list = new IntNotEmptySet(1, 2, 3, 4, 2, 3);

        self::assertSame(4, $list->max());
    }

    public function testMin_List_ReturnsMin(): void
    {
        $list = new IntNotEmptyList(3, 1, 4, 1, 5);

        self::assertSame(1, $list->min());
    }

    public function testProduct_Constructed_ReturnsProduct(): void
    {
        $list = new IntNotEmptyList(2, 3, 4);

        self::assertSame(24, $list->product());
    }

    public function testRange_Constructed_ReturnsRange(): void
    {
        $list = new IntNotEmptyList(1, 5, 3);

        self::assertSame(4, $list->range());
    }

    public function testSum_Constructed_ReturnsSum(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertSame(6, $list->sum());
    }

    // endregion

    // region IntCollectionTrait: string/conversion methods

    public function testJoin_WithSeparator_ReturnsJoinedString(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertSame('1,2,3', $list->join(','));
    }

    public function testImplode_WithSeparator_ReturnsJoinedString(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertSame('1-2-3', $list->implode('-'));
    }

    public function testToStringList_Constructed_ReturnsStringList(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertSame(['1', '2', '3'], $list->toStringList()->toArray());
    }

    public function testToStringSet_Constructed_ReturnsStringSet(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertSame(['1', '2', '3'], $list->toStringSet()->toArray());
    }

    // endregion

    // region IntImmutableCollectionTrait: element-preserving transformations

    public function testAbs_WithNegativeValues_ReturnsAbsoluteList(): void
    {
        $list = new IntNotEmptyList(-3, 0, 2, -1);

        self::assertSame([3, 0, 2, 1], $list->abs()->toArray());
    }

    public function testAbs_ReturnsNotEmptyListInstance(): void
    {
        $list = new IntNotEmptyList(1, 2);

        self::assertInstanceOf(IntNotEmptyList::class, $list->abs());
    }

    public function testMultiply_ByFactor_ReturnsMultipliedList(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertSame([3, 6, 9], $list->multiply(3)->toArray());
    }

    public function testSortAsc_Constructed_ReturnsSortedAscending(): void
    {
        $list = new IntNotEmptyList(3, 1, 2);

        self::assertSame([1, 2, 3], $list->sortAsc()->toArray());
    }

    public function testSortDesc_Constructed_ReturnsSortedDescending(): void
    {
        $list = new IntNotEmptyList(3, 1, 2);

        self::assertSame([3, 2, 1], $list->sortDesc()->toArray());
    }

    public function testSortAsc_ReturnsNotEmptyListInstance(): void
    {
        $list = new IntNotEmptyList(2, 1);

        self::assertInstanceOf(IntNotEmptyList::class, $list->sortAsc());
    }

    public function testDiff_WithAnotherList_ReturnsDifference(): void
    {
        $list = new IntNotEmptyList(1, 2, 3, 4);

        self::assertSame([1, 4], $list->diff(new IntList(2, 3))->toArray());
    }

    public function testDiff_AllElementsRemoved_ThrowsInvalidArgumentException(): void
    {
        $list = new IntNotEmptyList(1, 2);

        $this->expectException(InvalidArgumentException::class);

        $list->diff(new IntList(1, 2));
    }

    public function testIntersect_WithAnotherList_ReturnsIntersection(): void
    {
        $list = new IntNotEmptyList(1, 2, 3, 4);

        self::assertSame([2, 3], $list->intersect(new IntList(2, 3, 5))->toArray());
    }

    public function testIntersect_EmptyResult_ThrowsInvalidArgumentException(): void
    {
        $list = new IntNotEmptyList(1, 2);

        $this->expectException(InvalidArgumentException::class);

        $list->intersect(new IntList(3, 4));
    }

    // endregion

    // region IntImmutableCollectionTrait: filter-based transformations

    public function testNegativeValues_Constructed_ReturnsNegativeOnly(): void
    {
        $list = new IntNotEmptyList(-2, 0, 1, 3);

        self::assertSame([-2], $list->negativeValues()->toArray());
    }

    public function testNegativeValues_NoNegatives_ThrowsInvalidArgumentException(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        $this->expectException(InvalidArgumentException::class);

        $list->negativeValues();
    }

    public function testNotZeroValues_Constructed_ReturnsNonZeroOnly(): void
    {
        $list = new IntNotEmptyList(0, 1, 0, 2);

        self::assertSame([1, 2], $list->notZeroValues()->toArray());
    }

    public function testNotZeroValues_AllZeros_ThrowsInvalidArgumentException(): void
    {
        $list = new IntNotEmptyList(0, 0);

        $this->expectException(InvalidArgumentException::class);

        $list->notZeroValues();
    }

    public function testPositiveValues_Constructed_ReturnsPositiveOnly(): void
    {
        $list = new IntNotEmptyList(-2, 0, 1, 3);

        self::assertSame([1, 3], $list->positiveValues()->toArray());
    }

    public function testPositiveValues_NoPositives_ThrowsInvalidArgumentException(): void
    {
        $list = new IntNotEmptyList(-1, -2);

        $this->expectException(InvalidArgumentException::class);

        $list->positiveValues();
    }

    // endregion

    // region AbstractCollection: find methods

    public function testFindFirst_NoArgs_ReturnsFirstElement(): void
    {
        $list = new IntNotEmptyList(10, 20, 30);

        self::assertSame(10, $list->findFirst());
    }

    public function testFindFirst_WithPredicate_ReturnsMatchingElement(): void
    {
        $list = new IntNotEmptyList(1, 4, 2);

        self::assertSame(4, $list->findFirst(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testFindFirst_WithPredicate_NoMatch_ReturnsNull(): void
    {
        $list = new IntNotEmptyList(1, 3, 5);

        self::assertNull($list->findFirst(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testFindFirstAfter_Constructed_ReturnsNextElement(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertSame(2, $list->findFirstAfter(1));
    }

    public function testFindFirstAfter_LastElement_ReturnsNull(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertNull($list->findFirstAfter(3));
    }

    public function testFindFirstAfter_NotFound_ReturnsNull(): void
    {
        $list = new IntNotEmptyList(1, 2);

        self::assertNull($list->findFirstAfter(99));
    }

    public function testFindLast_NoArgs_ReturnsLastElement(): void
    {
        $list = new IntNotEmptyList(10, 20, 30);

        self::assertSame(30, $list->findLast());
    }

    public function testFindLast_WithPredicate_ReturnsLastMatchingElement(): void
    {
        $list = new IntNotEmptyList(1, 2, 3, 4);

        self::assertSame(4, $list->findLast(static fn (int $i): bool => 0 === $i % 2));
    }

    // endregion

    // region AbstractCollection: contains / has / any / all / none

    public function testContains_ExistingElement_ReturnsTrue(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertTrue($list->contains(2));
    }

    public function testContains_NonExistingElement_ReturnsFalse(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertFalse($list->contains(99));
    }

    public function testHas_ExistingElement_ReturnsTrue(): void
    {
        $list = new IntNotEmptyList(1, 2);

        self::assertTrue($list->has(1));
    }

    public function testAny_MatchingPredicate_ReturnsTrue(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertTrue($list->any(static fn (int $i): bool => $i > 2));
    }

    public function testAny_NoMatch_ReturnsFalse(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertFalse($list->any(static fn (int $i): bool => $i > 10));
    }

    public function testAll_AllMatch_ReturnsTrue(): void
    {
        $list = new IntNotEmptyList(2, 4, 6);

        self::assertTrue($list->all(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testAll_NotAllMatch_ReturnsFalse(): void
    {
        $list = new IntNotEmptyList(2, 3, 6);

        self::assertFalse($list->all(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testNone_NoneMatch_ReturnsTrue(): void
    {
        $list = new IntNotEmptyList(1, 3, 5);

        self::assertTrue($list->none(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testNone_SomeMatch_ReturnsFalse(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertFalse($list->none(static fn (int $i): bool => 0 === $i % 2));
    }

    // endregion

    // region AbstractCollection: groupBy / flattenGroupBy

    public function testFlattenGroupBy_Constructed_ReturnsGroupedArray(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        $result = $list->flattenGroupBy(static fn (int $i): string => 0 === $i % 2 ? 'even' : 'odd');

        self::assertSame(['odd' => 3, 'even' => 2], $result);
    }

    // endregion

    // region AbstractCollection: filter / filterNot

    public function testFilter_MatchingElements_ReturnsFilteredList(): void
    {
        $list = new IntNotEmptyList(1, 2, 3, 4, 5);

        self::assertSame([2, 4], $list->filter(static fn (int $n): bool => 0 === $n % 2)->toArray());
    }

    public function testFilter_AllElementsRemoved_ThrowsInvalidArgumentException(): void
    {
        $list = new IntNotEmptyList(1, 3, 5);

        $this->expectException(InvalidArgumentException::class);

        $list->filter(static fn (int $n): bool => 0 === $n % 2);
    }

    public function testFilter_Constructed_ReturnsNotEmptyListInstance(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertInstanceOf(IntNotEmptyList::class, $list->filter(static fn (int $n): bool => $n > 1));
    }

    public function testFilterNot_MatchingElements_ReturnsFilteredList(): void
    {
        $list = new IntNotEmptyList(1, 2, 3, 4, 5);

        self::assertSame([1, 3, 5], $list->filterNot(static fn (int $n): bool => 0 === $n % 2)->toArray());
    }

    public function testFilterNot_AllElementsRemoved_ThrowsInvalidArgumentException(): void
    {
        $list = new IntNotEmptyList(2, 4);

        $this->expectException(InvalidArgumentException::class);

        $list->filterNot(static fn (int $n): bool => 0 === $n % 2);
    }

    // endregion

    // region AbstractCollection: count / isEmpty / isNotEmpty

    public function testCount_Constructed_ReturnsCount(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertSame(3, $list->count());
    }

    public function testIsEmpty_Constructed_ReturnsFalse(): void
    {
        $list = new IntNotEmptyList(1);

        self::assertFalse($list->isEmpty());
    }

    public function testIsNotEmpty_Constructed_ReturnsTrue(): void
    {
        $list = new IntNotEmptyList(1);

        self::assertTrue($list->isNotEmpty());
    }

    // endregion

    // region AbstractCollection: unique

    public function testUnique_Constructed_ReturnsUniqueSet(): void
    {
        $list = new IntNotEmptySet(1, 2, 3, 4, 2, 3);

        self::assertSame([1, 2, 3, 4], $list->unique()->toArray());
    }

    public function testUnique_List_ReturnsUniqueList(): void
    {
        $list = new IntNotEmptyList(1, 2, 1, 3, 2);

        self::assertSame([1, 2, 3], $list->unique()->toArray());
    }

    public function testUnique_Constructed_ReturnsNotEmptyListInstance(): void
    {
        $list = new IntNotEmptyList(1, 1, 2);

        self::assertInstanceOf(IntNotEmptyList::class, $list->unique());
    }

    // endregion

    // region AbstractCollection: map / reduce

    public function testMap_Constructed_ReturnsArray(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertSame([2, 4, 6], $list->map(static fn (int $i): int => $i * 2));
    }

    public function testReduce_Constructed_ReturnsSumViaReduce(): void
    {
        $list = new IntNotEmptyList(1, 2, 3, 4);

        $result = $list->reduce(static fn (int $carry, int $item): int => $carry + $item, 0);

        self::assertSame(10, $result);
    }

    // endregion

    // region AbstractCollection: slice

    public function testSlice_ValidRange_ReturnsSlicedList(): void
    {
        $list = new IntNotEmptyList(1, 2, 3, 4, 5);

        self::assertSame([2, 3], $list->slice(1, 2)->toArray());
    }

    public function testSlice_EmptyResult_ThrowsInvalidArgumentException(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        $this->expectException(InvalidArgumentException::class);

        $list->slice(10, 5);
    }

    public function testSlice_Constructed_ReturnsNotEmptyListInstance(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertInstanceOf(IntNotEmptyList::class, $list->slice(0, 2));
    }

    // endregion

    // region AbstractCollection: sorted

    public function testSorted_DescComparator_ReturnsSortedDescList(): void
    {
        $list = new IntNotEmptyList(1, 3, 2);

        self::assertSame([3, 2, 1], $list->sorted(static fn (int $i, int $j): int => $j <=> $i)->toArray());
    }

    public function testSorted_ReturnsNotEmptyListInstance(): void
    {
        $list = new IntNotEmptyList(2, 1);

        self::assertInstanceOf(IntNotEmptyList::class, $list->sorted(static fn (int $i, int $j): int => $i <=> $j));
    }

    // endregion

    // region AbstractCollection: chunks

    public function testChunks_Constructed_ReturnsNotEmptyListChunks(): void
    {
        $list = new IntNotEmptyList(1, 2, 3, 4, 5);

        $result = $list->chunks(2);

        self::assertCount(3, $result);
        self::assertSame([1, 2], $result[0]->toArray());
        self::assertSame([3, 4], $result[1]->toArray());
        self::assertSame([5], $result[2]->toArray());
    }

    public function testChunks_Constructed_EachChunkIsNotEmptyListInstance(): void
    {
        $list = new IntNotEmptyList(1, 2);

        $result = $list->chunks(1);

        self::assertInstanceOf(IntNotEmptyList::class, $result[0]);
    }

    public function testChunks_ZeroSize_ThrowsInvalidArgumentException(): void
    {
        $list = new IntNotEmptyList(1);

        $this->expectException(InvalidArgumentException::class);

        $list->chunks(0);
    }

    // endregion

    // region AbstractCollection: iterator

    public function testIterate_Constructed_ReturnsAllElements(): void
    {
        $list = new IntNotEmptyList(10, 20, 30);

        self::assertSame([10, 20, 30], [...$list]);
    }

    // endregion

    // region AbstractList: indexOf / lastIndexOf / groupBy

    public function testIndexOf_ExistingElement_ReturnsIndex(): void
    {
        $list = new IntNotEmptyList(10, 20, 30);

        self::assertSame(1, $list->indexOf(20));
    }

    public function testIndexOf_NonExistingElement_ReturnsNull(): void
    {
        $list = new IntNotEmptyList(1, 2, 3);

        self::assertNull($list->indexOf(99));
    }

    public function testLastIndexOf_DuplicateValues_ReturnsLastIndex(): void
    {
        $list = new IntNotEmptyList(1, 2, 1, 3, 1);

        self::assertSame(4, $list->lastIndexOf(1));
    }

    public function testGroupBy_Constructed_ReturnsGroupedMap(): void
    {
        $list = new IntNotEmptyList(1, 2, 3, 4);

        $result = $list->groupBy(static fn (int $i): string => 0 === $i % 2 ? 'even' : 'odd');

        self::assertSame(['odd' => [1, 3], 'even' => [2, 4]], $result);
    }

    // endregion
}
