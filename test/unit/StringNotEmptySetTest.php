<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractList;
use Purr\Collection\AbstractSet;
use Purr\Collection\Exception\InvalidArgumentException;
use Purr\Collection\StringCollectionTrait;
use Purr\Collection\StringList;
use Purr\Collection\StringNotEmptySet;
use Purr\Collection\StringSet;

#[CoversClass(StringNotEmptySet::class)]
#[CoversClass(StringSet::class)]
#[CoversClass(AbstractSet::class)]
#[CoversClass(AbstractList::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(StringCollectionTrait::class)]
final class StringNotEmptySetTest extends TestCase
{
    // region Constructor

    public function testConstructor_EmptyList_throwsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Strings are empty');

        new StringNotEmptySet();
    }

    public function testConstructor_WithElements_ReturnsSet(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertSame(['a', 'b', 'c'], $set->toArray());
    }

    public function testConstructor_WithDuplicates_SetDeduplicatesValues(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'a', 'c', 'b');

        self::assertSame(['a', 'b', 'c'], $set->toArray());
    }

    // endregion

    // region StringCollectionTrait: fromInts

    public function testFromInts_ValidInts_ReturnsNotEmptySetInstance(): void
    {
        $set = StringNotEmptySet::fromInts(1, 2, 3);

        self::assertInstanceOf(StringNotEmptySet::class, $set);
        self::assertSame(['1', '2', '3'], $set->toArray());
    }

    public function testFromInts_WithDuplicates_ReturnsDeduplicatedSet(): void
    {
        $set = StringNotEmptySet::fromInts(1, 2, 1, 3);

        self::assertSame(['1', '2', '3'], $set->toArray());
    }

    // endregion

    // region StringCollectionTrait: join / implode / sortedAlphabetically

    public function testJoin_WithSeparator_ReturnsJoinedString(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertSame('a,b,c', $set->join(','));
    }

    public function testImplode_WithSeparator_ReturnsJoinedString(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertSame('a-b-c', $set->implode('-'));
    }

    public function testSortedAlphabeticallySortAsc_Constructed_ReturnsSortedSet(): void
    {
        $set = new StringNotEmptySet('c', 'a', 'b');

        self::assertSame(['a', 'b', 'c'], $set->sortedAlphabetically()->toArray());
    }

    public function testSortedAlphabeticallySortAsc_ReturnsNotEmptySetInstance(): void
    {
        $set = new StringNotEmptySet('b', 'a');

        self::assertInstanceOf(StringNotEmptySet::class, $set->sortedAlphabetically());
    }

    public function testSortedAlphabeticallyDesc_Constructed_ReturnsSortedSetDescending(): void
    {
        $set = new StringNotEmptySet('a', 'c', 'b');

        self::assertSame(['c', 'b', 'a'], $set->sortedAlphabetically(desc: true)->toArray());
    }

    public function testSortedAlphabetically_Desc_ReturnsNotEmptySetInstance(): void
    {
        $set = new StringNotEmptySet('a', 'b');

        self::assertInstanceOf(StringNotEmptySet::class, $set->sortedAlphabetically(desc: true));
    }

    // endregion

    // region StringCollectionTrait: diff / intersect

    public function testDiff_WithAnotherCollection_ReturnsDifference(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c', 'd');

        self::assertSame(['a', 'd'], $set->diff(new StringList('b', 'c'))->toArray());
    }

    public function testDiff_ReturnsNotEmptySetInstance(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertInstanceOf(StringNotEmptySet::class, $set->diff(new StringList('a')));
    }

    public function testDiff_AllElementsRemoved_ThrowsInvalidArgumentException(): void
    {
        $set = new StringNotEmptySet('a', 'b');

        $this->expectException(InvalidArgumentException::class);

        $set->diff(new StringList('a', 'b'));
    }

    public function testIntersect_WithAnotherCollection_ReturnsIntersection(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c', 'd');

        self::assertSame(['b', 'c'], $set->intersect(new StringList('b', 'c', 'e'))->toArray());
    }

    public function testIntersect_ReturnsNotEmptySetInstance(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertInstanceOf(StringNotEmptySet::class, $set->intersect(new StringList('a', 'b')));
    }

    public function testIntersect_EmptyResult_ThrowsInvalidArgumentException(): void
    {
        $set = new StringNotEmptySet('a', 'b');

        $this->expectException(InvalidArgumentException::class);

        $set->intersect(new StringList('c', 'd'));
    }

    // endregion

    // region AbstractCollection: find methods

    public function testFindFirst_NoArgs_ReturnsFirstElement(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertSame('a', $set->findFirst());
    }

    public function testFindFirst_WithPredicate_ReturnsMatchingElement(): void
    {
        $set = new StringNotEmptySet('a', 'bb', 'c');

        self::assertSame('bb', $set->findFirst(static fn (string $s): bool => strlen($s) > 1));
    }

    public function testFindFirst_WithPredicate_NoMatch_ReturnsNull(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertNull($set->findFirst(static fn (string $s): bool => strlen($s) > 5));
    }

    public function testFindFirstAfter_Constructed_ReturnsNextElement(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertSame('b', $set->findFirstAfter('a'));
    }

    public function testFindFirstAfter_LastElement_ReturnsNull(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertNull($set->findFirstAfter('c'));
    }

    public function testFindFirstAfter_NotFound_ReturnsNull(): void
    {
        $set = new StringNotEmptySet('a', 'b');

        self::assertNull($set->findFirstAfter('z'));
    }

    public function testFindLast_NoArgs_ReturnsLastElement(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertSame('c', $set->findLast());
    }

    public function testFindLast_WithPredicate_ReturnsLastMatchingElement(): void
    {
        $set = new StringNotEmptySet('a', 'bb', 'c', 'dd');

        self::assertSame('dd', $set->findLast(static fn (string $s): bool => strlen($s) > 1));
    }

    // endregion

    // region AbstractCollection: contains / has / any / all / none

    public function testContains_ExistingElement_ReturnsTrue(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertTrue($set->contains('b'));
    }

    public function testContains_NonExistingElement_ReturnsFalse(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertFalse($set->contains('z'));
    }

    public function testHas_ExistingElement_ReturnsTrue(): void
    {
        $set = new StringNotEmptySet('a', 'b');

        self::assertTrue($set->has('a'));
    }

    public function testAny_MatchingPredicate_ReturnsTrue(): void
    {
        $set = new StringNotEmptySet('a', 'bb', 'c');

        self::assertTrue($set->any(static fn (string $s): bool => strlen($s) > 1));
    }

    public function testAny_NoMatch_ReturnsFalse(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertFalse($set->any(static fn (string $s): bool => strlen($s) > 5));
    }

    public function testAll_AllMatch_ReturnsTrue(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertTrue($set->all(static fn (string $s): bool => 1 === strlen($s)));
    }

    public function testAll_NotAllMatch_ReturnsFalse(): void
    {
        $set = new StringNotEmptySet('a', 'bb', 'c');

        self::assertFalse($set->all(static fn (string $s): bool => 1 === strlen($s)));
    }

    public function testNone_NoneMatch_ReturnsTrue(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertTrue($set->none(static fn (string $s): bool => strlen($s) > 5));
    }

    public function testNone_SomeMatch_ReturnsFalse(): void
    {
        $set = new StringNotEmptySet('a', 'bb', 'c');

        self::assertFalse($set->none(static fn (string $s): bool => strlen($s) > 1));
    }

    // endregion

    // region AbstractCollection: groupBy / flattenGroupBy

    public function testFlattenGroupBy_Constructed_ReturnsGroupedArray(): void
    {
        $set = new StringNotEmptySet('a', 'bb', 'c');

        $result = $set->flattenGroupBy(static fn (string $s): string => strlen($s) > 1 ? 'long' : 'short');

        self::assertSame(['short' => 'c', 'long' => 'bb'], $result);
    }

    // endregion

    // region AbstractCollection: filter / filterNot

    public function testFilter_MatchingElements_ReturnsFilteredSet(): void
    {
        $set = new StringNotEmptySet('a', 'bb', 'ccc');

        self::assertSame(['bb', 'ccc'], $set->filter(static fn (string $s): bool => strlen($s) > 1)->toArray());
    }

    public function testFilter_ReturnsNotEmptySetInstance(): void
    {
        $set = new StringNotEmptySet('a', 'bb', 'c');

        self::assertInstanceOf(StringNotEmptySet::class, $set->filter(static fn (string $s): bool => strlen($s) > 0));
    }

    public function testFilter_AllElementsRemoved_ThrowsInvalidArgumentException(): void
    {
        $set = new StringNotEmptySet('a', 'b');

        $this->expectException(InvalidArgumentException::class);

        $set->filter(static fn (string $s): bool => strlen($s) > 5);
    }

    public function testFilterNot_MatchingElements_ReturnsFilteredSet(): void
    {
        $set = new StringNotEmptySet('a', 'bb', 'ccc');

        self::assertSame(['a'], $set->filterNot(static fn (string $s): bool => strlen($s) > 1)->toArray());
    }

    public function testFilterNot_ReturnsNotEmptySetInstance(): void
    {
        $set = new StringNotEmptySet('a', 'bb', 'c');

        self::assertInstanceOf(StringNotEmptySet::class, $set->filterNot(static fn (string $s): bool => strlen($s) > 1));
    }

    public function testFilterNot_AllElementsRemoved_ThrowsInvalidArgumentException(): void
    {
        $set = new StringNotEmptySet('a', 'b');

        $this->expectException(InvalidArgumentException::class);

        $set->filterNot(static fn (string $s): bool => strlen($s) >= 1);
    }

    // endregion

    // region AbstractCollection: count / isEmpty / isNotEmpty

    public function testCount_Constructed_ReturnsCount(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertSame(3, $set->count());
    }

    public function testCount_WithDuplicates_ReturnsDeduplicatedCount(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'a');

        self::assertSame(2, $set->count());
    }

    public function testIsEmpty_Constructed_ReturnsFalse(): void
    {
        $set = new StringNotEmptySet('a');

        self::assertFalse($set->isEmpty());
    }

    public function testIsNotEmpty_Constructed_ReturnsTrue(): void
    {
        $set = new StringNotEmptySet('a');

        self::assertTrue($set->isNotEmpty());
    }

    // endregion

    // region AbstractCollection: unique

    public function testUnique_Constructed_ReturnsUniqueList(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c', 'a', 'b');

        self::assertSame(['a', 'b', 'c'], $set->unique()->toArray());
    }

    public function testUnique_ReturnsNotEmptySetInstance(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertInstanceOf(StringNotEmptySet::class, $set->unique());
    }

    // endregion

    // region AbstractCollection: map / reduce

    public function testMap_Constructed_ReturnsArray(): void
    {
        $set = new StringNotEmptySet('a', 'bb', 'ccc');

        self::assertSame([1, 2, 3], $set->map(static fn (string $s): int => strlen($s)));
    }

    public function testReduce_Constructed_ReturnsConcatenated(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertSame('abc', $set->reduce(static fn (string $carry, string $s): string => $carry.$s, ''));
    }

    // endregion

    // region AbstractCollection: sorted / slice

    public function testSorted_DescComparator_ReturnsSortedDescSet(): void
    {
        $set = new StringNotEmptySet('a', 'c', 'b');

        self::assertSame(['c', 'b', 'a'], $set->sorted(static fn (string $a, string $b): int => $b <=> $a)->toArray());
    }

    public function testSorted_ReturnsNotEmptySetInstance(): void
    {
        $set = new StringNotEmptySet('b', 'a');

        self::assertInstanceOf(StringNotEmptySet::class, $set->sorted(static fn (string $a, string $b): int => $a <=> $b));
    }

    public function testSlice_ValidRange_ReturnsSlicedSet(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c', 'd', 'e');

        self::assertSame(['b', 'c'], $set->slice(1, 2)->toArray());
    }

    public function testSlice_ReturnsNotEmptySetInstance(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertInstanceOf(StringNotEmptySet::class, $set->slice(0, 2));
    }

    public function testSlice_EmptyResult_ThrowsInvalidArgumentException(): void
    {
        $set = new StringNotEmptySet('a', 'b');

        $this->expectException(InvalidArgumentException::class);

        $set->slice(10, 5);
    }

    // endregion

    // region AbstractCollection: chunks

    public function testChunks_Constructed_ReturnsNotEmptySetChunks(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c', 'd', 'e');

        $result = $set->chunks(2);

        self::assertCount(3, $result);
        self::assertSame(['a', 'b'], $result[0]->toArray());
        self::assertSame(['c', 'd'], $result[1]->toArray());
        self::assertSame(['e'], $result[2]->toArray());
    }

    public function testChunks_EachChunkIsNotEmptySetInstance(): void
    {
        $set = new StringNotEmptySet('a', 'b');

        self::assertInstanceOf(StringNotEmptySet::class, $set->chunks(1)[0]);
    }

    // endregion

    // region AbstractCollection: iterator

    public function testIterate_Constructed_ReturnsAllElements(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertSame(['a', 'b', 'c'], [...$set]);
    }

    // endregion

    // region AbstractList: indexOf / lastIndexOf / groupBy

    public function testIndexOf_ExistingElement_ReturnsIndex(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertSame(1, $set->indexOf('b'));
    }

    public function testIndexOf_NonExistingElement_ReturnsNull(): void
    {
        $set = new StringNotEmptySet('a', 'b');

        self::assertNull($set->indexOf('z'));
    }

    public function testLastIndexOf_UniqueElements_ReturnsSameAsIndexOf(): void
    {
        $set = new StringNotEmptySet('a', 'b', 'c');

        self::assertSame(0, $set->lastIndexOf('a'));
    }

    public function testGroupBy_Constructed_ReturnsGroupedMap(): void
    {
        $set = new StringNotEmptySet('a', 'bb', 'c', 'dd');

        $result = $set->groupBy(static fn (string $s): string => strlen($s) > 1 ? 'long' : 'short');

        self::assertSame(['short' => ['a', 'c'], 'long' => ['bb', 'dd']], $result);
    }

    // endregion
}
