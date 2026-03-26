<?php
declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractList;
use Purr\Collection\AbstractMutableList;
use Purr\Collection\Exception\IndexOutOfBoundsException;
use Purr\Collection\Exception\InvalidArgumentTypeException;
use Purr\Collection\IntCollectionTrait;
use Purr\Collection\IntList;
use Purr\Collection\IntMutableList;
use Purr\Collection\IntMutableSet;

#[CoversClass(IntMutableSet::class)]
#[CoversClass(IntMutableList::class)]
#[CoversClass(AbstractMutableList::class)]
#[CoversClass(AbstractList::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(IntCollectionTrait::class)]
class IntMutableSetTest extends TestCase
{
    public function testConstruct_NotUniqueValues_StoresUniqValues(): void
    {
        $list = new IntMutableSet(3, 1, 1, 2);

        self::assertSame([3, 1, 2], $list->toArray());
    }

    public function testConstruct_WithValues_ReturnsList(): void
    {
        $list = new IntMutableSet(3, 1, 2);

        self::assertSame([3, 1, 2], $list->toArray());
    }

    public function testOffsetSet_AppendWithNullKey_AddsToEnd(): void
    {
        $list = new IntMutableSet(1, 2);

        $list[] = 3;

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testOffsetSet_ExistingIndex_OverwritesValue(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $list[1] = 99;

        self::assertSame([1, 99, 3], $list->toArray());
    }

    public function testOffsetSet_IndexBeyondEnd_ThrowsIndexOutOfBoundsException(): void
    {
        $list = new IntMutableSet(1, 2);

        $this->expectException(IndexOutOfBoundsException::class);

        $list[5] = 10;
    }

    public function testOffsetSet_InvalidType_ThrowsInvalidArgumentException(): void
    {
        $list = new IntMutableSet();

        $this->expectException(InvalidArgumentTypeException::class);

        $list[] = 'not-an-int'; // @phpstan-ignore offsetAssign.valueType
    }

    public function testOffsetUnset_MiddleElement_ReindexesList(): void
    {
        $list = new IntMutableSet(10, 20, 30);

        unset($list[1]);

        self::assertSame([10, 30], $list->toArray());
    }

    public function testOffsetUnset_FirstElement_ReindexesList(): void
    {
        $list = new IntMutableSet(10, 20, 30);

        unset($list[0]);

        self::assertSame([20, 30], $list->toArray());
    }

    public function testOffsetUnset_LastElement_ReindexesList(): void
    {
        $list = new IntMutableSet(10, 20, 30);

        unset($list[2]);

        self::assertSame([10, 20], $list->toArray());
    }

    public function testOffsetUnset_AllElements_ReturnsEmptyList(): void
    {
        $list = new IntMutableSet(10);

        unset($list[0]);

        self::assertSame([], $list->toArray());
    }

    public function testOffsetExists_ExistingIndex_ReturnsTrue(): void
    {
        $list = new IntMutableSet(10, 20);

        self::assertTrue(isset($list[0]));
        self::assertTrue(isset($list[1]));
    }

    public function testOffsetExists_OutOfBoundsIndex_ReturnsFalse(): void
    {
        $list = new IntMutableSet(10, 20);

        self::assertFalse(isset($list[2]));
    }

    public function testOffsetGet_ExistingIndex_ReturnsValue(): void
    {
        $list = new IntMutableSet(10, 20, 30);

        self::assertSame(20, $list[1]);
    }

    public function testAppendMultiple_PreservesListStructure(): void
    {
        $list = new IntMutableSet();

        $list[] = 1;
        $list[] = 2;
        $list[] = 3;

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testOffsetUnset_ThenAppend_IndicesAreContiguous(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        unset($list[1]);
        $list[] = 4;

        self::assertSame([1, 3, 4], $list->toArray());
    }

    /** @param array<int, int> $source */
    #[DataProvider('providerIntCollectionMethods')]
    public function testIntCollectionTrait_WithValues_ReturnsExpected(
        string $method,
        array $source,
        mixed $expected
    ): void {
        $list = new IntMutableSet(...$source);

        self::assertSame($expected, $list->{$method}());
    }

    /** @return array<array-key, array<int, mixed>> */
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

    public function testNotZeroValues_MutatesOriginalInPlace(): void
    {
        $list = new IntMutableSet(0, 1, 0, 2, 0);

        $result = $list->notZeroValues();

        self::assertSame([1, 2], $list->toArray());
        self::assertSame($list, $result);
    }

    public function testCount_AfterMutations_ReflectsCurrentSize(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $list[] = 4;
        unset($list[0]);

        self::assertSame(3, $list->count());
    }

    public function testOffsetSet_StringOffset_ThrowsInvalidArgumentException(): void
    {
        $list = new IntMutableSet(1, 2);

        $this->expectExceptionObject(new InvalidArgumentTypeException('string', 'int'));

        $list['key'] = 3; // @phpstan-ignore offsetAssign.dimType
    }

    public function testOffsetGet_StringOffset_ThrowsInvalidArgumentException(): void
    {
        $list = new IntMutableSet(1, 2);

        $this->expectExceptionObject(new InvalidArgumentTypeException('string', 'int'));

        $_ = $list['key']; // @phpstan-ignore offsetAccess.notFound
    }

    public function testOffsetExists_StringOffset_ThrowsInvalidArgumentException(): void
    {
        $list = new IntMutableSet(1, 2);

        $this->expectExceptionObject(new InvalidArgumentTypeException('string', 'int'));

        isset($list['key']); // @phpstan-ignore isset.offset,expr.resultUnused
    }

    public function testOffsetUnset_StringOffset_ThrowsInvalidArgumentException(): void
    {
        $list = new IntMutableSet(1, 2);

        $this->expectExceptionObject(new InvalidArgumentTypeException('string', 'int'));

        unset($list['key']);  // @phpstan-ignore unset.offset
    }

    public function testAdd_SingleValue_AppendsToEnd(): void
    {
        $list = new IntMutableSet(1, 2);

        $list->add(3);

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testAdd_MultipleNotUniqueValues_AppendsUniqValuesToEnd(): void
    {
        $list = new IntMutableSet(1, 2);

        $list->add(3, 4, 4, 5, 1, 2);

        self::assertSame([1, 2, 3, 4, 5], $list->toArray());
    }

    public function testAdd_ToEmptyList_PopulatesTheList(): void
    {
        $list = new IntMutableSet();

        $list->add(10, 20);

        self::assertSame([10, 20], $list->toArray());
    }

    public function testAdd_Constrcuted_ReturnsSameInstance(): void
    {
        $list = new IntMutableSet(1);

        $result = $list->add(2);

        self::assertSame($list, $result);
    }

    public function testAdd_ChainedCalls_AppendsAll(): void
    {
        $list = new IntMutableSet(1);

        $list->add(2)->add(3, 4);

        self::assertSame([1, 2, 3, 4], $list->toArray());
    }

    public function testAdd_NoArguments_LeavesListUnchanged(): void
    {
        $list = new IntMutableSet(1, 2);

        $list->add();

        self::assertSame([1, 2], $list->toArray());
    }

    // insert

    public function testInsert_AtStart_InsertsBeforeFirstElement(): void
    {
        $list = new IntMutableSet(2, 3);

        $list->insert(0, 1);

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testInsert_AtEnd_AppendsToList(): void
    {
        $list = new IntMutableSet(1, 2);

        $list->insert(2, 3);

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testInsert_InMiddle_InsertsCorrectly(): void
    {
        $list = new IntMutableSet(1, 3);

        $list->insert(1, 2);

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testInsert_MultipleValues_InsertsAllInOrder(): void
    {
        $list = new IntMutableSet(1, 4);

        $list->insert(1, 2, 3);

        self::assertSame([1, 2, 3, 4], $list->toArray());
    }

    public function testInsert_OutOfBounds_ThrowsIndexOutOfBoundsException(): void
    {
        $list = new IntMutableSet(1, 2);

        $this->expectException(IndexOutOfBoundsException::class);

        $list->insert(5, 99);
    }

    public function testInsert_InvalidType_ThrowsInvalidArgumentTypeException(): void
    {
        $list = new IntMutableSet(1, 2);

        $this->expectException(InvalidArgumentTypeException::class);

        $list->insert(1, 'not-an-int'); // @phpstan-ignore argument.type
    }

    public function testInsert_ReturnsSameInstance(): void
    {
        $list = new IntMutableSet(1, 2);

        self::assertSame($list, $list->insert(1, 99));
    }

    // remove

    public function testRemove_ExistingValue_RemovesFirstOccurrence(): void
    {
        $list = new IntMutableSet(1, 2, 2, 3);

        $list->remove(2);

        self::assertSame([1, 3], $list->toArray());
    }

    public function testRemove_NotFound_LeavesListUnchanged(): void
    {
        $list = new IntMutableSet(1, 2);

        $list->remove(99);

        self::assertSame([1, 2], $list->toArray());
    }

    public function testRemove_ReturnsSameInstance(): void
    {
        $list = new IntMutableSet(1, 2);

        self::assertSame($list, $list->remove(1));
    }

    // removeAll

    public function testRemoveAll_MultipleValues_RemovesAllOccurrences(): void
    {
        $list = new IntMutableSet(1, 2, 3, 2, 1);

        $list->removeAll(1, 2);

        self::assertSame([3], $list->toArray());
    }

    public function testRemoveAll_NoneMatch_LeavesListUnchanged(): void
    {
        $list = new IntMutableSet(1, 2);

        $list->removeAll(99);

        self::assertSame([1, 2], $list->toArray());
    }

    public function testRemoveAll_ReturnsSameInstance(): void
    {
        $list = new IntMutableSet(1, 2);

        self::assertSame($list, $list->removeAll(1));
    }

    // clear

    public function testClear_NonEmptyList_RemovesAllElements(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $list->clear();

        self::assertSame([], $list->toArray());
    }

    public function testClear_ReturnsSameInstance(): void
    {
        $list = new IntMutableSet(1);

        self::assertSame($list, $list->clear());
    }

    // prepend

    public function testPrepend_SingleValue_AddsToStart(): void
    {
        $list = new IntMutableSet(2, 3);

        $list->prepend(1);

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testPrepend_MultipleValues_PrependsAllInOrder(): void
    {
        $list = new IntMutableSet(3);

        $list->prepend(1, 2);

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testPrepend_InvalidType_ThrowsInvalidArgumentTypeException(): void
    {
        $list = new IntMutableSet(1);

        $this->expectException(InvalidArgumentTypeException::class);

        $list->prepend('not-an-int');
    }

    public function testPrepend_ReturnsSameInstance(): void
    {
        $list = new IntMutableSet(2);

        self::assertSame($list, $list->prepend(1));
    }

    // reverse

    public function testReverse_ReversesOrder(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $list->reverse();

        self::assertSame([3, 2, 1], $list->toArray());
    }

    public function testReverse_EmptyList_RemainsEmpty(): void
    {
        $list = new IntMutableSet();

        $list->reverse();

        self::assertSame([], $list->toArray());
    }

    public function testReverse_ReturnsSameInstance(): void
    {
        $list = new IntMutableSet(1, 2);

        self::assertSame($list, $list->reverse());
    }

    // pop

    public function testPop_NonEmptyList_RemovesAndReturnsLastElement(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $popped = $list->pop();

        self::assertSame(3, $popped);
        self::assertSame([1, 2], $list->toArray());
    }

    public function testPop_EmptyList_ReturnsNull(): void
    {
        $list = new IntMutableSet();

        self::assertNull($list->pop());
    }

    // shift

    public function testShift_NonEmptyList_RemovesAndReturnsFirstElement(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $shifted = $list->shift();

        self::assertSame(1, $shifted);
        self::assertSame([2, 3], $list->toArray());
    }

    public function testShift_EmptyList_ReturnsNull(): void
    {
        $list = new IntMutableSet();

        self::assertNull($list->shift());
    }

    // indexOf / lastIndexOf

    public function testIndexOf_ExistingValue_ReturnsFirstIndex(): void
    {
        $list = new IntMutableSet(10, 20, 10, 30);

        self::assertSame(0, $list->indexOf(10));
    }

    public function testIndexOf_NotFound_ReturnsNull(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        self::assertNull($list->indexOf(99));
    }

    public function testLastIndexOf_ExistingValue_ReturnsLastIndex(): void
    {
        $list = new IntMutableSet(10, 20, 30);

        self::assertSame(0, $list->lastIndexOf(10));
    }

    public function testLastIndexOf_NotFound_ReturnsNull(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        self::assertNull($list->lastIndexOf(99));
    }

    // product

    public function testProduct_WithValues_ReturnsProduct(): void
    {
        $list = new IntMutableSet(2, 3, 4);

        self::assertSame(24, $list->product());
    }

    public function testProduct_EmptyList_ReturnsOne(): void
    {
        $list = new IntMutableSet();

        self::assertSame(1, $list->product());
    }

    // median

    public function testMedian_OddCount_ReturnsMiddleValue(): void
    {
        $list = new IntMutableSet(3, 1, 2);

        self::assertSame(2.0, $list->median());
    }

    public function testMedian_EvenCount_ReturnsAverageOfTwoMiddle(): void
    {
        $list = new IntMutableSet(4, 1, 3, 2);

        self::assertSame(2.5, $list->median());
    }

    public function testMedian_EmptyList_ReturnsNull(): void
    {
        $list = new IntMutableSet();

        self::assertNull($list->median());
    }

    // positiveValues / negativeValues

    public function testPositiveValues_MutatesOriginalInPlace(): void
    {
        $list = new IntMutableSet(-2, 0, 1, 3);

        $result = $list->positiveValues();

        self::assertSame([1, 3], $list->toArray());
        self::assertSame($list, $result);
    }

    public function testNegativeValues_MutatesOriginalInPlace(): void
    {
        $list = new IntMutableSet(-2, 0, 1, 3);

        $result = $list->negativeValues();

        self::assertSame([-2], $list->toArray());
        self::assertSame($list, $result);
    }

    // abs

    public function testAbs_DifferentAbsValues_MutatesOriginalInPlace(): void
    {
        $list = new IntMutableSet(-3, 0, 2, -1);

        $result = $list->abs();

        self::assertSame([3, 0, 2, 1], $list->toArray());
        self::assertSame($list, $result);
    }

    public function testAbs_DuplicatedAbsValues_StoresUniqueAbsValues(): void
    {
        $list = new IntMutableSet(-3, 3, 0, 2, -1);

        $result = $list->abs();

        self::assertSame([3, 0, 2, 1], $list->toArray());
        self::assertSame($list, $result);
    }

    // multiply

    public function testMultiply_Constructed_MutatesOriginalInPlace(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $result = $list->multiply(3);

        self::assertSame([3, 6, 9], $list->toArray());
        self::assertSame($list, $result);
    }

    public function testMultiply_ByZero_AllBecomesZero(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $list->multiply(0);

        self::assertSame([0, 0, 0], $list->toArray());
    }

    // sortAsc / sortDesc

    public function testSortAsc_Constructed_MutatesOriginalInPlace(): void
    {
        $list = new IntMutableSet(3, 1, 2);

        $result = $list->sortAsc();

        self::assertSame([1, 2, 3], $list->toArray());
        self::assertSame($list, $result);
    }

    public function testSortDesc_Constructed_MutatesOriginalInPlace(): void
    {
        $list = new IntMutableSet(3, 1, 2);

        $result = $list->sortDesc();

        self::assertSame([3, 2, 1], $list->toArray());
        self::assertSame($list, $result);
    }

    // range

    public function testRange_Constructed_ReturnsMaxMinusMin(): void
    {
        $list = new IntMutableSet(1, 5, 3);

        self::assertSame(4, $list->range());
    }

    public function testRange_EmptySet_ReturnsNull(): void
    {
        $list = new IntMutableSet();

        self::assertNull($list->range());
    }

    public function testDiff_RemovesMatchingElements_MutatesInPlace(): void
    {
        $list = new IntMutableSet(1, 2, 3, 4);

        $list->diff(new IntList(2, 3));

        self::assertSame([1, 4], $list->toArray());
    }

    public function testDiff_CompareWithListWithDuplicatedValues_StoresUniqueValues(): void
    {
        $list = new IntMutableSet(1);

        $list->diff(new IntList(2, 3, 2));

        self::assertSame([1], $list->toArray());
    }

    public function testDiff_NoOverlap_LeavesListUnchanged(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $list->diff(new IntList(4, 5));

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testDiff_AllElementsMatch_LeavesEmptyList(): void
    {
        $list = new IntMutableSet(1, 2);

        $list->diff(new IntList(1, 2));

        self::assertSame([], $list->toArray());
    }

    public function testDiff_Constructed_ReindexesResult(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $list->diff(new IntList(1));

        self::assertSame([2, 3], $list->toArray());
        self::assertSame(0, array_key_first($list->toArray()));
    }

    public function testDiff_Constructed_ReturnsSelf(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        self::assertSame($list, $list->diff(new IntList(1)));
    }

    public function testIntersect_KeepsOnlyMatchingElements_MutatesInPlace(): void
    {
        $list = new IntMutableSet(1, 2, 3, 4);

        $list->intersect(new IntList(2, 3, 5));

        self::assertSame([2, 3], $list->toArray());
    }

    public function testIntersect_NoOverlap_LeavesEmptyList(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $list->intersect(new IntList(4, 5));

        self::assertSame([], $list->toArray());
    }

    public function testIntersect_AllElementsMatch_LeavesListUnchanged(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $list->intersect(new IntList(1, 2, 3, 4));

        self::assertSame([1, 2, 3], $list->toArray());
    }

    public function testIntersect_Constructed_ReindexesResult(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        $list->intersect(new IntList(2, 3));

        self::assertSame([2, 3], $list->toArray());
        self::assertSame(0, array_key_first($list->toArray()));
    }

    public function testIntersect_Constructed_ReturnsSelf(): void
    {
        $list = new IntMutableSet(1, 2, 3);

        self::assertSame($list, $list->intersect(new IntList(2)));
    }
}
