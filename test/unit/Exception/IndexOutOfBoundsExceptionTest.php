<?php
declare(strict_types=1);

namespace Purr\Collection\Test\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\Exception\IndexOutOfBoundsException;

#[CoversClass(IndexOutOfBoundsException::class)]
class IndexOutOfBoundsExceptionTest extends TestCase
{
    public function testGetContext_Constructed_ReturnsGivenProps(): void
    {
        $e = new IndexOutOfBoundsException(3, 2);

        self::assertEquals([
            'offset' => 3,
            'size' => 2,
        ], $e->getContext());
    }

    public function testGetMessage_Constructed_ReturnsFormattedMessage(): void
    {
        $e = new IndexOutOfBoundsException(3, 2);

        self::assertEquals('Index 3 is out of bounds for list of size 2', $e->getMessage());
    }
}
