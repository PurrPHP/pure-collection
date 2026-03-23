<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Purr\Collection\IntList;

#[CoversClass(IntList::class)]
class IntListTest extends TestCase
{
    public function testConstructorMapProvidedReturnsTargetList(): void
    {
        $list = new IntList(...[1, 2]);

        self::assertSame([1, 2], $list->toArray());
    }

    #[DataProvider('providerFindFirst')]
    public function testFindFirstConstructedReturnsTargetValue(
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
    public function testFindFirstAfterConstructedReturnsTargetValue(
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
    public function testFindLastConstructedReturnsTargetValue(?int $result, array $source): void
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
    public function testContainsConstructedReturnsTargetValue(bool $result, int $needle, array $source): void
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

    public function testFilterConstructedReturnsTargetList(): void
    {
        $list = new IntList(1, 2, 3, 4, 5);

        $filter1 = fn (int $n): bool => 2 !== $n;
        $filter2 = fn (int $n): bool => 4 !== $n;

        self::assertSame([1, 3, 5], $list->filter($filter1, $filter2)->toArray());
    }

    public function testFilterOneItemAfterFilterReturnsTargetList(): void
    {
        $list = new IntList(1, 2);

        $filter1 = fn (int $n): bool => 2 !== $n;

        self::assertSame([1], $list->filter($filter1)->toArray());
    }

    public function testFilterFilterRemovesAllReturnsEmptyList(): void
    {
        $list = new IntList(1);

        $filter1 = fn (int $n): bool => 1 !== $n;

        self::assertSame([], $list->filter($filter1)->toArray());
    }

    public function testFilterNotConstructedReturnsTargetList(): void
    {
        $list = new IntList(1, 2, 3, 4, 5);

        $filter1 = fn (int $n): bool => 2 === $n;
        $filter2 = fn (int $n): bool => 4 === $n;

        self::assertSame([1, 3, 5], $list->filterNot($filter1, $filter2)->toArray());
    }

    public function testFilterNotOneItemAfterFilterReturnsTargetList(): void
    {
        $list = new IntList(1, 2);

        $filter1 = fn (int $n): bool => 2 === $n;

        self::assertSame([1], $list->filterNot($filter1)->toArray());
    }

    public function testFilterNotFilterRemovesAllReturnsEmptyList(): void
    {
        $list = new IntList(1);

        $filter1 = fn (int $n): bool => 1 === $n;

        self::assertSame([], $list->filterNot($filter1)->toArray());
    }

    public function testMapConstructedReturnsTargetMap(): void
    {
        $list = new IntList(1, 2);

        self::assertSame([2, 3], $list->map(fn (int $i): int => $i + 1));
    }

    public function testGroupByConstructedReturnsTargetMap(): void
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
    public function testSortedAscToDescReturnsTargetList(array $result, array $source): void
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
    public function testSliceConstructedReturnsTargetValue(
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

    public function testToArrayConstructedReturnsGivenList(): void
    {
        $list = new IntList(1, 2, 3);

        self::assertSame([1, 2, 3], $list->toArray());
    }

    #[DataProvider('providerUnique')]
    public function testUniqueSomeValuesReturnsTargetList(array $result, array $source): void
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

    public function testCountTwoElementsReturnsTwo(): void
    {
        $list = new IntList(1, 2);

        self::assertSame(2, $list->count());
    }

    public function testIsEmptyEmptyReturnsTrue(): void
    {
        $list = new IntList();

        self::assertTrue($list->isEmpty());
    }

    public function testIsEmptyNotEmptyReturnsFalse(): void
    {
        $list = new IntList(1);

        self::assertFalse($list->isEmpty());
    }

    public function testIsNotEmptyNotEmptyReturnsTrue(): void
    {
        $list = new IntList(1);

        self::assertTrue($list->isNotEmpty());
    }

    public function testIsNotEmptyEmptyReturnsFalse(): void
    {
        $list = new IntList();

        self::assertFalse($list->isNotEmpty());
    }

    public function testToStringSetConstructedReturnsTargetList(): void
    {
        $list = new IntList(1, 2, 3, 4);

        self::assertSame(['1', '2', '3', '4'], $list->toStringSet()->toArray());
    }

    public function testChunkConstructedReturnsTargetList(): void
    {
        $list = new IntList(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);

        $result = $list->chunks(3);

        self::assertSame(
            [[1, 2, 3], [4, 5, 6], [7, 8, 9], [10]],
            array_map(fn (IntList $list) => $list->toArray(), $result)
        );
    }

    public function testChunksSingleElementReturnsIntListChunk(): void
    {
        $list = new IntList(1);

        $result = $list->chunks(1);

        self::assertInstanceOf(IntList::class, $result[0]);
    }

    public function testDestructSomeListReturnsSourceValues(): void
    {
        $list = new IntList(1, 2, 3);

        self::assertSame([1, 2, 3], [...$list]);
    }

    public function testMaxConstructedReturnsMax(): void
    {
        $list = new IntList(1, 2, 3, 4, 2, 3);

        self::assertSame(4, $list->max());
    }

    public function testMinConstructedReturnsMin(): void
    {
        $list = new IntList(2, 3, 4, 2, 1);

        self::assertSame(1, $list->min());
    }
}
