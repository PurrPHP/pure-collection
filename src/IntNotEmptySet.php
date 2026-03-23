<?php

declare(strict_types=1);

namespace Purr\Collection;

use Purr\Collection\Exception\InvalidArgumentException;

class IntNotEmptySet extends IntSet
{
    /**
     * @throws InvalidArgumentException when no numbers provided
     */
    public function __construct(int ...$numbers)
    {
        if (!$numbers) {
            throw new InvalidArgumentException('Numbers are empty');
        }

        parent::__construct(...$numbers);
    }
}
