<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @phpstan-consistent-constructor
 *
 * @template-extends AbstractList<int>
 */
class IntList extends AbstractList implements IntCollectionInterface
{
    use IntCollectionTrait;

    public function __construct(int ...$numbers)
    {
        parent::__construct($numbers);
    }
}
