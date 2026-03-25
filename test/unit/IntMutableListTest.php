<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractMutableList;
use Purr\Collection\Exception\IndexOutOfBoundsException;
use Purr\Collection\Exception\InvalidArgumentTypeException;
use Purr\Collection\IntMutableList;

#[CoversClass(IntMutableList::class)]
#[CoversClass(AbstractMutableList::class)]
class IntMutableListTest extends TestCase
{
    public function testConstruct_WithValues_ReturnsOrderedList(): void
    {
        $list = new IntMutableList(3, 1, 2);

        self::assertSame([3, 1, 2], $list->toArray());
    }

    public function testOffsetSet_AppendWithNullKey_AddsToEnd(): void
    {
        $list = new IntMutableList(1, 2);

        $list[] = 3;

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testOffsetSet_ExistingIndex_OverwritesValue(): void
    {
        $list = new IntMutableList(1, 2, 3);

        $list[1] = 99;

        self::assertSame([1, 99, 3], $list->toArray());
    }

    public function testOffsetSet_IndexBeyondEnd_ThrowsIndexOutOfBoundsException(): void
    {
        $list = new IntMutableList(1, 2);

        $this->expectException(IndexOutOfBoundsException::class);

        $list[5] = 10;
    }

    public function testOffsetSet_InvalidType_ThrowsInvalidArgumentException(): void
    {
        $list = new IntMutableList();

        $this->expectException(InvalidArgumentTypeException::class);

        $list[] = 'not-an-int';
    }

    public function testOffsetUnset_MiddleElement_ReindexesList(): void
    {
        $list = new IntMutableList(10, 20, 30);

        unset($list[1]);

        self::assertSame([10, 30], $list->toArray());
    }

    public function testOffsetUnset_FirstElement_ReindexesList(): void
    {
        $list = new IntMutableList(10, 20, 30);

        unset($list[0]);

        self::assertSame([20, 30], $list->toArray());
    }

    public function testOffsetUnset_LastElement_ReindexesList(): void
    {
        $list = new IntMutableList(10, 20, 30);

        unset($list[2]);

        self::assertSame([10, 20], $list->toArray());
    }

    public function testOffsetUnset_AllElements_ReturnsEmptyList(): void
    {
        $list = new IntMutableList(10);

        unset($list[0]);

        self::assertSame([], $list->toArray());
        self::assertTrue($list->isEmpty());
    }

    public function testOffsetExists_ExistingIndex_ReturnsTrue(): void
    {
        $list = new IntMutableList(10, 20);

        self::assertTrue(isset($list[0]));
        self::assertTrue(isset($list[1]));
    }

    public function testOffsetExists_OutOfBoundsIndex_ReturnsFalse(): void
    {
        $list = new IntMutableList(10, 20);

        self::assertFalse(isset($list[2]));
    }

    public function testOffsetGet_ExistingIndex_ReturnsValue(): void
    {
        $list = new IntMutableList(10, 20, 30);

        self::assertSame(20, $list[1]);
    }

    public function testAppendMultiple_PreservesListStructure(): void
    {
        $list = new IntMutableList();

        $list[] = 1;
        $list[] = 2;
        $list[] = 3;

        self::assertSame([1, 2, 3], $list->toArray());
        self::assertSame(3, $list->count());
    }

    public function testOffsetUnset_ThenAppend_IndicesAreContiguous(): void
    {
        $list = new IntMutableList(1, 2, 3);

        unset($list[1]);
        $list[] = 4;

        self::assertSame([1, 3, 4], $list->toArray());
    }

    #[DataProvider('providerIntCollectionMethods')]
    public function testIntCollectionTrait_WithValues_ReturnsExpected(
        string $method,
        array $source,
        mixed $expected
    ): void {
        $list = new IntMutableList(...$source);

        self::assertSame($expected, $list->{$method}());
    }

    public static function providerIntCollectionMethods(): array
    {
        return [
            'max' => ['max', [1, 5, 3], 5],
            'max empty' => ['max', [], null],
            'min' => ['min', [1, 5, 3], 1],
            'min empty' => ['min', [], null],
            'sum' => ['sum', [1, 2, 3], 6],
            'sum empty' => ['sum', [], 0],
            'avg' => ['avg', [1, 2, 3], 2.0],
            'avg empty' => ['avg', [], null],
        ];
    }

    public function testNotZeroValues_WithZeros_FiltersZeros(): void
    {
        $list = new IntMutableList(0, 1, 0, 2, 0);

        self::assertSame([1, 2], $list->notZeroValues()->toArray());
    }

    public function testCount_AfterMutations_ReflectsCurrentSize(): void
    {
        $list = new IntMutableList(1, 2, 3);

        $list[] = 4;
        unset($list[0]);

        self::assertSame(3, $list->count());
    }

    public function testOffsetSet_StringOffset_ThrowsInvalidArgumentException(): void
    {
        $list = new IntMutableList(1, 2);

        $this->expectException(InvalidArgumentTypeException::class);

        $list['key'] = 3;
    }

    public function testOffsetGet_StringOffset_ThrowsInvalidArgumentException(): void
    {
        $list = new IntMutableList(1, 2);

        $this->expectException(InvalidArgumentTypeException::class);

        $_ = $list['key'];
    }

    public function testOffsetExists_StringOffset_ThrowsInvalidArgumentException(): void
    {
        $list = new IntMutableList(1, 2);

        $this->expectException(InvalidArgumentTypeException::class);

        isset($list['key']);
    }

    public function testOffsetUnset_StringOffset_ThrowsInvalidArgumentException(): void
    {
        $list = new IntMutableList(1, 2);

        $this->expectException(InvalidArgumentTypeException::class);

        unset($list['key']);
    }
}
