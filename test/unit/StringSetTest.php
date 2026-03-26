<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractList;
use Purr\Collection\AbstractSet;
use Purr\Collection\StringCollectionTrait;
use Purr\Collection\StringList;
use Purr\Collection\StringSet;

#[CoversClass(StringSet::class)]
#[CoversClass(AbstractSet::class)]
#[CoversClass(AbstractList::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(StringCollectionTrait::class)]
class StringSetTest extends TestCase
{
    // region Constructor

    public function testConstructor_WithElements_ReturnsSet(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertSame(['a', 'b', 'c'], $set->toArray());
    }

    public function testConstructor_Empty_ReturnsEmptySet(): void
    {
        $set = new StringSet();

        self::assertSame([], $set->toArray());
    }

    public function testToArray_Constructed_ReturnsUniqueValues(): void
    {
        $set = new StringSet('a', 'b', 'c', 'c', 'a');

        self::assertSame(['a', 'b', 'c'], $set->toArray());
    }

    // endregion

    // region StringCollectionTrait: fromInts

    public function testFromInts_Constructed_ReturnsStringSetInstance(): void
    {
        $set = StringSet::fromInts(1, 2, 3);

        self::assertInstanceOf(StringSet::class, $set);
        self::assertSame(['1', '2', '3'], $set->toArray());
    }

    public function testFromInts_WithDuplicates_ReturnsDeduplicatedStringSet(): void
    {
        $set = StringSet::fromInts(1, 2, 1, 3);

        self::assertSame(['1', '2', '3'], $set->toArray());
    }

    // endregion

    // region StringCollectionTrait: join / implode / sortedAlphabetically

    public function testJoin_WithSeparator_ReturnsJoinedString(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertSame('a,b,c', $set->join(','));
    }

    public function testJoin_WithoutSeparator_ReturnsConcatenatedString(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertSame('abc', $set->join());
    }

    public function testImplode_WithSeparator_ReturnsJoinedString(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertSame('a-b-c', $set->implode('-'));
    }

    public function testSortedAlphabeticallySortAsc_Constructed_ReturnsSortedSet(): void
    {
        $set = new StringSet('c', 'b', 'a');

        self::assertSame(['a', 'b', 'c'], $set->sortedAlphabetically()->toArray());
    }

    public function testSortedAlphabeticallySortAsc_ReturnsStringSetInstance(): void
    {
        $set = new StringSet('b', 'a');

        self::assertInstanceOf(StringSet::class, $set->sortedAlphabetically());
    }

    public function testSortedAlphabeticallyDesc_Constructed_ReturnsSortedSetDescending(): void
    {
        $set = new StringSet('a', 'c', 'b');

        self::assertSame(['c', 'b', 'a'], $set->sortedAlphabetically(desc: true)->toArray());
    }

    public function testSortedAlphabetically_Desc_ReturnsStringSetInstance(): void
    {
        $set = new StringSet('a', 'b');

        self::assertInstanceOf(StringSet::class, $set->sortedAlphabetically(desc: true));
    }

    // endregion

    // region StringCollectionTrait: diff / intersect

    public function testDiff_WithAnotherCollection_ReturnsDifference(): void
    {
        $set = new StringSet('a', 'b', 'c', 'd');

        self::assertSame(['a', 'd'], $set->diff(new StringList('b', 'c'))->toArray());
    }

    public function testDiff_NoOverlap_LeavesSetUnchanged(): void
    {
        $set = new StringSet('a', 'b');

        self::assertSame(['a', 'b'], $set->diff(new StringList('c', 'd'))->toArray());
    }

    public function testDiff_AllElementsRemoved_ReturnsEmptySet(): void
    {
        $set = new StringSet('a', 'b');

        self::assertSame([], $set->diff(new StringList('a', 'b'))->toArray());
    }

    public function testDiff_ReturnsStringSetInstance(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertInstanceOf(StringSet::class, $set->diff(new StringList('a')));
    }

    public function testIntersect_WithAnotherCollection_ReturnsIntersection(): void
    {
        $set = new StringSet('a', 'b', 'c', 'd');

        self::assertSame(['b', 'c'], $set->intersect(new StringList('b', 'c', 'e'))->toArray());
    }

    public function testIntersect_NoOverlap_ReturnsEmptySet(): void
    {
        $set = new StringSet('a', 'b');

        self::assertSame([], $set->intersect(new StringList('c', 'd'))->toArray());
    }

    public function testIntersect_ReturnsStringSetInstance(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertInstanceOf(StringSet::class, $set->intersect(new StringList('a')));
    }

    // endregion

    // region AbstractCollection: find methods

