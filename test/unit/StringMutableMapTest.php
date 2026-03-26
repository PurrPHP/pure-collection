<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractMutableMap;
use Purr\Collection\Exception\InvalidArgumentTypeException;
use Purr\Collection\StringCollectionTrait;
use Purr\Collection\StringList;
use Purr\Collection\StringMutableMap;

#[CoversClass(StringMutableMap::class)]
#[CoversClass(AbstractMutableMap::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(StringCollectionTrait::class)]
class StringMutableMapTest extends TestCase
{
    // region ArrayAccess

    public function testOffsetSet_ExistingKey_rewritesValue(): void
    {
        $m = new StringMutableMap();

        $m['a'] = 'foo';
        $m['a'] = 'bar';

        self::assertSame(['a' => 'bar'], $m->toArray());
    }

    public function testOffsetGet_ExistingKey_ReturnsValue(): void
    {
        $m = new StringMutableMap();

        $m['a'] = 'foo';

        self::assertSame('foo', $m['a']);
    }

    public function testOffsetExists_ExistingKey_ReturnsTrue(): void
    {
        $m = new StringMutableMap();

        $m['a'] = 'foo';

        self::assertTrue(isset($m['a']));
    }

    public function testOffsetExists_MissingKey_ReturnsFalse(): void
    {
        $m = new StringMutableMap();

        self::assertFalse(isset($m['missing']));
    }

    public function testOffsetUnset_ExistingKey_RemovesKey(): void
    {
        $m = new StringMutableMap();

        $m['a'] = 'foo';
        unset($m['a']);

        self::assertSame([], $m->toArray());
    }

    public function testOffsetSet_NullKey_AppendsValue(): void
    {
        $m = new StringMutableMap();

        $m[] = 'foo';
        $m[] = 'bar';

        self::assertSame(['foo', 'bar'], $m->toArray());
    }

    public function testOffsetSet_WrongType_ThrowsInvalidArgumentTypeException(): void
    {
        $m = new StringMutableMap();

        $this->expectException(InvalidArgumentTypeException::class);

        $m['a'] = 123; // @phpstan-ignore offsetAssign.valueType
    }

    // endregion

    // region Mutations: sortAlphabetically

    public function testSortAlphabetically_MutatesInPlace_PreservesKeys(): void
    {
        $m = new StringMutableMap(...['a' => 'zebra', 'b' => 'apple', 'c' => 'banana']);

        $m->sortAlphabetically();

        self::assertSame(['b' => 'apple', 'c' => 'banana', 'a' => 'zebra'], $m->toArray());
    }

    public function testSortAlphabetically_ReturnsSelf(): void
    {
        $m = new StringMutableMap(...['a' => 'zebra', 'b' => 'apple']);

        self::assertSame($m, $m->sortAlphabetically());
    }

    // endregion

    // region Mutations: diff / intersect

    public function testDiff_MutatesInPlace(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        $m->diff(new StringList('bar', 'baz'));

        self::assertSame(['a' => 'foo'], $m->toArray());
    }

    public function testDiff_ReturnsSelf(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar']);

        self::assertSame($m, $m->diff(new StringList('bar')));
    }

    public function testIntersect_MutatesInPlace(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        $m->intersect(new StringList('bar', 'baz', 'qux'));

        self::assertSame(['b' => 'bar', 'c' => 'baz'], $m->toArray());
    }

    // endregion

    // region AbstractMutableMap: clear

    public function testClear_WithElements_EmptiesCollection(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        $m->clear();

        self::assertSame([], $m->toArray());
    }

    public function testClear_WithElements_ReturnsSelf(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar']);

        self::assertSame($m, $m->clear());
    }

    // endregion

    // region AbstractMutableMap: groupBy

