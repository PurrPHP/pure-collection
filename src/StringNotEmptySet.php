<?php

declare(strict_types=1);

namespace Purr\Collection;

use Purr\Collection\Exception\InvalidArgumentTypeException;

class StringNotEmptySet extends StringSet
{
    /**
     * @throws InvalidArgumentTypeException when no strings provided
     */
    public function __construct(string ...$strings)
    {
        if (!$strings) {
            throw new InvalidArgumentTypeException('Strings are empty');
        }

        parent::__construct(...$strings);
    }
}