    public function testFindFirst_NoArgs_ReturnsFirstElement(): void
    {
        $set = new StringSet('a', 'c', 'd', 'b');

        self::assertSame('a', $set->findFirst());
    }

    public function testFindFirst_WithPredicate_ReturnsMatchingElement(): void
    {
        $set = new StringSet('a', 'bb', 'c');

        self::assertSame('bb', $set->findFirst(static fn (string $s): bool => strlen($s) > 1));
    }

    public function testFindFirst_WithPredicate_NoMatch_ReturnsNull(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertNull($set->findFirst(static fn (string $s): bool => strlen($s) > 5));
    }

    public function testFindFirstAfter_Constructed_ReturnsNextElement(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertSame('b', $set->findFirstAfter('a'));
    }

    public function testFindFirstAfter_LastElement_ReturnsNull(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertNull($set->findFirstAfter('c'));
    }

    public function testFindFirstAfter_NotFound_ReturnsNull(): void
    {
        $set = new StringSet('a', 'b');

        self::assertNull($set->findFirstAfter('z'));
    }

    public function testFindLast_NoArgs_ReturnsLastElement(): void
    {
        $set = new StringSet('a', 'c', 'd', 'b');

        self::assertSame('b', $set->findLast());
    }

    public function testFindLast_WithPredicate_ReturnsLastMatchingElement(): void
    {
        $set = new StringSet('a', 'bb', 'c', 'dd');

        self::assertSame('dd', $set->findLast(static fn (string $s): bool => strlen($s) > 1));
    }

    // endregion

    // region AbstractCollection: contains / has / any / all / none

    public function testContains_ExistingElement_ReturnsTrue(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertTrue($set->contains('b'));
    }

    public function testContains_NonExistingElement_ReturnsFalse(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertFalse($set->contains('z'));
    }

    public function testHas_ExistingElement_ReturnsTrue(): void
    {
        $set = new StringSet('a', 'b');

        self::assertTrue($set->has('a'));
    }

    public function testAny_MatchingPredicate_ReturnsTrue(): void
    {
        $set = new StringSet('a', 'bb', 'c');

        self::assertTrue($set->any(static fn (string $s): bool => strlen($s) > 1));
    }

    public function testAny_NoMatch_ReturnsFalse(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertFalse($set->any(static fn (string $s): bool => strlen($s) > 5));
    }

    public function testAll_AllMatch_ReturnsTrue(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertTrue($set->all(static fn (string $s): bool => 1 === strlen($s)));
    }

    public function testAll_NotAllMatch_ReturnsFalse(): void
    {
        $set = new StringSet('a', 'bb', 'c');

        self::assertFalse($set->all(static fn (string $s): bool => 1 === strlen($s)));
    }

    public function testNone_NoneMatch_ReturnsTrue(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertTrue($set->none(static fn (string $s): bool => strlen($s) > 5));
    }

    public function testNone_SomeMatch_ReturnsFalse(): void
    {
        $set = new StringSet('a', 'bb', 'c');

        self::assertFalse($set->none(static fn (string $s): bool => strlen($s) > 1));
    }

    // endregion

    // region AbstractCollection: groupBy / flattenGroupBy

    public function testFlattenGroupBy_Constructed_ReturnsGroupedArray(): void
    {
        $set = new StringSet('a', 'bb', 'c');

        $result = $set->flattenGroupBy(static fn (string $s): string => strlen($s) > 1 ? 'long' : 'short');

        self::assertSame(['short' => 'c', 'long' => 'bb'], $result);
    }

    // endregion

    // region AbstractCollection: filter / filterNot

    public function testFilter_EqualsBCheck_ReturnsMatchingSet(): void
    {
        $set = new StringSet('a', 'b', 'c', 'd');

        self::assertSame(['b'], $set->filter(fn (string $a) => 'b' === $a)->toArray());
    }

    public function testFilter_Constructed_ReturnsStringSetInstance(): void
    {
        $set = new StringSet('a', 'b', 'c', 'd');

        self::assertInstanceOf(StringSet::class, $set->filter(fn (string $a) => true));
    }

    public function testFilterNot_WithPredicate_ReturnsFilteredSet(): void
    {
        $set = new StringSet('a', 'bb', 'ccc');

        self::assertSame(['a'], $set->filterNot(static fn (string $s): bool => strlen($s) > 1)->toArray());
    }

    public function testFilterNot_ReturnsStringSetInstance(): void
    {
        $set = new StringSet('a', 'bb', 'c');

        self::assertInstanceOf(StringSet::class, $set->filterNot(static fn (string $s): bool => strlen($s) > 1));
    }

    // endregion

    // region AbstractCollection: count / isEmpty / isNotEmpty

