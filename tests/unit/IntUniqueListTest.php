<?php
declare(strict_types=1);
namespace Purr\Collection\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\IntSet;

#[CoversClass(IntSet::class)]
class IntUniqueListTest extends TestCase
{
    public function testFromString_Constructed_ReturnsEmptyList(): void
    {
        self::assertSame([], IntSet::fromString('', ',')->toArray());
    }

    public function testFromString_Ints_ReturnsTargetList(): void
    {
        self::assertSame([1, 2], IntSet::fromString('1,2', ',')->toArray());
    }

    public function testToArray_Constructed_ReturnsUniqValues(): void
    {
        $set = new IntSet(1, 3, 2, 3, 1);

        self::assertSame([1, 3, 2], $set->toArray());
    }

    public function testUnique_Constructed_ReturnsUniqValues(): void
    {
        $set = new IntSet(1, 3, 2, 3, 1);

        self::assertSame([1, 3, 2], $set->unique()->toArray());
    }

    public function testUnique_Constructed_ReturnsIntSet(): void
    {
        $set = new IntSet(1, 3, 2, 3, 1);

        self::assertInstanceOf(IntSet::class, $set->unique());
    }

    public function testCount_TwoElements_ReturnsTwo(): void
    {
        $set = new IntSet(1, 2);

        self::assertSame(2, $set->count());
    }

    public function testFilter_CheckIsEven_ReturnsEvenSet(): void
    {
        $set = new IntSet(1, 2, 3, 4);

        self::assertSame([2, 4], $set->filter(fn(int $a) => $a % 2 === 0)->toArray());
    }

    public function testFilter_CheckIsEven_ReturnsIntSet(): void
    {
        $set = new IntSet(1, 2, 3, 4);

        self::assertInstanceOf(IntSet::class, $set->filter(fn(int $a) => $a % 2 === 0));
    }

    public function testMax_Constructed_ReturnsMax(): void
    {
        $list = new IntSet(1, 2, 3, 4, 2, 3);

        self::assertSame(4, $list->max());
    }

    public function testMin_Constructed_ReturnsMin(): void
    {
        $list = new IntSet(2, 3, 4, 2, 1);

        self::assertSame(1, $list->min());
    }

    public function testMin_Constructed_ReturnsTargetMap(): void
    {
        $list = new IntSet(2, 3, 0, 2, 1);

        self::assertSame([2, 3, 1], $list->notZeroValues()->toArray());
    }

    public function testSlice_PositiveOffsetAndLimit_ReturnsSlicedList(): void
    {
        $list = new IntSet(1, 2, 3, 4, 5);

        $result = $list->slice(1, 2);

        self::assertSame([2, 3], $result->toArray());
    }

    public function testSlice_PositiveOffsetAndLimit_ReturnsIntUniqueList(): void
    {
        $list = new IntSet(1, 2, 3, 4, 5);

        $result = $list->slice(1, 2);

        self::assertInstanceOf(IntSet::class, $result);
    }

    public function testSlice_OffsetZeroAndLimitTwo_ReturnsFirstTwoElements(): void
    {
        $list = new IntSet(10, 20, 30, 40);

        $result = $list->slice(0, 2);

        self::assertSame([10, 20], $result->toArray());
    }

    public function testSlice_NegativeOffset_ReturnsElementsFromEnd(): void
    {
        $list = new IntSet(1, 2, 3, 4, 5);

        $result = $list->slice(-2, 2);

        self::assertSame([4, 5], $result->toArray());
    }

    public function testSlice_OffsetExceedsListSize_ReturnsEmptyList(): void
    {
        $list = new IntSet(1, 2, 3);

        $result = $list->slice(5, 2);

        self::assertSame([], $result->toArray());
    }

    public function testSlice_LimitExceedsRemainingElements_ReturnsAvailableElements(): void
    {
        $list = new IntSet(1, 2, 3, 4);

        $result = $list->slice(2, 5);

        self::assertSame([3, 4], $result->toArray());
    }

    public function testSlice_ZeroLimit_ReturnsEmptyList(): void
    {
        $list = new IntSet(1, 2, 3, 4);

        $result = $list->slice(1, 0);

        self::assertSame([], $result->toArray());
    }

    public function testSlice_EmptyList_ReturnsEmptyList(): void
    {
        $list = new IntSet();

        $result = $list->slice(0, 2);

        self::assertSame([], $result->toArray());
    }
}
