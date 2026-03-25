<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\AbstractCollection;
use Purr\Collection\AbstractMutableMap;
use Purr\Collection\IntCollectionTrait;
use Purr\Collection\IntMutableMap;

#[CoversClass(IntMutableMap::class)]
#[CoversClass(AbstractMutableMap::class)]
#[CoversClass(AbstractCollection::class)]
#[CoversClass(IntCollectionTrait::class)]
class IntMutableMapTest extends TestCase
{
    public function testOffsetSet_ExistingKey_rewritesValue(): void
    {
        $m = new IntMutableMap();

        $m['a'] = 1;
        $m['a'] = 2;

        self::assertSame(['a' => 2], $m->toArray());
    }
}
