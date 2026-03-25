<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractList;
use Purr\Collection\AbstractSet;
use Purr\Collection\Exception\InvalidArgumentException;
use Purr\Collection\StringCollectionTrait;
use Purr\Collection\StringNotEmptySet;
use Purr\Collection\StringSet;

#[CoversClass(StringNotEmptySet::class)]
#[CoversClass(StringSet::class)]
#[CoversClass(AbstractSet::class)]
#[CoversClass(AbstractList::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(StringCollectionTrait::class)]
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
