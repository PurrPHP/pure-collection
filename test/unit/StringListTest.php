<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractList;
use Purr\Collection\StringCollectionTrait;
use Purr\Collection\StringList;

#[CoversClass(StringList::class)]
#[CoversClass(AbstractList::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(StringCollectionTrait::class)]
class StringListTest extends TestCase
{
    public function testConstructor_StringsProvided_ReturnsTargetList(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertSame(['a', 'b', 'c'], $list->toArray());
    }

    public function testConstructor_Empty_ReturnsEmptyList(): void
    {
        $list = new StringList();

        self::assertSame([], $list->toArray());
    }

    public function testConstructor_DuplicatesAllowed_KeepsDuplicates(): void
    {
        $list = new StringList('a', 'b', 'a');

        self::assertSame(['a', 'b', 'a'], $list->toArray());
    }

    /** @param array<int, string> $source */
    #[DataProvider('providerFindFirst')]
    public function testFindFirst_WithOptionalPredicate_ReturnsTargetValue(
        ?string $result,
        ?callable $predicate,
        array $source
    ): void {
        $list = new StringList(...$source);

        self::assertSame($result, $list->findFirst($predicate));
    }

    /** @return array<string, array<int, mixed>> */
    public static function providerFindFirst(): array
    {
        return [
            'empty' => [null, null, []],
            'single' => ['a', null, ['a']],
            'first of two' => ['a', null, ['a', 'b']],
            'predicate match' => ['bb', static fn (string $s): bool => strlen($s) > 1, ['a', 'bb', 'ccc']],
            'predicate no match' => [null, static fn (string $s): bool => strlen($s) > 5, ['a', 'bb']],
        ];
    }

    public function testFilter_WithPredicate_ReturnsFilteredList(): void
    {
        $list = new StringList('a', 'bb', 'ccc');

        self::assertSame(['bb', 'ccc'], $list->filter(fn (string $s): bool => strlen($s) > 1)->toArray());
    }

    public function testFilterNot_WithPredicate_ReturnsFilteredList(): void
    {
        $list = new StringList('a', 'bb', 'ccc');

        self::assertSame(['a'], $list->filterNot(fn (string $s): bool => strlen($s) > 1)->toArray());
    }

    public function testMap_Constructed_ReturnsTargetArray(): void
    {
        $list = new StringList('a', 'bb');

        self::assertSame([1, 2], $list->map(fn (string $s): int => strlen($s)));
    }

    public function testContains_ExistingValue_ReturnsTrue(): void
    {
        $list = new StringList('a', 'b');

        self::assertTrue($list->contains('a'));
    }

    public function testContains_MissingValue_ReturnsFalse(): void
    {
        $list = new StringList('a', 'b');

        self::assertFalse($list->contains('c'));
    }

    public function testCount_TwoElements_ReturnsTwo(): void
    {
        $list = new StringList('a', 'b');

        self::assertSame(2, $list->count());
    }

    public function testIsEmpty_Empty_ReturnsTrue(): void
    {
        self::assertTrue((new StringList())->isEmpty());
    }

    public function testIsNotEmpty_NotEmpty_ReturnsTrue(): void
    {
        self::assertTrue((new StringList('a'))->isNotEmpty());
    }

    public function testSorted_Alphabetically_ReturnsSortedList(): void
    {
        $list = new StringList('c', 'a', 'b');

        self::assertSame(['a', 'b', 'c'], $list->sorted(fn (string $a, string $b): int => $a <=> $b)->toArray());
    }

    public function testUnique_WithDuplicates_ReturnsUniqueList(): void
    {
        $list = new StringList('a', 'b', 'a');

        self::assertSame(['a', 'b'], $list->unique()->toArray());
    }

    public function testGroupBy_Constructed_ReturnsGroupedArray(): void
    {
        $list = new StringList('a', 'bb', 'c', 'dd');

        $grouped = $list->groupBy(fn (string $s): string => strlen($s) > 1 ? 'long' : 'short');

        self::assertSame(['short' => ['a', 'c'], 'long' => ['bb', 'dd']], $grouped);
    }

