<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\StringNotEmptySet;

#[CoversClass(StringNotEmptySet::class)]
final class StringUniqueNotEmptyListTest extends TestCase
{
    public function testUniqueConstructedReturnsUniqList(): void
    {
        $list = new StringNotEmptySet(
            'a',
            'b',
            'c',
            'a',
            'b'
        );

        self::assertSame(['a', 'b', 'c'], $list->unique()->toArray());
    }

    public function testConstructorEmptyListThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Strings are empty');

        new StringNotEmptySet();
    }
}
