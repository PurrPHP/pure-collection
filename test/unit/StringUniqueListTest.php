<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\StringSet;

#[CoversClass(StringSet::class)]
class StringSetTest extends TestCase
{
    public function testToArrayConstructedReturnsUniqValues(): void
    {
        $set = new StringSet('a', 'b', 'c', 'c', 'a');

        self::assertSame(['a', 'b', 'c'], $set->toArray());
    }

    public function testUniqueConstructedReturnsUniqValues(): void
    {
        $set = new StringSet('a', 'b', 'c', 'c', 'a');

        self::assertSame(['a', 'b', 'c'], $set->unique()->toArray());
    }

    public function testUniqueConstructedReturnsStringSet(): void
    {
        $set = new StringSet('a', 'b', 'c', 'c', 'a');

        self::assertInstanceOf(StringSet::class, $set->unique());
    }

    public function testCountTwoElementsReturnsTwo(): void
    {
        $set = new StringSet('a', 'b');

        self::assertSame(2, $set->count());
    }

    public function testFilterCheckIsBReturnsListOfB(): void
    {
        $set = new StringSet('a', 'b', 'c', 'd');

        self::assertSame(['b'], $set->filter(fn (string $a) => 'b' === $a)->toArray());
    }

    public function testFilterConstructedReturnsStringSet(): void
    {
        $set = new StringSet('a', 'b', 'c', 'd');

        self::assertInstanceOf(StringSet::class, $set->filter(fn (string $a) => true));
    }

    public function testFindLastConstructedReturnsLastItem(): void
    {
        $set = new StringSet('a', 'c', 'd', 'b');

        self::assertSame('b', $set->findLast());
    }

    public function testFindLastConstructedReturnsFistItem(): void
    {
        $set = new StringSet('a', 'c', 'd', 'b');

        self::assertSame('a', $set->findFirst());
    }

    public function testSortedAlphabeticallyConstructedSortsAlphabetically(): void
    {
        $set = new StringSet('c', 'b', 'a');

        self::assertSame(['a', 'b', 'c'], $set->sortedAlphabetically()->toArray());
    }
}