    public function testSlice_WithOffsetAndLimit_ReturnsTargetList(): void
    {
        $list = new StringList('a', 'b', 'c', 'd');

        self::assertSame(['b', 'c'], $list->slice(1, 2)->toArray());
    }

    public function testChunks_Constructed_ReturnsTargetChunks(): void
    {
        $list = new StringList('a', 'b', 'c', 'd', 'e');

        $chunks = $list->chunks(2);

        self::assertCount(3, $chunks);
        self::assertSame(['a', 'b'], $chunks[0]->toArray());
        self::assertSame(['c', 'd'], $chunks[1]->toArray());
        self::assertSame(['e'], $chunks[2]->toArray());
    }

    public function testAny_WithMatchingPredicate_ReturnsTrue(): void
    {
        $list = new StringList('a', 'bb');

        self::assertTrue($list->any(fn (string $s): bool => strlen($s) > 1));
    }

    public function testAll_AllSatisfyPredicate_ReturnsTrue(): void
    {
        $list = new StringList('a', 'b');

        self::assertTrue($list->all(fn (string $s): bool => 1 === strlen($s)));
    }

    public function testNone_NoneMatchPredicate_ReturnsTrue(): void
    {
        $list = new StringList('a', 'b');

        self::assertTrue($list->none(fn (string $s): bool => strlen($s) > 5));
    }

    public function testReduce_Constructed_ReturnsConcatenated(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertSame('abc', $list->reduce(fn (string $carry, string $s): string => $carry.$s, ''));
    }

    public function testDestruct_SomeList_ReturnsSourceValues(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertSame(['a', 'b', 'c'], [...$list]);
    }

    public function testFromInts_IntArray_ReturnsStringList(): void
    {
        $list = StringList::fromInts(1, 2, 3);

        self::assertSame(['1', '2', '3'], $list->toArray());
    }

    public function testJoin_WithSeparator_ReturnsJoinedString(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertSame('a,b,c', $list->join(','));
    }

    public function testJoin_WithoutSeparator_ReturnsConcatenatedString(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertSame('abc', $list->join());
    }

    public function testImplode_WithSeparator_ReturnsJoinedString(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertSame('a-b-c', $list->implode('-'));
    }

    public function testDiff_WithAnotherList_ReturnsDifference(): void
    {
        $list = new StringList('a', 'b', 'c', 'd');

        self::assertSame(['a', 'd'], $list->diff(new StringList('b', 'c'))->toArray());
    }

    public function testIntersect_WithAnotherList_ReturnsIntersection(): void
    {
        $list = new StringList('a', 'b', 'c', 'd');

        self::assertSame(['b', 'c'], $list->intersect(new StringList('b', 'c', 'e'))->toArray());
    }

    public function testIntersect_NoOverlap_ReturnsEmptyList(): void
    {
        $list = new StringList('a', 'b');

        self::assertSame([], $list->intersect(new StringList('c', 'd'))->toArray());
    }

    public function testIntersect_ReturnsStringListInstance(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertInstanceOf(StringList::class, $list->intersect(new StringList('a')));
    }

    public function testDiff_AllElementsRemoved_ReturnsEmptyList(): void
    {
        $list = new StringList('a', 'b');

        self::assertSame([], $list->diff(new StringList('a', 'b'))->toArray());
    }

    public function testDiff_ReturnsStringListInstance(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertInstanceOf(StringList::class, $list->diff(new StringList('a')));
    }

    // region AbstractCollection: find methods

    public function testFindLast_NoArgs_ReturnsLastElement(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertSame('c', $list->findLast());
    }

    public function testFindLast_WithPredicate_ReturnsLastMatchingElement(): void
    {
        $list = new StringList('a', 'bb', 'c', 'dd');

        self::assertSame('dd', $list->findLast(static fn (string $s): bool => strlen($s) > 1));
    }

    public function testFindFirstAfter_Constructed_ReturnsNextElement(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertSame('b', $list->findFirstAfter('a'));
    }

