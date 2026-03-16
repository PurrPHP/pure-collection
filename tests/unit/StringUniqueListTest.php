<?php
declare(strict_types=1);
namespace Purr\Collection\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\StringSet;

#[CoversClass(StringSet::class)]
class StringUniqueListTest extends TestCase
{
    public function testToArray_Constructed_ReturnsUniqValues(): void
    {
        $set = new StringSet('a', 'b', 'c', 'c', 'a');

        self::assertSame(['a', 'b', 'c'], $set->toArray());
    }

    public function testUnique_Constructed_ReturnsUniqValues(): void
    {
        $set = new StringSet('a', 'b', 'c', 'c', 'a');

        self::assertSame(['a', 'b', 'c'], $set->unique()->toArray());
    }

    public function testUnique_Constructed_ReturnsStringUniqueList(): void
    {
        $set = new StringSet('a', 'b', 'c', 'c', 'a');

        self::assertInstanceOf(StringSet::class, $set->unique());
    }

    public function testCount_TwoElements_ReturnsTwo(): void
    {
        $set = new StringSet('a', 'b');

        self::assertSame(2, $set->count());
    }

    public function testFilter_CheckIsB_ReturnsListOfB(): void
    {
        $set = new StringSet('a', 'b', 'c', 'd');

        self::assertSame(['b'], $set->filter(fn(string $a) => $a === 'b')->toArray());
    }

    public function testFilter_Constructed_ReturnsStringUniqueList(): void
    {
        $set = new StringSet('a', 'b', 'c', 'd');

        self::assertInstanceOf(StringSet::class, $set->filter(fn(string $a) => true));
    }

    public function testFindLast_Constructed_ReturnsLastItem(): void
    {
        $set = new StringSet('a', 'c', 'd', 'b');

        self::assertSame('b', $set->findLast());
    }

    public function testFindLast_Constructed_ReturnsFistItem(): void
    {
        $set = new StringSet('a', 'c', 'd', 'b');

        self::assertSame('a', $set->findFirst());
    }

    public function testSortedAlphabetically_Constructed_SortsAlphabetically(): void
    {
        $set = new StringSet('c', 'b', 'a');

        self::assertSame(['a', 'b', 'c'], $set->sortedAlphabetically()->toArray());
    }
}
