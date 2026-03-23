<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\IntSet;

#[CoversClass(IntSet::class)]
class IntUniqueListTest extends TestCase
{
    public function testFromStringConstructedReturnsEmptyList(): void
    {
        self::assertSame([], IntSet::fromString('', ',')->toArray());
    }

    public function testFromStringIntsReturnsTargetList(): void
    {
        self::assertSame([1, 2], IntSet::fromString('1,2', ',')->toArray());
    }

    public function testToArrayConstructedReturnsUniqValues(): void
    {
        $set = new IntSet(1, 3, 2, 3, 1);

        self::assertSame([1, 3, 2], $set->toArray());
    }

    public function testUniqueConstructedReturnsUniqValues(): void
    {
        $set = new IntSet(1, 3, 2, 3, 1);

        self::assertSame([1, 3, 2], $set->unique()->toArray());
    }

    public function testUniqueConstructedReturnsIntSet(): void
    {
        $set = new IntSet(1, 3, 2, 3, 1);

        self::assertInstanceOf(IntSet::class, $set->unique());
    }

    public function testCountTwoElementsReturnsTwo(): void
    {
        $set = new IntSet(1, 2);

        self::assertSame(2, $set->count());
    }

    public function testFilterCheckIsEvenReturnsEvenSet(): void
    {
        $set = new IntSet(1, 2, 3, 4);

        self::assertSame([2, 4], $set->filter(fn (int $a) => 0 === $a % 2)->toArray());
    }

    public function testFilterCheckIsEvenReturnsIntSet(): void
    {
        $set = new IntSet(1, 2, 3, 4);

        self::assertInstanceOf(IntSet::class, $set->filter(fn (int $a) => 0 === $a % 2));
    }

    public function testMaxConstructedReturnsMax(): void
    {
        $list = new IntSet(1, 2, 3, 4, 2, 3);

        self::assertSame(4, $list->max());
    }

    public function testMinConstructedReturnsMin(): void
    {
        $list = new IntSet(2, 3, 4, 2, 1);

        self::assertSame(1, $list->min());
    }

    public function testMinConstructedReturnsTargetMap(): void
    {
        $list = new IntSet(2, 3, 0, 2, 1);

        self::assertSame([2, 3, 1], $list->notZeroValues()->toArray());
    }

    public function testSlicePositiveOffsetAndLimitReturnsSlicedList(): void
    {
        $list = new IntSet(1, 2, 3, 4, 5);

        $result = $list->slice(1, 2);

        self::assertSame([2, 3], $result->toArray());
    }

    public function testSlicePositiveOffsetAndLimitReturnsIntUniqueList(): void
    {
        $list = new IntSet(1, 2, 3, 4, 5);

        $result = $list->slice(1, 2);

        self::assertInstanceOf(IntSet::class, $result);
    }

    public function testSliceOffsetZeroAndLimitTwoReturnsFirstTwoElements(): void
    {
        $list = new IntSet(10, 20, 30, 40);

        $result = $list->slice(0, 2);

        self::assertSame([10, 20], $result->toArray());
    }

    public function testSliceNegativeOffsetReturnsElementsFromEnd(): void
    {
        $list = new IntSet(1, 2, 3, 4, 5);

        $result = $list->slice(-2, 2);

        self::assertSame([4, 5], $result->toArray());
    }

    public function testSliceOffsetExceedsListSizeReturnsEmptyList(): void
    {
        $list = new IntSet(1, 2, 3);

        $result = $list->slice(5, 2);

        self::assertSame([], $result->toArray());
    }

    public function testSliceLimitExceedsRemainingElementsReturnsAvailableElements(): void
    {
        $list = new IntSet(1, 2, 3, 4);

        $result = $list->slice(2, 5);

        self::assertSame([3, 4], $result->toArray());
    }

    public function testSliceZeroLimitReturnsEmptyList(): void
    {
        $list = new IntSet(1, 2, 3, 4);

        $result = $list->slice(1, 0);

        self::assertSame([], $result->toArray());
    }

    public function testSliceEmptyListReturnsEmptyList(): void
    {
        $list = new IntSet();

        $result = $list->slice(0, 2);

        self::assertSame([], $result->toArray());
    }
}
