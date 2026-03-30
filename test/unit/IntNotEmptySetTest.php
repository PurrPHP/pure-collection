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
use Purr\Collection\IntList;
use Purr\Collection\IntNotEmptySet;
use Purr\Collection\IntSet;

#[CoversClass(IntNotEmptySet::class)]
#[CoversClass(IntSet::class)]
#[CoversClass(AbstractSet::class)]
#[CoversClass(AbstractList::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(IntCollectionTrait::class)]
final class IntNotEmptySetTest extends TestCase
{
    public function testConstructor_EmptySet_ThrowsInvalidArgumentException(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Numbers are empty'));

        new IntNotEmptySet();
    }

    public function testConstructor_WithElements_ReturnsSet(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertSame([1, 2, 3], $set->toArray());
    }

    public function testConstructor_WithDuplicates_SetDeduplicatesValues(): void
    {
        $set = new IntNotEmptySet(1, 2, 2, 3, 1);

        self::assertSame([1, 2, 3], $set->toArray());
    }

    public function testFromString_ValidString_ReturnsNotEmptySetInstance(): void
    {
        $set = IntNotEmptySet::fromString('1,2,3', ',');

        self::assertInstanceOf(IntNotEmptySet::class, $set);
        self::assertSame([1, 2, 3], $set->toArray());
    }

    public function testAvg_Constructed_ReturnsAverage(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertSame(2.0, $set->avg());
    }

    public function testMedian_OddCount_ReturnsMiddle(): void
    {
        $set = new IntNotEmptySet(3, 1, 2);

        self::assertSame(2.0, $set->median());
    }

    public function testMedian_EvenCount_ReturnsAverageOfMiddleTwo(): void
    {
        $set = new IntNotEmptySet(4, 1, 3, 2);

        self::assertSame(2.5, $set->median());
    }

    public function testMax_Constructed_ReturnsMax(): void
    {
        $set = new IntNotEmptySet(1, 2, 3, 4);

        self::assertSame(4, $set->max());
    }

    public function testMin_Constructed_ReturnsMin(): void
    {
        $set = new IntNotEmptySet(2, 3, 4, 1);

        self::assertSame(1, $set->min());
    }

    public function testProduct_Constructed_ReturnsProduct(): void
    {
        $set = new IntNotEmptySet(2, 3, 4);

        self::assertSame(24, $set->product());
    }

    public function testRange_Constructed_ReturnsRange(): void
    {
        $set = new IntNotEmptySet(1, 5, 3);

        self::assertSame(4, $set->range());
    }

    public function testSum_Constructed_ReturnsSum(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertSame(6, $set->sum());
    }

    public function testJoin_WithSeparator_ReturnsJoinedString(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertSame('1,2,3', $set->join(','));
    }

    public function testImplode_WithSeparator_ReturnsJoinedString(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertSame('1-2-3', $set->implode('-'));
    }

    public function testToStringList_Constructed_ReturnsStringList(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertSame(['1', '2', '3'], $set->toStringList()->toArray());
    }

    public function testToStringSet_Constructed_ReturnsStringSet(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertSame(['1', '2', '3'], $set->toStringSet()->toArray());
    }

    public function testToStringSet_WithDuplicatesInSource_ReturnsDeduplicatedStringSet(): void
    {
        $set = new IntNotEmptySet(1, 2, 1, 3, 2);

        self::assertSame(['1', '2', '3'], $set->toStringSet()->toArray());
    }

    public function testAbs_WithNegativeValues_ReturnsAbsoluteSet(): void
    {
        $set = new IntNotEmptySet(-3, 0, 2, -1);

        self::assertSame([3, 0, 2, 1], $set->abs()->toArray());
    }

    public function testAbs_ReturnsNotEmptySetInstance(): void
    {
        $set = new IntNotEmptySet(1, 2);

        self::assertInstanceOf(IntNotEmptySet::class, $set->abs());
    }

    public function testMultiply_ByFactor_ReturnsMultipliedSet(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertSame([3, 6, 9], $set->multiply(3)->toArray());
    }