    public function testFindFirstAfter_LastElement_ReturnsNull(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertNull($list->findFirstAfter('c'));
    }

    public function testFindFirstAfter_NotFound_ReturnsNull(): void
    {
        $list = new StringList('a', 'b');

        self::assertNull($list->findFirstAfter('z'));
    }

    // endregion

    // region AbstractCollection: has / flattenGroupBy

    public function testHas_ExistingElement_ReturnsTrue(): void
    {
        $list = new StringList('a', 'b');

        self::assertTrue($list->has('a'));
    }

    public function testFlattenGroupBy_Constructed_ReturnsGroupedArray(): void
    {
        $list = new StringList('a', 'bb', 'c');

        $result = $list->flattenGroupBy(static fn (string $s): string => strlen($s) > 1 ? 'long' : 'short');

        self::assertSame(['short' => 'c', 'long' => 'bb'], $result);
    }

    // endregion

    // region StringCollectionTrait: sortedAlphabetically

    public function testSortedAlphabetically_DefaultAsc_ReturnsSortedList(): void
    {
        $list = new StringList('c', 'a', 'b');

        self::assertSame(['a', 'b', 'c'], $list->sortedAlphabetically()->toArray());
    }

    public function testSortedAlphabetically_DefaultAsc_ReturnsStringListInstance(): void
    {
        $list = new StringList('b', 'a');

        self::assertInstanceOf(StringList::class, $list->sortedAlphabetically());
    }

    public function testSortedAlphabetically_DescTrue_ReturnsSortedListDescending(): void
    {
        $list = new StringList('a', 'c', 'b');

        self::assertSame(['c', 'b', 'a'], $list->sortedAlphabetically(desc: true)->toArray());
    }

    public function testSortedAlphabetically_DescTrue_ReturnsStringListInstance(): void
    {
        $list = new StringList('a', 'b');

        self::assertInstanceOf(StringList::class, $list->sortedAlphabetically(desc: true));
    }

    // endregion

    // region AbstractList: indexOf / lastIndexOf

    public function testIndexOf_ExistingElement_ReturnsIndex(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertSame(1, $list->indexOf('b'));
    }

    public function testIndexOf_NonExistingElement_ReturnsNull(): void
    {
        $list = new StringList('a', 'b');

        self::assertNull($list->indexOf('z'));
    }

    public function testLastIndexOf_DuplicateValues_ReturnsLastIndex(): void
    {
        $list = new StringList('a', 'b', 'a', 'c', 'a');

        self::assertSame(4, $list->lastIndexOf('a'));
    }

    // endregion

    // region AbstractCollection: instance checks

    public function testUnique_ReturnsStringListInstance(): void
    {
        $list = new StringList('a', 'b', 'a');

        self::assertInstanceOf(StringList::class, $list->unique());
    }

    public function testFilter_ReturnsStringListInstance(): void
    {
        $list = new StringList('a', 'bb', 'c');

        self::assertInstanceOf(StringList::class, $list->filter(static fn (string $s): bool => 1 === strlen($s)));
    }

    public function testFilterNot_ReturnsStringListInstance(): void
    {
        $list = new StringList('a', 'bb', 'c');

        self::assertInstanceOf(StringList::class, $list->filterNot(static fn (string $s): bool => strlen($s) > 1));
    }

    public function testSlice_ReturnsStringListInstance(): void
    {
        $list = new StringList('a', 'b', 'c');

        self::assertInstanceOf(StringList::class, $list->slice(0, 2));
    }

    public function testSorted_ReturnsStringListInstance(): void
    {
        $list = new StringList('b', 'a');

        self::assertInstanceOf(StringList::class, $list->sorted(static fn (string $a, string $b): int => $a <=> $b));
    }

    public function testChunks_EachChunkIsStringListInstance(): void
    {
        $list = new StringList('a', 'b');

        self::assertInstanceOf(StringList::class, $list->chunks(1)[0]);
    }

    // endregion
}
