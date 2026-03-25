<?php
declare(strict_types=1);

namespace Purr\Collection\Test\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Purr\Collection\Exception\InvalidArgumentTypeException;

#[CoversClass(InvalidArgumentTypeException::class)]
class InvalidArgumentTypeExceptionTest extends TestCase
{
    public function testGetContext_Constructed_ReturnsGivenProps(): void
    {
        $e = new InvalidArgumentTypeException('a', 'b');

        self::assertEquals([
            'type' => 'a',
            'expects' => 'b'
        ], $e->getContext());
    }

}