    public function testSortAsc_Constructed_ReturnsSortedAscending(): void
    {
        $set = new IntNotEmptySet(3, 1, 2);

        self::assertSame([1, 2, 3], $set->sortAsc()->toArray());
    }

    public function testSortDesc_Constructed_ReturnsSortedDescending(): void
    {
        $set = new IntNotEmptySet(3, 1, 2);

        self::assertSame([3, 2, 1], $set->sortDesc()->toArray());
    }

    public function testSortAsc_ReturnsNotEmptySetInstance(): void
    {
        $set = new IntNotEmptySet(2, 1);

        self::assertInstanceOf(IntNotEmptySet::class, $set->sortAsc());
    }

    public function testDiff_WithAnotherCollection_ReturnsDifference(): void
    {
        $set = new IntNotEmptySet(1, 2, 3, 4);

        self::assertSame([1, 4], $set->diff(new IntList(2, 3))->toArray());
    }

    public function testDiff_AllElementsRemoved_ThrowsInvalidArgumentException(): void
    {
        $set = new IntNotEmptySet(1, 2);

        $this->expectException(InvalidArgumentException::class);

        $set->diff(new IntList(1, 2));
    }

    public function testIntersect_WithAnotherCollection_ReturnsIntersection(): void
    {
        $set = new IntNotEmptySet(1, 2, 3, 4);

        self::assertSame([2, 3], $set->intersect(new IntList(2, 3, 5))->toArray());
    }

    public function testIntersect_EmptyResult_ThrowsInvalidArgumentException(): void
    {
        $set = new IntNotEmptySet(1, 2);

        $this->expectException(InvalidArgumentException::class);

        $set->intersect(new IntList(3, 4));
    }

    public function testNegativeValues_Constructed_ReturnsNegativeOnly(): void
    {
        $set = new IntNotEmptySet(-2, 0, 1, 3);

        self::assertSame([-2], $set->negativeValues()->toArray());
    }

    public function testNegativeValues_NoNegatives_ThrowsInvalidArgumentException(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        $this->expectException(InvalidArgumentException::class);

        $set->negativeValues();
    }

    public function testNotZeroValues_Constructed_ReturnsNonZeroOnly(): void
    {
        $set = new IntNotEmptySet(0, 1, 2);

        self::assertSame([1, 2], $set->notZeroValues()->toArray());
    }

    public function testNotZeroValues_AllZeros_ThrowsInvalidArgumentException(): void
    {
        $set = new IntNotEmptySet(0);

        $this->expectException(InvalidArgumentException::class);

        $set->notZeroValues();
    }

    public function testPositiveValues_Constructed_ReturnsPositiveOnly(): void
    {
        $set = new IntNotEmptySet(-2, 0, 1, 3);

        self::assertSame([1, 3], $set->positiveValues()->toArray());
    }

    public function testPositiveValues_NoPositives_ThrowsInvalidArgumentException(): void
    {
        $set = new IntNotEmptySet(-1, -2);

        $this->expectException(InvalidArgumentException::class);

        $set->positiveValues();
    }

    public function testFindFirst_NoArgs_ReturnsFirstElement(): void
    {
        $set = new IntNotEmptySet(10, 20, 30);

        self::assertSame(10, $set->findFirst());
    }

