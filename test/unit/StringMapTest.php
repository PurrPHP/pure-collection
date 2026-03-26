<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractMap;
use Purr\Collection\StringCollectionTrait;
use Purr\Collection\StringList;
use Purr\Collection\StringMap;

#[CoversClass(StringMap::class)]
#[CoversClass(AbstractMap::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(StringCollectionTrait::class)]
final class StringMapTest extends TestCase
{
    // region Constructor

    public function testConstructor_ListProvided_ReturnsTargetMap(): void
    {
        $map = new StringMap('a', 'b');

        self::assertSame(['a', 'b'], $map->toArray());
    }

    // endregion

    // region StringCollectionTrait: fromInts

    public function testFromInts_ValidInts_ReturnsStringMapInstance(): void
    {
        $map = StringMap::fromInts(1, 2, 3);

        self::assertInstanceOf(StringMap::class, $map);
        self::assertSame(['1', '2', '3'], $map->toArray());
    }

    // endregion

    // region StringCollectionTrait: sortedAlphabetically

    public function testSortedAlphabetically_DefaultAsc_ReturnsSortedMapByValue(): void
    {
        $map = new StringMap(...['c' => 'zebra', 'a' => 'apple', 'b' => 'banana']);

        self::assertSame(['a' => 'apple', 'b' => 'banana', 'c' => 'zebra'], $map->sortedAlphabetically()->toArray());
    }

    public function testSortedAlphabetically_DescTrue_ReturnsSortedMapByValueDescending(): void
    {
        $map = new StringMap(...['c' => 'zebra', 'a' => 'apple', 'b' => 'banana']);

        self::assertSame(['c' => 'zebra', 'b' => 'banana', 'a' => 'apple'], $map->sortedAlphabetically(desc: true)->toArray());
    }

    // endregion

    // region StringCollectionTrait: join / implode

    public function testJoin_WithSeparator_ReturnsJoinedString(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame('foo,bar,baz', $map->join(','));
    }

    public function testImplode_WithSeparator_ReturnsJoinedString(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame('foo-bar-baz', $map->implode('-'));
    }

    // endregion

    // region StringCollectionTrait: diff / intersect

    public function testDiff_WithAnotherCollection_ReturnsDifference(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame(['a' => 'foo'], $map->diff(new StringList('bar', 'baz'))->toArray());
    }

    public function testIntersect_WithAnotherCollection_ReturnsIntersection(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame(['b' => 'bar', 'c' => 'baz'], $map->intersect(new StringList('bar', 'baz', 'qux'))->toArray());
    }

    // endregion

    // region AbstractCollection: find methods

    #[DataProvider('providerFindFirst')]
    public function testFindFirst_WithOptionalPredicate_ReturnsTargetValue(
        ?string $result,
        ?callable $predicate,
        array $source
    ): void {
        $map = new StringMap(...$source);

        self::assertSame($result, $map->findFirst($predicate));
    }

    public static function providerFindFirst(): array
    {
        return [
            'empty' => [null, null, []],
            'empty predicate' => [null, static fn (string $s): bool => str_starts_with($s, 'a'), []],
            'foo' => ['foo', null, ['a' => 'foo']],
            'foo,bar' => ['foo', null, ['a' => 'foo', 'b' => 'bar']],
            'first starting with b' => ['bar', static fn (string $s): bool => str_starts_with($s, 'b'), ['a' => 'foo', 'b' => 'bar', 'c' => 'baz']],
        ];
    }

    #[DataProvider('providerFindFirstAfter')]
    public function testFindFirstAfter_WithNeedle_ReturnsTargetValue(
        ?string $result,
        string $needle,
        array $source
    ): void {
        $map = new StringMap(...$source);

        self::assertSame($result, $map->findFirstAfter($needle));
    }

    public static function providerFindFirstAfter(): array
    {
        return [
            'empty' => [null, 'foo', []],
            'foo' => [null, 'foo', ['a' => 'foo']],
            'foo,bar - needle foo' => ['bar', 'foo', ['a' => 'foo', 'b' => 'bar']],
            'foo,bar - needle bar' => [null, 'bar', ['a' => 'foo', 'b' => 'bar']],
        ];
    }

    #[DataProvider('providerFindLast')]
    public function testFindLast_WithOptionalPredicate_ReturnsTargetValue(?string $result, ?callable $predicate, array $source): void
    {
        $map = new StringMap(...$source);

        self::assertSame($result, $map->findLast($predicate));
    }

    public static function providerFindLast(): array
    {
        return [
            'empty' => [null, null, []],
            'empty predicate' => [null, static fn (string $s): bool => str_starts_with($s, 'a'), []],
            'foo' => ['foo', null, ['a' => 'foo']],
            'foo,bar' => ['bar', null, ['a' => 'foo', 'b' => 'bar']],
            'last starting with b' => ['baz', static fn (string $s): bool => str_starts_with($s, 'b'), ['a' => 'foo', 'b' => 'bar', 'c' => 'baz']],
        ];
    }

    // endregion

