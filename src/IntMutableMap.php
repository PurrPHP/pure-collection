<?php

declare(strict_types=1);

namespace Purr\Collection;

use Purr\Collection\Exception\InvalidArgumentException;

/**
 * @template-extends AbstractMap<int>
 */
class IntMutableMap extends AbstractMutableMap implements IntCollectionInterface
{
    use IntCollectionTrait;

    public function __construct(int ...$numbers)
    {
        parent::__construct($numbers);
    }

    protected function ensureType(mixed $value): void
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException(sprintf('Invalid type. Got %s. Expects int', gettype($value)));
        }
    }
}
