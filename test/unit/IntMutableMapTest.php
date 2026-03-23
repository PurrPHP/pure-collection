<?php

declare(strict_types=1);

namespace Purr\Collection\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\IntMutableMap;

#[CoversClass(IntMutableMap::class)]
class IntMutableMapTest extends TestCase
{
    public function testSetValueExistsRewritesValue(): void
    {
        $m = new IntMutableMap();

        $m['a'] = 1;
        $m['a'] = 2;

        self::assertSame([], $m->toArray());
    }
}
