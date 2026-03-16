<?php
declare(strict_types=1);
namespace Purr\Collection\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Purr\Collection\IntList;

#[CoversClass(IntList::class)]
class IntListTest extends TestCase
{
    public function testConstructor_MapProvided_ReturnsTargetList(): void
    {
        $list = new IntList(...[1, 2]);

        self::assertSame([1, 2], $list->toArray());
    }

    #[DataProvider('providerFindFirst')]
    public function testFindFirst_Constructed_ReturnsTargetValue(
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
            'empty predicate' => [null, static fn(int $i): bool => $i % 2 === 0, []],
            '1' => [1, null, [1]],
            '1,2' => [1, null, [1, 2]],
            'first even' => [4, static fn(int $i): bool => $i % 2 === 0, [1, 4, 2]],
        ];
    }

    #[DataProvider('providerFindFirstAfter')]
    public function testFindFirstAfter_Constructed_ReturnsTargetValue(
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
    public function testFindLast_Constructed_ReturnsTargetValue(?int $result, array $source): void
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
    public function testContains_Constructed_ReturnsTargetValue(bool $result, int $needle, array $source): void
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

    public function testFilter_Constructed_ReturnsTargetList(): void
    {
        $list = new IntList(1, 2, 3, 4, 5);

        $filter1 = fn(int $n): bool => $n !== 2;
        $filter2 = fn(int $n): bool => $n !== 4;

        self::assertSame([1, 3, 5], $list->filter($filter1, $filter2)->toArray());
    }

    public function testFilter_OneItemAfterFilter_ReturnsTargetList(): void
    {
        $list = new IntList(1, 2);

        $filter1 = fn(int $n): bool => $n !== 2;

        self::assertSame([1], $list->filter($filter1)->toArray());
    }

    public function testFilter_FilterRemovesAll_ReturnsEmptyList(): void
    {
        $list = new IntList(1);

        $filter1 = fn(int $n): bool => $n !== 1;

        self::assertSame([], $list->filter($filter1)->toArray());
    }

    public function testFilterNot_Constructed_ReturnsTargetList(): void
    {
        $list = new IntList(1, 2, 3, 4, 5);

        $filter1 = fn(int $n): bool => $n === 2;
        $filter2 = fn(int $n): bool => $n === 4;

        self::assertSame([1, 3, 5], $list->filterNot($filter1, $filter2)->toArray());
    }

    public function testFilterNot_OneItemAfterFilter_ReturnsTargetList(): void
    {
        $list = new IntList(1, 2);

        $filter1 = fn(int $n): bool => $n === 2;

        self::assertSame([1], $list->filterNot($filter1)->toArray());
    }

    public function testFilterNot_FilterRemovesAll_ReturnsEmptyList(): void
    {
        $list = new IntList(1);

        $filter1 = fn(int $n): bool => $n === 1;

        self::assertSame([], $list->filterNot($filter1)->toArray());
    }

    public function testMap_Constructed_ReturnsTargetMap(): void
    {
        $list = new IntList(1, 2);

        self::assertSame([2, 3], $list->map(fn(int $i): int => $i + 1));
    }

    public function testGroupBy_Constructed_ReturnsTargetMap(): void
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
        ], $list->groupBy(fn(int $i): string => $i % 2 === 0 ? 'even' : 'odd'));
    }

    #[DataProvider('providerSorted')]
    public function testSorted_AscToDesc_ReturnsTargetList(array $result, array $source): void
    {
        $list = new IntList(...$source);

        self::assertSame($result, $list->sorted(fn(int $i, int $j): int => $j <=> $i)->toArray());
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
    public function testSlice_Constructed_ReturnsTargetValue(
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

    public function testToArray_Constructed_ReturnsGivenList(): void
    {
        $list = new IntList(1, 2, 3);

        self::assertSame([1, 2, 3], $list->toArray());
    }

    #[DataProvider('providerUnique')]
    public function testUnique_SomeValues_ReturnsTargetList(array $result, array $source): void
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

    public function testCount_TwoElements_ReturnsTwo(): void
    {
        $list = new IntList(1, 2);

        self::assertSame(2, $list->count());
    }

    public function testIsEmpty_Empty_ReturnsTrue(): void
    {
        $list = new IntList();

        self::assertTrue($list->isEmpty());
    }

    public function testIsEmpty_NotEmpty_ReturnsFalse(): void
    {
        $list = new IntList(1);

        self::assertFalse($list->isEmpty());
    }

    public function testIsNotEmpty_NotEmpty_ReturnsTrue(): void
    {
        $list = new IntList(1);

        self::assertTrue($list->isNotEmpty());
    }

    public function testIsNotEmpty_Empty_ReturnsFalse(): void
    {
        $list = new IntList();

        self::assertFalse($list->isNotEmpty());
    }

    public function testToStringUniqueList_Constructed_ReturnsTargetList(): void
    {
        $list = new IntList(1, 2, 3, 4);

        self::assertSame(['1', '2', '3', '4'], $list->toStringUniqueList()->toArray());
    }

    public function testChunk_Constructed_ReturnsTargetList(): void
    {
        $list = new IntList(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);

        $result = $list->chunks(3);

        self::assertSame([[1, 2, 3], [4, 5, 6], [7, 8, 9], [10]],
            array_map(fn(IntList $list) => $list->toArray(), $result));
    }

    public function testChunks_SingleElement_ReturnsIntListChunk(): void
    {
        $list = new IntList(1);

        $result = $list->chunks(1);

        self::assertInstanceOf(IntList::class, $result[0]);
    }

    public function testDestruct_SomeList_ReturnsSourceValues(): void
    {
        $list = new IntList(1, 2, 3);

        self::assertSame([1, 2, 3], [...$list]);
    }

    public function testMax_Constructed_ReturnsMax(): void
    {
        $list = new IntList(1, 2, 3, 4, 2, 3);

        self::assertSame(4, $list->max());
    }

    public function testMin_Constructed_ReturnsMin(): void
    {
        $list = new IntList(2, 3, 4, 2, 1);

        self::assertSame(1, $list->min());
    }
}