    public function testFindFirst_WithPredicate_ReturnsMatchingElement(): void
    {
        $set = new IntNotEmptySet(1, 4, 3);

        self::assertSame(4, $set->findFirst(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testFindFirst_WithPredicate_NoMatch_ReturnsNull(): void
    {
        $set = new IntNotEmptySet(1, 3, 5);

        self::assertNull($set->findFirst(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testFindFirstAfter_Constructed_ReturnsNextElement(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertSame(2, $set->findFirstAfter(1));
    }

    public function testFindFirstAfter_LastElement_ReturnsNull(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertNull($set->findFirstAfter(3));
    }

    public function testFindFirstAfter_NotFound_ReturnsNull(): void
    {
        $set = new IntNotEmptySet(1, 2);

        self::assertNull($set->findFirstAfter(99));
    }

    public function testFindLast_NoArgs_ReturnsLastElement(): void
    {
        $set = new IntNotEmptySet(10, 20, 30);

        self::assertSame(30, $set->findLast());
    }

    public function testFindLast_WithPredicate_ReturnsLastMatchingElement(): void
    {
        $set = new IntNotEmptySet(1, 2, 3, 4);

        self::assertSame(4, $set->findLast(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testContains_ExistingElement_ReturnsTrue(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertTrue($set->contains(2));
    }

    public function testContains_NonExistingElement_ReturnsFalse(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertFalse($set->contains(99));
    }

    public function testHas_ExistingElement_ReturnsTrue(): void
    {
        $set = new IntNotEmptySet(1, 2);

        self::assertTrue($set->has(1));
    }

    public function testAny_MatchingPredicate_ReturnsTrue(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertTrue($set->any(static fn (int $i): bool => $i > 2));
    }

    public function testAny_NoMatch_ReturnsFalse(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertFalse($set->any(static fn (int $i): bool => $i > 10));
    }

    public function testAll_AllMatch_ReturnsTrue(): void
    {
        $set = new IntNotEmptySet(2, 4, 6);

        self::assertTrue($set->all(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testAll_NotAllMatch_ReturnsFalse(): void
    {
        $set = new IntNotEmptySet(2, 3, 6);

        self::assertFalse($set->all(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testNone_NoneMatch_ReturnsTrue(): void
    {
        $set = new IntNotEmptySet(1, 3, 5);

        self::assertTrue($set->none(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testNone_SomeMatch_ReturnsFalse(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertFalse($set->none(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testFlattenGroupBy_Constructed_ReturnsGroupedArray(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        $result = $set->flattenGroupBy(static fn (int $i): string => 0 === $i % 2 ? 'even' : 'odd');

        self::assertSame(['odd' => 3, 'even' => 2], $result);
    }

    public function testFilter_MatchingElements_ReturnsFilteredSet(): void
    {
        $set = new IntNotEmptySet(1, 2, 3, 4, 5);

        self::assertSame([2, 4], $set->filter(static fn (int $n): bool => 0 === $n % 2)->toArray());
    }

    public function testFilter_AllElementsRemoved_ThrowsInvalidArgumentException(): void
    {
        $set = new IntNotEmptySet(1, 3, 5);

        $this->expectException(InvalidArgumentException::class);

        $set->filter(static fn (int $n): bool => 0 === $n % 2);
    }

    public function testFilter_Constructed_ReturnsNotEmptySetInstance(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertInstanceOf(IntNotEmptySet::class, $set->filter(static fn (int $n): bool => $n > 1));
    }

    public function testFilterNot_MatchingElements_ReturnsFilteredSet(): void
    {
        $set = new IntNotEmptySet(1, 2, 3, 4, 5);

        self::assertSame([1, 3, 5], $set->filterNot(static fn (int $n): bool => 0 === $n % 2)->toArray());
    }

    public function testFilterNot_AllElementsRemoved_ThrowsInvalidArgumentException(): void
    {
        $set = new IntNotEmptySet(2, 4);

        $this->expectException(InvalidArgumentException::class);

        $set->filterNot(static fn (int $n): bool => 0 === $n % 2);
    }

    public function testCount_Constructed_ReturnsCount(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertSame(3, $set->count());
    }

    public function testCount_WithDuplicates_ReturnsDeduplicatedCount(): void
    {
        $set = new IntNotEmptySet(1, 2, 2, 3);

        self::assertSame(3, $set->count());
    }

    public function testIsEmpty_Constructed_ReturnsFalse(): void
    {
        $set = new IntNotEmptySet(1);

        self::assertFalse($set->isEmpty());
    }

    public function testIsNotEmpty_Constructed_ReturnsTrue(): void
    {
        $set = new IntNotEmptySet(1);

        self::assertTrue($set->isNotEmpty());
    }

    public function testUnique_WithDuplicatesInConstructor_ReturnsAlreadyUniqueSet(): void
    {
        $set = new IntNotEmptySet(1, 2, 3, 4, 2, 3);

        self::assertSame([1, 2, 3, 4], $set->unique()->toArray());
    }

    public function testUnique_Constructed_ReturnsNotEmptySetInstance(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertInstanceOf(IntNotEmptySet::class, $set->unique());
    }

    public function testMap_Constructed_ReturnsArray(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertSame([2, 4, 6], $set->map(static fn (int $i): int => $i * 2));
    }

    public function testReduce_Constructed_ReturnsSumViaReduce(): void
    {
        $set = new IntNotEmptySet(1, 2, 3, 4);

        $result = $set->reduce(static fn (?int $carry, int $item): int => $carry + $item, 0);

        self::assertSame(10, $result);
    }

    public function testSlice_ValidRange_ReturnsSlicedSet(): void
    {
        $set = new IntNotEmptySet(1, 2, 3, 4, 5);

        self::assertSame([2, 3], $set->slice(1, 2)->toArray());
    }

    public function testSlice_EmptyResult_ThrowsInvalidArgumentException(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        $this->expectException(InvalidArgumentException::class);

        $set->slice(10, 5);
    }

    public function testSlice_Constructed_ReturnsNotEmptySetInstance(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertInstanceOf(IntNotEmptySet::class, $set->slice(0, 2));
    }

    public function testSorted_DescComparator_ReturnsSortedDescSet(): void
    {
        $set = new IntNotEmptySet(1, 3, 2);

        self::assertSame([3, 2, 1], $set->sorted(static fn (int $i, int $j): int => $j <=> $i)->toArray());
    }

    public function testSorted_ReturnsNotEmptySetInstance(): void
    {
        $set = new IntNotEmptySet(2, 1);

        self::assertInstanceOf(IntNotEmptySet::class, $set->sorted(static fn (int $i, int $j): int => $i <=> $j));
    }

    public function testChunks_Constructed_ReturnsNotEmptySetChunks(): void
    {
        $set = new IntNotEmptySet(1, 2, 3, 4, 5);

        $result = $set->chunks(2);

        self::assertCount(3, $result);
        self::assertSame([1, 2], $result[0]->toArray());
        self::assertSame([3, 4], $result[1]->toArray());
        self::assertSame([5], $result[2]->toArray());
    }

    public function testChunks_Constructed_EachChunkIsNotEmptySetInstance(): void
    {
        $set = new IntNotEmptySet(1, 2);

        $result = $set->chunks(1);

        self::assertInstanceOf(IntNotEmptySet::class, $result[0]);
    }

    public function testChunks_ZeroSize_ThrowsInvalidArgumentException(): void
    {
        $set = new IntNotEmptySet(1);

        $this->expectException(InvalidArgumentException::class);

        $set->chunks(0);
    }

    public function testIterate_Constructed_ReturnsAllElements(): void
    {
        $set = new IntNotEmptySet(10, 20, 30);

        self::assertSame([10, 20, 30], [...$set]);
    }

    public function testIndexOf_ExistingElement_ReturnsIndex(): void
    {
        $set = new IntNotEmptySet(10, 20, 30);

        self::assertSame(1, $set->indexOf(20));
    }

    public function testIndexOf_NonExistingElement_ReturnsNull(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertNull($set->indexOf(99));
    }

    public function testLastIndexOf_UniqueElements_ReturnsSameAsIndexOf(): void
    {
        $set = new IntNotEmptySet(1, 2, 3);

        self::assertSame(0, $set->lastIndexOf(1));
    }

    public function testGroupBy_Constructed_ReturnsGroupedMap(): void
    {
        $set = new IntNotEmptySet(1, 2, 3, 4);

        $result = $set->groupBy(static fn (int $i): string => 0 === $i % 2 ? 'even' : 'odd');

        self::assertSame(['odd' => [1, 3], 'even' => [2, 4]], $result);
    }
}