    public function testGroupBy_Constructed_ReturnsGroupedMap(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'barn', 'c' => 'baz', 'd' => 'qux']);

        $result = $m->groupBy(static fn (string $s): string => (string) strlen($s));

        self::assertSame(['3' => ['a' => 'foo', 'c' => 'baz', 'd' => 'qux'], '4' => ['b' => 'barn']], $result);
    }

    // endregion

    // region AbstractCollection: count / isEmpty / isNotEmpty

    public function testCount_Constructed_ReturnsCount(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar']);

        self::assertSame(2, $m->count());
    }

    public function testIsEmpty_EmptyMap_ReturnsTrue(): void
    {
        $m = new StringMutableMap();

        self::assertTrue($m->isEmpty());
    }

    public function testIsNotEmpty_Constructed_ReturnsTrue(): void
    {
        $m = new StringMutableMap(...['a' => 'foo']);

        self::assertTrue($m->isNotEmpty());
    }

    // endregion

    // region AbstractCollection: filter / filterNot

    public function testFilter_LengthCheck_ReturnsFilteredMap(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'barn', 'c' => 'baz', 'd' => 'qux']);

        self::assertSame(['a' => 'foo', 'c' => 'baz', 'd' => 'qux'], $m->filter(static fn (string $s): bool => 3 === strlen($s))->toArray());
    }

    public function testFilterNot_LengthCheck_ReturnsFilteredMap(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'barn', 'c' => 'baz', 'd' => 'qux']);

        self::assertSame(['b' => 'barn'], $m->filterNot(static fn (string $s): bool => 3 === strlen($s))->toArray());
    }

    // endregion

    // region StringCollectionTrait: join / implode

    public function testJoin_WithSeparator_ReturnsJoinedString(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame('foo,bar,baz', $m->join(','));
    }

    public function testImplode_WithSeparator_ReturnsJoinedString(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame('foo-bar-baz', $m->implode('-'));
    }

    // endregion

    // region AbstractCollection: contains / has / any / all / none

    public function testContains_ExistingElement_ReturnsTrue(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar']);

        self::assertTrue($m->contains('foo'));
    }

    public function testContains_NonExistingElement_ReturnsFalse(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar']);

        self::assertFalse($m->contains('baz'));
    }

    public function testHas_ExistingElement_ReturnsTrue(): void
    {
        $m = new StringMutableMap(...['a' => 'foo']);

        self::assertTrue($m->has('foo'));
    }

    public function testAny_MatchingPredicate_ReturnsTrue(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertTrue($m->any(static fn (string $s): bool => strlen($s) > 2));
    }

    public function testAll_AllMatch_ReturnsTrue(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertTrue($m->all(static fn (string $s): bool => 3 === strlen($s)));
    }

    public function testNone_NoneMatch_ReturnsTrue(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertTrue($m->none(static fn (string $s): bool => strlen($s) > 5));
    }

    // endregion

    // region AbstractCollection: unique

    public function testUnique_WithDuplicates_ReturnsUniqueMap(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'foo']);

        self::assertSame(['a' => 'foo', 'b' => 'bar'], $m->unique()->toArray());
    }

    // endregion

    // region AbstractCollection: find methods

    public function testFindFirst_NoArgs_ReturnsFirstElement(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar']);

        self::assertSame('foo', $m->findFirst());
    }

    public function testFindFirst_WithPredicate_ReturnsMatchingElement(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'barn', 'c' => 'baz']);

        self::assertSame('barn', $m->findFirst(static fn (string $s): bool => 4 === strlen($s)));
    }

    public function testFindLast_NoArgs_ReturnsLastElement(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar']);

        self::assertSame('bar', $m->findLast());
    }

    public function testFindLast_WithPredicate_ReturnsLastMatchingElement(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'barn', 'c' => 'baz']);

        self::assertSame('baz', $m->findLast(static fn (string $s): bool => 3 === strlen($s)));
    }

    // endregion

    // region AbstractCollection: map / reduce

    public function testMap_Constructed_ReturnsArray(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar']);

        self::assertSame(['a' => 'FOO', 'b' => 'BAR'], $m->map(static fn (string $s): string => strtoupper($s)));
    }

    public function testReduce_Constructed_ReturnsConcatenatedViaReduce(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame('foobarbaz', $m->reduce(static fn (string $carry, string $item): string => $carry.$item, ''));
    }

    // endregion

    // region AbstractCollection: slice / sorted

    public function testSlice_Constructed_ReturnsSlicedMap(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame(['b' => 'bar', 'c' => 'baz'], $m->slice(1, 2)->toArray());
    }

    public function testSorted_DescComparator_ReturnsSortedDescMap(): void
    {
        $m = new StringMutableMap(...['a' => 'apple', 'b' => 'zebra', 'c' => 'banana']);

        self::assertSame(['b' => 'zebra', 'c' => 'banana', 'a' => 'apple'], $m->sorted(static fn (string $i, string $j): int => $j <=> $i)->toArray());
    }

    // endregion

    // region AbstractCollection: chunks

    public function testChunks_Constructed_ReturnsStringMutableMapChunks(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        $result = $m->chunks(2);

        self::assertCount(2, $result);
        self::assertSame(['a' => 'foo', 'b' => 'bar'], $result[0]->toArray());
        self::assertSame(['c' => 'baz'], $result[1]->toArray());
    }

    public function testChunks_EachChunkIsStringMutableMapInstance(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar']);

        $result = $m->chunks(1);

        self::assertInstanceOf(StringMutableMap::class, $result[0]);
    }

    // endregion

    // region AbstractCollection: iterator

    public function testIterate_Constructed_ReturnsAllElements(): void
    {
        $m = new StringMutableMap(...['a' => 'foo', 'b' => 'bar']);

        self::assertSame(['a' => 'foo', 'b' => 'bar'], [...$m]);
    }

    // endregion

    // region StringCollectionTrait: fromInts

    public function testFromInts_ValidInts_ReturnsStringMutableMapInstance(): void
    {
        $m = StringMutableMap::fromInts(1, 2, 3);

        self::assertInstanceOf(StringMutableMap::class, $m);
        self::assertSame(['1', '2', '3'], $m->toArray());
    }

    // endregion

    // region StringCollectionTrait: sortedAlphabetically

    public function testSortedAlphabeticallySort_Asc_ReturnsSortedMap(): void
    {
        $m = new StringMutableMap(...['c' => 'zebra', 'a' => 'apple', 'b' => 'banana']);

        self::assertSame(['a' => 'apple', 'b' => 'banana', 'c' => 'zebra'], $m->sortedAlphabetically()->toArray());
    }

    public function testSortedAlphabetically_Desc_ReturnsSortedMapDescending(): void
    {
        $m = new StringMutableMap(...['c' => 'zebra', 'a' => 'apple', 'b' => 'banana']);

        self::assertSame(['c' => 'zebra', 'b' => 'banana', 'a' => 'apple'], $m->sortedAlphabetically(desc: true)->toArray());
    }

    // endregion
}
