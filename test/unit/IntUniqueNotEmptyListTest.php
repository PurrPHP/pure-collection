<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\IntNotEmptyList;

#[CoversClass(IntNotEmptyList::class)]
final class IntUniqueNotEmptyListTest extends TestCase
{
    public function testUniqueConstructedReturnsUniqList(): void
    {
        $list = new IntNotEmptyList(
            1,
            2,
            3,
            4,
            2,
            3,
        );

        self::assertSame([1, 2, 3, 4], $list->unique()->toArray());
    }

    public function testConstructorEmptyListThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Numbers are empty');

        new IntNotEmptyList();
    }

    public function testMaxConstructedReturnsMax(): void
    {
        $list = new IntNotEmptyList(1, 2, 3, 4, 2, 3);

        self::assertSame(4, $list->max());
    }

    public function testMinConstructedReturnsMin(): void
    {
        $list = new IntNotEmptyList(2, 3, 4, 2, 1);

        self::assertSame(1, $list->min());
    }
}
