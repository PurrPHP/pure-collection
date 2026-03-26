<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template TValue
 *
 * @template-extends AbstractList<TValue>
 */
abstract class AbstractSet extends AbstractList
{
    /** @param TValue[] $items */
    protected function __construct(array $items)
    {
        $unique = $this->filterUniqValues($items);

        parent::__construct($unique);
    }
}
