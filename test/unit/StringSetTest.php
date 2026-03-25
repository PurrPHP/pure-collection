<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractList;
use Purr\Collection\AbstractSet;
use Purr\Collection\StringCollectionTrait;
use Purr\Collection\StringSet;

#[CoversClass(StringSet::class)]
#[CoversClass(AbstractSet::class)]
#[CoversClass(AbstractList::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(StringCollectionTrait::class)]
class StringSetTest extends TestCase
{
    public function testToArray_Constructed_ReturnsUniqueValues(): void
    {
        $set = new StringSet('a', 'b', 'c', 'c', 'a');

        self::assertSame(['a', 'b', 'c'], $set->toArray());
    }

    public function testUnique_Constructed_ReturnsUniqueValues(): void
    {
        $set = new StringSet('a', 'b', 'c', 'c', 'a');

        self::assertSame(['a', 'b', 'c'], $set->unique()->toArray());
    }

    public function testUnique_Constructed_ReturnsStringSetInstance(): void
    {
        $set = new StringSet('a', 'b', 'c', 'c', 'a');

        self::assertInstanceOf(StringSet::class, $set->unique());
    }

    public function testCount_TwoElements_ReturnsTwo(): void
    {
        $set = new StringSet('a', 'b');

        self::assertSame(2, $set->count());
    }

    public function testFilter_EqualsBCheck_ReturnsMatchingSet(): void
    {
        $set = new StringSet('a', 'b', 'c', 'd');

        self::assertSame(['b'], $set->filter(fn (string $a) => 'b' === $a)->toArray());
    }

    public function testFilter_Constructed_ReturnsStringSetInstance(): void
    {
        $set = new StringSet('a', 'b', 'c', 'd');

        self::assertInstanceOf(StringSet::class, $set->filter(fn (string $a) => true));
    }

    public function testFindLast_Constructed_ReturnsLastItem(): void
    {
        $set = new StringSet('a', 'c', 'd', 'b');

        self::assertSame('b', $set->findLast());
    }

    public function testFindFirst_Constructed_ReturnsFirstItem(): void
    {
        $set = new StringSet('a', 'c', 'd', 'b');

        self::assertSame('a', $set->findFirst());
    }

    public function testSortedAlphabetically_Constructed_ReturnsSortedSet(): void
    {
        $set = new StringSet('c', 'b', 'a');

        self::assertSame(['a', 'b', 'c'], $set->sortedAlphabetically()->toArray());
    }
}
