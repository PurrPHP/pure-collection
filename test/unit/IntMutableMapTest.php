<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractMutableMap;
use Purr\Collection\Exception\InvalidArgumentTypeException;
use Purr\Collection\IntCollectionTrait;
use Purr\Collection\IntList;
use Purr\Collection\IntMutableMap;

#[CoversClass(IntMutableMap::class)]
#[CoversClass(AbstractMutableMap::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(IntCollectionTrait::class)]
class IntMutableMapTest extends TestCase
{
    // region ArrayAccess

    public function testOffsetSet_ExistingKey_rewritesValue(): void
    {
        $m = new IntMutableMap();

        $m['a'] = 1;
        $m['a'] = 2;

        self::assertSame(['a' => 2], $m->toArray());
    }

    public function testOffsetGet_ExistingKey_ReturnsValue(): void
    {
        $m = new IntMutableMap();

        $m['a'] = 1;

        self::assertSame(1, $m['a']);
    }

    public function testOffsetExists_ExistingKey_ReturnsTrue(): void
    {
        $m = new IntMutableMap();

        $m['a'] = 1;

        self::assertTrue(isset($m['a']));
    }

    public function testOffsetExists_MissingKey_ReturnsFalse(): void
    {
        $m = new IntMutableMap();

        self::assertFalse(isset($m['missing']));
    }

    public function testOffsetUnset_ExistingKey_RemovesKey(): void
    {
        $m = new IntMutableMap();

        $m['a'] = 1;
        unset($m['a']);

        self::assertSame([], $m->toArray());
    }

    public function testOffsetSet_NullKey_AppendsValue(): void
    {
        $m = new IntMutableMap();

        $m[] = 1;
        $m[] = 2;

        self::assertSame([1, 2], $m->toArray());
    }

    public function testOffsetSet_WrongType_ThrowsInvalidArgumentTypeException(): void
    {
        $m = new IntMutableMap();

        $this->expectException(InvalidArgumentTypeException::class);

        $m['a'] = 'not an int';
    }

    // endregion

    // region Mutations: abs / multiply

    public function testAbs_WithNegativeValues_MutatesInPlace(): void
    {
        $m = new IntMutableMap(...['a' => -1, 'b' => 0, 'c' => 2]);

        $m->abs();

        self::assertSame(['a' => 1, 'b' => 0, 'c' => 2], $m->toArray());
    }

    public function testAbs_ReturnsSelf(): void
    {
        $m = new IntMutableMap(...['a' => -1]);

        self::assertSame($m, $m->abs());
    }

    public function testMultiply_ByFactor_MutatesInPlace(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        $m->multiply(3);

        self::assertSame(['a' => 3, 'b' => 6, 'c' => 9], $m->toArray());
    }

    public function testMultiply_ReturnsSelf(): void
    {
        $m = new IntMutableMap(...['a' => 1]);

        self::assertSame($m, $m->multiply(2));
    }

    // endregion

    // region Mutations: filter-based

    public function testNegativeValues_MutatesInPlace(): void
    {
        $m = new IntMutableMap(...['a' => -1, 'b' => 0, 'c' => 2]);

        $m->negativeValues();

        self::assertSame(['a' => -1], $m->toArray());
    }

