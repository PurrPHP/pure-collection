<?php

declare(strict_types=1);

namespace Purr\Collection;

use Purr\Collection\Exception\InvalidArgumentTypeException;

/**
 * @template-extends AbstractMutableList<int>
 */
class IntMutableList extends AbstractMutableList implements IntCollectionInterface
{
    use IntCollectionTrait;

    public function __construct(int ...$numbers)
    {
        parent::__construct($numbers);
    }

    protected function ensureType(mixed $value): void
    {
        if (!is_int($value)) {
            throw new InvalidArgumentTypeException(type: gettype($value), expects: 'int');
        }
    }
}
