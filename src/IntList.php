<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template-extends AbstractList<int>
 */
class IntList extends AbstractList implements IntCollectionInterface
{
    use IntImmutableCollectionTrait;

    public function __construct(int ...$numbers)
    {
        parent::__construct($numbers);
    }

    public function toStringSet(): StringSet
    {
        return StringSet::fromIntList($this->collection);
    }
}
