<?php
declare(strict_types=1);

namespace Purr\Collection;

use Purr\Collection\Exception\InvalidArgumentTypeException;

class IntNotEmptyList extends IntList
{
    /**
     * @throws InvalidArgumentTypeException when no numbers provided
     */
    public function __construct(int ...$numbers)
    {
        if (!$numbers) {
            throw new InvalidArgumentTypeException('Numbers are empty');
        }

        parent::__construct(...$numbers);
    }
}
