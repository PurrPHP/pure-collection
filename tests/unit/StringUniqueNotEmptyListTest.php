<?php
declare(strict_types=1);

namespace Purr\Collection\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\StringNotEmptySet;

#[CoversClass(StringNotEmptySet::class)]
final class StringUniqueNotEmptyListTest extends TestCase
{
    public function testUnique_Constructed_ReturnsUniqList(): void
    {
        $list = new StringNotEmptySet(
            'a', 'b', 'c', 'a', 'b'
        );

        self::assertSame(['a', 'b', 'c'], $list->unique()->toArray());
    }

    public function testConstructor_EmptyList_ThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Strings are empty');

        new StringNotEmptySet();
    }
}
