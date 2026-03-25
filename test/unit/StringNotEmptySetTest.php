<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\Exception\InvalidArgumentException;
use Purr\Collection\StringNotEmptySet;

#[CoversClass(StringNotEmptySet::class)]
final class StringNotEmptySetTest extends TestCase
{
    public function testUnique_Constructed_ReturnsUniqueList(): void
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

    public function testConstructor_EmptyList_throwsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Strings are empty');

        new StringNotEmptySet();
    }
}