    public function testNotZeroValues_MutatesInPlace(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 0, 'c' => 3]);

        $m->notZeroValues();

        self::assertSame(['a' => 1, 'c' => 3], $m->toArray());
    }

    public function testPositiveValues_MutatesInPlace(): void
    {
        $m = new IntMutableMap(...['a' => -1, 'b' => 0, 'c' => 2]);

        $m->positiveValues();

        self::assertSame(['c' => 2], $m->toArray());
    }

    // endregion

    // region Mutations: sort

    public function testSortAsc_MutatesInPlace_PreservesKeys(): void
    {
        $m = new IntMutableMap(...['a' => 3, 'b' => 1, 'c' => 2]);

        $m->sortAsc();

        self::assertSame(['b' => 1, 'c' => 2, 'a' => 3], $m->toArray());
    }

    public function testSortAsc_ReturnsSelf(): void
    {
        $m = new IntMutableMap(...['a' => 2, 'b' => 1]);

        self::assertSame($m, $m->sortAsc());
    }

    public function testSortDesc_MutatesInPlace_PreservesKeys(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 3, 'c' => 2]);

        $m->sortDesc();

        self::assertSame(['b' => 3, 'c' => 2, 'a' => 1], $m->toArray());
    }

    // endregion

    // region Mutations: diff / intersect

    public function testDiff_MutatesInPlace(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        $m->diff(new IntList(2, 3));

        self::assertSame(['a' => 1], $m->toArray());
    }

    public function testDiff_ReturnsSelf(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2]);

        self::assertSame($m, $m->diff(new IntList(2)));
    }

    public function testIntersect_MutatesInPlace(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        $m->intersect(new IntList(2, 3, 5));

        self::assertSame(['b' => 2, 'c' => 3], $m->toArray());
    }

    // endregion

    // region AbstractMutableMap: clear

    public function testClear_WithElements_EmptiesCollection(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        $m->clear();

        self::assertSame([], $m->toArray());
    }

    public function testClear_WithElements_ReturnsSelf(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2]);

        self::assertSame($m, $m->clear());
    }

    // endregion

    // region AbstractMutableMap: groupBy

    public function testGroupBy_Constructed_ReturnsGroupedMap(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]);

        $result = $m->groupBy(static fn (int $i): string => 0 === $i % 2 ? 'even' : 'odd');

        self::assertSame(['odd' => ['a' => 1, 'c' => 3], 'even' => ['b' => 2, 'd' => 4]], $result);
    }

    // endregion

    // region AbstractCollection: count / isEmpty / isNotEmpty

    public function testCount_Constructed_ReturnsCount(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2]);

        self::assertSame(2, $m->count());
    }

    public function testIsEmpty_EmptyMap_ReturnsTrue(): void
    {
        $m = new IntMutableMap();

        self::assertTrue($m->isEmpty());
    }

    public function testIsNotEmpty_Constructed_ReturnsTrue(): void
    {
        $m = new IntMutableMap(...['a' => 1]);

        self::assertTrue($m->isNotEmpty());
    }

    // endregion

    // region AbstractCollection: filter / filterNot

    public function testFilter_EvenCheck_ReturnsFilteredMap(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]);

        self::assertSame(['b' => 2, 'd' => 4], $m->filter(static fn (int $n): bool => 0 === $n % 2)->toArray());
    }

    public function testFilterNot_EvenCheck_ReturnsFilteredMap(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]);

        self::assertSame(['a' => 1, 'c' => 3], $m->filterNot(static fn (int $n): bool => 0 === $n % 2)->toArray());
    }

    // endregion

    // region IntCollectionTrait: aggregate methods

    public function testMax_Constructed_ReturnsMax(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 3, 'c' => 2]);

        self::assertSame(3, $m->max());
    }

    public function testMin_Constructed_ReturnsMin(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 3, 'c' => 2]);

        self::assertSame(1, $m->min());
    }

    public function testSum_Constructed_ReturnsSum(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(6, $m->sum());
    }

    public function testAvg_Constructed_ReturnsAverage(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(2.0, $m->avg());
    }

    public function testProduct_Constructed_ReturnsProduct(): void
    {
        $m = new IntMutableMap(...['a' => 2, 'b' => 3, 'c' => 4]);

        self::assertSame(24, $m->product());
    }

    public function testRange_Constructed_ReturnsRange(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 5, 'c' => 3]);

        self::assertSame(4, $m->range());
    }

    public function testMedian_OddCount_ReturnsMiddle(): void
    {
        $m = new IntMutableMap(...['a' => 3, 'b' => 1, 'c' => 2]);

        self::assertSame(2.0, $m->median());
    }

    // endregion

    // region IntCollectionTrait: string/conversion methods

    public function testJoin_WithSeparator_ReturnsJoinedString(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame('1,2,3', $m->join(','));
    }

    public function testImplode_WithSeparator_ReturnsJoinedString(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame('1-2-3', $m->implode('-'));
    }

    public function testToStringList_Constructed_ReturnsStringList(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(['1', '2', '3'], $m->toStringList()->toArray());
    }

    public function testToStringSet_Constructed_ReturnsStringSet(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(['1', '2', '3'], $m->toStringSet()->toArray());
    }

    public function testToStringSet_WithDuplicateValues_ReturnsDeduplicatedStringSet(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 1, 'd' => 3, 'e' => 2]);

        self::assertSame(['1', '2', '3'], $m->toStringSet()->toArray());
    }

    // endregion

    // region AbstractCollection: contains / has / any / all / none

    public function testContains_ExistingElement_ReturnsTrue(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2]);

        self::assertTrue($m->contains(1));
    }

    public function testContains_NonExistingElement_ReturnsFalse(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2]);

        self::assertFalse($m->contains(99));
    }

    public function testHas_ExistingElement_ReturnsTrue(): void
    {
        $m = new IntMutableMap(...['a' => 1]);

        self::assertTrue($m->has(1));
    }

    public function testAny_MatchingPredicate_ReturnsTrue(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertTrue($m->any(static fn (int $i): bool => $i > 2));
    }

    public function testAll_AllMatch_ReturnsTrue(): void
    {
        $m = new IntMutableMap(...['a' => 2, 'b' => 4, 'c' => 6]);

        self::assertTrue($m->all(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testNone_NoneMatch_ReturnsTrue(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 3, 'c' => 5]);

        self::assertTrue($m->none(static fn (int $i): bool => 0 === $i % 2));
    }

    // endregion

    // region AbstractCollection: unique

    public function testUnique_WithDuplicates_ReturnsUniqueMap(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 1]);

        self::assertSame(['a' => 1, 'b' => 2], $m->unique()->toArray());
    }

    // endregion

    // region AbstractCollection: find methods

    public function testFindFirst_NoArgs_ReturnsFirstElement(): void
    {
        $m = new IntMutableMap(...['a' => 10, 'b' => 20]);

        self::assertSame(10, $m->findFirst());
    }

    public function testFindFirst_WithPredicate_ReturnsMatchingElement(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 4, 'c' => 2]);

        self::assertSame(4, $m->findFirst(static fn (int $i): bool => 0 === $i % 2));
    }

    public function testFindLast_NoArgs_ReturnsLastElement(): void
    {
        $m = new IntMutableMap(...['a' => 10, 'b' => 20]);

        self::assertSame(20, $m->findLast());
    }

    public function testFindLast_WithPredicate_ReturnsLastMatchingElement(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 4, 'c' => 2]);

        self::assertSame(2, $m->findLast(static fn (int $i): bool => 0 === $i % 2));
    }

    // endregion

    // region AbstractCollection: map / reduce

    public function testMap_Constructed_ReturnsArray(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2]);

        self::assertSame(['a' => 2, 'b' => 3], $m->map(static fn (int $i): int => $i + 1));
    }

    public function testReduce_Constructed_ReturnsSumViaReduce(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(6, $m->reduce(static fn (int $carry, int $item): int => $carry + $item, 0));
    }

    // endregion

    // region AbstractCollection: slice / sorted

    public function testSlice_Constructed_ReturnsSlicedMap(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame(['b' => 2, 'c' => 3], $m->slice(1, 2)->toArray());
    }

    public function testSorted_DescComparator_ReturnsSortedDescMap(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 3, 'c' => 2]);

        self::assertSame(['b' => 3, 'c' => 2, 'a' => 1], $m->sorted(static fn (int $i, int $j): int => $j <=> $i)->toArray());
    }

    // endregion

    // region AbstractCollection: chunks

    public function testChunks_Constructed_ReturnsIntMutableMapChunks(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2, 'c' => 3]);

        $result = $m->chunks(2);

        self::assertCount(2, $result);
        self::assertSame(['a' => 1, 'b' => 2], $result[0]->toArray());
        self::assertSame(['c' => 3], $result[1]->toArray());
    }

    public function testChunks_EachChunkIsIntMutableMapInstance(): void
    {
        $m = new IntMutableMap(...['a' => 1, 'b' => 2]);

        $result = $m->chunks(1);

        self::assertInstanceOf(IntMutableMap::class, $result[0]);
    }

    // endregion

    // region AbstractCollection: iterator

    public function testIterate_Constructed_ReturnsAllElements(): void
    {
        $m = new IntMutableMap(...['a' => 10, 'b' => 20]);

        self::assertSame(['a' => 10, 'b' => 20], [...$m]);
    }

    // endregion

    // region IntCollectionTrait: fromString

    public function testFromString_ValidString_ReturnsIntMutableMapInstance(): void
    {
        $m = IntMutableMap::fromString('1,2,3', ',');

        self::assertInstanceOf(IntMutableMap::class, $m);
        self::assertSame([1, 2, 3], $m->toArray());
    }

    // endregion
}
