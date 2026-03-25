<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template-extends AbstractSet<int>
 */
class IntSet extends AbstractSet implements IntCollectionInterface
{
    use IntImmutableCollectionTrait;

    public function __construct(int ...$numbers)
    {
        parent::__construct($numbers);
    }
}