    // region AbstractCollection: contains / has / any / all / none

    #[DataProvider('providerContains')]
    public function testContains_WithNeedle_ReturnsTargetBool(bool $result, string $needle, array $source): void
    {
        $map = new StringMap(...$source);

        self::assertSame($result, $map->contains($needle));
    }

    public static function providerContains(): array
    {
        return [
            'empty' => [false, 'foo', []],
            'contains foo' => [true, 'foo', ['a' => 'foo']],
            'contains bar' => [true, 'bar', ['a' => 'foo', 'b' => 'bar']],
            'not' => [false, 'baz', ['a' => 'foo', 'b' => 'bar']],
        ];
    }

    public function testHas_ExistingElement_ReturnsTrue(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar']);

        self::assertTrue($map->has('foo'));
    }

    public function testAny_MatchingPredicate_ReturnsTrue(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertTrue($map->any(static fn (string $s): bool => strlen($s) > 2));
    }

    public function testAny_NoMatch_ReturnsFalse(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertFalse($map->any(static fn (string $s): bool => strlen($s) > 10));
    }

    public function testAll_AllMatch_ReturnsTrue(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertTrue($map->all(static fn (string $s): bool => 3 === strlen($s)));
    }

    public function testAll_NotAllMatch_ReturnsFalse(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'barn', 'c' => 'baz']);

        self::assertFalse($map->all(static fn (string $s): bool => 3 === strlen($s)));
    }

    public function testNone_NoneMatch_ReturnsTrue(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertTrue($map->none(static fn (string $s): bool => strlen($s) > 5));
    }

    public function testNone_SomeMatch_ReturnsFalse(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'barn', 'c' => 'baz']);

        self::assertFalse($map->none(static fn (string $s): bool => 4 === strlen($s)));
    }

    // endregion

    // region AbstractCollection: filter / filterNot

    public function testFilter_MultipleFilters_ReturnsFilteredMap(): void
    {
        $map = new StringMap(
            ...['a' => 'foo', 'b' => 'bar', 'c' => 'baz', 'd' => 'qux', 'e' => 'quux']
        );

        $filter1 = fn (string $s): bool => 'bar' !== $s;
        $filter2 = fn (string $s): bool => 'qux' !== $s;

        self::assertSame([
            'a' => 'foo',
            'c' => 'baz',
            'e' => 'quux',
        ], $map->filter($filter1, $filter2)->toArray());
    }

    public function testFilter_OneItemRemains_ReturnsOneItemMap(): void
    {
        $map = new StringMap(
            ...['a' => 'foo', 'b' => 'bar']
        );

        $filter1 = fn (string $s): bool => 'bar' !== $s;

        self::assertSame(['a' => 'foo'], $map->filter($filter1)->toArray());
    }

    public function testFilter_AllRemoved_ReturnsEmptyMap(): void
    {
        $map = new StringMap(
            ...['a' => 'foo']
        );

        $filter1 = fn (string $s): bool => 'foo' !== $s;

        self::assertSame([], $map->filter($filter1)->toArray());
    }

    public function testFilterNot_MultipleFilters_ReturnsFilteredMap(): void
    {
        $map = new StringMap(
            ...['a' => 'foo', 'b' => 'bar', 'c' => 'baz', 'd' => 'qux', 'e' => 'quux']
        );

        $filter1 = fn (string $s): bool => 'bar' === $s;
        $filter2 = fn (string $s): bool => 'qux' === $s;

        self::assertSame([
            'a' => 'foo',
            'c' => 'baz',
            'e' => 'quux',
        ], $map->filterNot($filter1, $filter2)->toArray());
    }

    public function testFilterNot_OneItemRemains_ReturnsOneItemMap(): void
    {
        $map = new StringMap(
            ...['a' => 'foo', 'b' => 'bar']
        );

        $filter1 = fn (string $s): bool => 'bar' === $s;

        self::assertSame(['a' => 'foo'], $map->filterNot($filter1)->toArray());
    }

    public function testFilterNot_AllRemoved_ReturnsEmptyMap(): void
    {
        $map = new StringMap(
            ...['a' => 'foo']
        );

        $filter1 = fn (string $s): bool => 'foo' === $s;

        self::assertSame([], $map->filterNot($filter1)->toArray());
    }

    // endregion

    // region AbstractCollection: count / isEmpty / isNotEmpty

    public function testCount_Constructed_ReturnsCount(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        self::assertSame(3, $map->count());
    }

    public function testIsEmpty_EmptyMap_ReturnsTrue(): void
    {
        $map = new StringMap();

        self::assertTrue($map->isEmpty());
    }

    public function testIsEmpty_Constructed_ReturnsFalse(): void
    {
        $map = new StringMap(...['a' => 'foo']);

        self::assertFalse($map->isEmpty());
    }

    public function testIsNotEmpty_Constructed_ReturnsTrue(): void
    {
        $map = new StringMap(...['a' => 'foo']);

        self::assertTrue($map->isNotEmpty());
    }

    // endregion

    // region AbstractCollection: groupBy / flattenGroupBy

    public function testGroupBy_Constructed_ReturnsGroupedMap(): void
    {
        $map = new StringMap(
            ...['a' => 'foo', 'b' => 'bar', 'c' => 'baz', 'd' => 'qux']
        );

        self::assertSame([
            '3' => [
                'a' => 'foo',
                'b' => 'bar',
                'c' => 'baz',
                'd' => 'qux',
            ],
        ], $map->groupBy(fn (string $s): string => (string) strlen($s)));
    }

    public function testFlattenGroupBy_Constructed_ReturnsGroupedArray(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'barn', 'c' => 'baz']);

        $result = $map->flattenGroupBy(static fn (string $s): string => (string) strlen($s));

        self::assertSame(['3' => 'baz', '4' => 'barn'], $result);
    }

    // endregion

    // region AbstractCollection: map / reduce

    public function testMap_Constructed_ReturnsTargetArray(): void
    {
        $map = new StringMap(
            ...['a' => 'foo', 'b' => 'bar']
        );

        self::assertSame([
            'a' => 'FOO',
            'b' => 'BAR',
        ], $map->map(fn (string $s): string => strtoupper($s)));

        self::assertSame(['a' => 'foo', 'b' => 'bar'], $map->toArray(), 'no mutation of source');
    }

    public function testReduce_Constructed_ReturnsConcatenatedViaReduce(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz', 'd' => 'qux']);

        $result = $map->reduce(static fn (string $carry, string $item): string => $carry.$item, '');

        self::assertSame('foobarbazqux', $result);
    }

    // endregion

    // region AbstractCollection: unique

    #[DataProvider('providerUnique')]
    public function testUnique_SomeValues_ReturnsUniqueMap(array $result, array $source): void
    {
        $map = new StringMap(...$source);

        self::assertSame($result, $map->unique()->toArray());
    }

    public static function providerUnique(): array
    {
        return [
            'empty' => [[], []],
            'single value' => [['a' => 'foo'], ['a' => 'foo', 'b' => 'foo']],
            'two uniq values' => [['a' => 'foo', 'b' => 'bar'], ['a' => 'foo', 'b' => 'bar', 'c' => 'foo', 'd' => 'bar']],
        ];
    }

    // endregion

    // region AbstractCollection: sorted / slice

    #[DataProvider('providerSorted')]
    public function testSorted_DescOrder_ReturnsDescMap(array $result, array $source): void
    {
        $map = new StringMap(...$source);

        self::assertSame($result, $map->sorted(fn (string $i, string $j): int => $j <=> $i)->toArray());
    }

    public static function providerSorted(): array
    {
        return [
            'empty' => [[], []],
            'single value' => [['a' => 'foo'], ['a' => 'foo']],
            'three values' => [['c' => 'baz', 'b' => 'bar', 'a' => 'apple'], ['a' => 'apple', 'b' => 'bar', 'c' => 'baz']],
        ];
    }

    #[DataProvider('providerSlice')]
    public function testSlice_WithOffsetAndLimit_ReturnsTargetMap(
        array $result,
        array $source,
        int $offset,
        int $limit
    ): void {
        $map = new StringMap(...$source);

        self::assertSame($result, $map->slice($offset, $limit)->toArray());
    }

    public static function providerSlice(): array
    {
        return [
            'empty' => [[], [], 1, 1],
            'foo' => [['a' => 'foo'], ['a' => 'foo'], 0, 1],
            'foo, bar, request 1' => [['b' => 'bar'], ['a' => 'foo', 'b' => 'bar'], 1, 1],
            'foo,bar,baz request out of range' => [['b' => 'bar', 'c' => 'baz'], ['a' => 'foo', 'b' => 'bar', 'c' => 'baz'], 1, 3],
        ];
    }

    // endregion

    // region AbstractCollection: chunks

    public function testChunks_Constructed_ReturnsStringMapChunks(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar', 'c' => 'baz']);

        $result = $map->chunks(2);

        self::assertCount(2, $result);
        self::assertSame(['a' => 'foo', 'b' => 'bar'], $result[0]->toArray());
        self::assertSame(['c' => 'baz'], $result[1]->toArray());
    }

    public function testChunks_EachChunkIsStringMapInstance(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar']);

        $result = $map->chunks(1);

        self::assertInstanceOf(StringMap::class, $result[0]);
    }

    // endregion

    // region AbstractCollection: toArray

    public function testToArray_Constructed_ReturnsGivenMap(): void
    {
        $map = new StringMap(
            ...['a' => 'foo', 'b' => 'bar']
        );

        self::assertSame([
            'a' => 'foo',
            'b' => 'bar',
        ], $map->toArray());
    }

    // endregion

    // region AbstractCollection: iterator

    public function testIterate_Constructed_ReturnsAllElements(): void
    {
        $map = new StringMap(...['a' => 'foo', 'b' => 'bar']);

        self::assertSame(['a' => 'foo', 'b' => 'bar'], [...$map]);
    }

    // endregion
}