    public function testCount_TwoElements_ReturnsTwo(): void
    {
        $set = new StringSet('a', 'b');

        self::assertSame(2, $set->count());
    }

    public function testCount_WithDuplicates_ReturnsDeduplicatedCount(): void
    {
        $set = new StringSet('a', 'b', 'a');

        self::assertSame(2, $set->count());
    }

    public function testIsEmpty_EmptySet_ReturnsTrue(): void
    {
        $set = new StringSet();

        self::assertTrue($set->isEmpty());
    }

    public function testIsNotEmpty_Constructed_ReturnsTrue(): void
    {
        $set = new StringSet('a');

        self::assertTrue($set->isNotEmpty());
    }

    // endregion

    // region AbstractCollection: unique

    public function testUnique_Constructed_ReturnsUniqueValues(): void
    {
        $set = new StringSet('a', 'b', 'c', 'c', 'a');

        self::assertSame(['a', 'b', 'c'], $set->unique()->toArray());
    }

    public function testUnique_Constructed_ReturnsStringSetInstance(): void
    {
        $set = new StringSet('a', 'b', 'c', 'c', 'a');

        self::assertInstanceOf(StringSet::class, $set->unique());
    }

    // endregion

    // region AbstractCollection: map / reduce

    public function testMap_Constructed_ReturnsArray(): void
    {
        $set = new StringSet('a', 'bb', 'ccc');

        self::assertSame([1, 2, 3], $set->map(static fn (string $s): int => strlen($s)));
    }

    public function testReduce_Constructed_ReturnsConcatenated(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertSame('abc', $set->reduce(static fn (string $carry, string $s): string => $carry.$s, ''));
    }

    // endregion

    // region AbstractCollection: sorted / slice

    public function testSorted_DescComparator_ReturnsSortedDescSet(): void
    {
        $set = new StringSet('a', 'c', 'b');

        self::assertSame(['c', 'b', 'a'], $set->sorted(static fn (string $a, string $b): int => $b <=> $a)->toArray());
    }

    public function testSorted_ReturnsStringSetInstance(): void
    {
        $set = new StringSet('b', 'a');

        self::assertInstanceOf(StringSet::class, $set->sorted(static fn (string $a, string $b): int => $a <=> $b));
    }

    public function testSlice_ValidRange_ReturnsSlicedSet(): void
    {
        $set = new StringSet('a', 'b', 'c', 'd', 'e');

        self::assertSame(['b', 'c'], $set->slice(1, 2)->toArray());
    }

    public function testSlice_ReturnsStringSetInstance(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertInstanceOf(StringSet::class, $set->slice(0, 2));
    }

    public function testSlice_OffsetExceedsSize_ReturnsEmptySet(): void
    {
        $set = new StringSet('a', 'b');

        self::assertSame([], $set->slice(5, 2)->toArray());
    }

    // endregion

    // region AbstractCollection: chunks

    public function testChunks_Constructed_ReturnsStringSetChunks(): void
    {
        $set = new StringSet('a', 'b', 'c', 'd', 'e');

        $result = $set->chunks(2);

        self::assertCount(3, $result);
        self::assertSame(['a', 'b'], $result[0]->toArray());
        self::assertSame(['c', 'd'], $result[1]->toArray());
        self::assertSame(['e'], $result[2]->toArray());
    }

    public function testChunks_EachChunkIsStringSetInstance(): void
    {
        $set = new StringSet('a', 'b');

        self::assertInstanceOf(StringSet::class, $set->chunks(1)[0]);
    }

    // endregion

    // region AbstractCollection: iterator

    public function testIterate_Constructed_ReturnsAllElements(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertSame(['a', 'b', 'c'], [...$set]);
    }

    // endregion

    // region AbstractList: indexOf / lastIndexOf / groupBy

    public function testIndexOf_ExistingElement_ReturnsIndex(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertSame(1, $set->indexOf('b'));
    }

    public function testIndexOf_NonExistingElement_ReturnsNull(): void
    {
        $set = new StringSet('a', 'b');

        self::assertNull($set->indexOf('z'));
    }

    public function testLastIndexOf_UniqueElements_ReturnsSameAsIndexOf(): void
    {
        $set = new StringSet('a', 'b', 'c');

        self::assertSame(0, $set->lastIndexOf('a'));
    }

    public function testGroupBy_Constructed_ReturnsGroupedMap(): void
    {
        $set = new StringSet('a', 'bb', 'c', 'dd');

        $result = $set->groupBy(static fn (string $s): string => strlen($s) > 1 ? 'long' : 'short');

        self::assertSame(['short' => ['a', 'c'], 'long' => ['bb', 'dd']], $result);
    }

    // endregion
}
