<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\Exception\InvalidArgumentException;
use Purr\Collection\IntNotEmptyList;
use Purr\Collection\IntNotEmptySet;

#[CoversClass(IntNotEmptyList::class)]
#[CoversClass(IntNotEmptySet::class)]
final class IntNotEmptyCollectionsTest extends TestCase
{
    public function testUnique_Constructed_ReturnsUniqueSet(): void
    {
        $list = new IntNotEmptySet(1, 2, 3, 4, 2, 3);

        self::assertSame([1, 2, 3, 4], $list->unique()->toArray());
    }

    public function testConstructor_EmptyList_throwsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Numbers are empty');

        new IntNotEmptyList();
    }

    public function testConstructor_EmptySet_throwsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Numbers are empty');

        new IntNotEmptySet();
    }

    public function testMax_Constructed_ReturnsMax(): void
    {
        $list = new IntNotEmptySet(1, 2, 3, 4, 2, 3);

        self::assertSame(4, $list->max());
    }

    public function testMin_Constructed_ReturnsMin(): void
    {
        $list = new IntNotEmptySet(2, 3, 4, 2, 1);

        self::assertSame(1, $list->min());
    }
}
