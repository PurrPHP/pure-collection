<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template-extends AbstractList<string>
 */
class StringList extends AbstractList implements StringCollectionInterface
{
    use StringCollectionTrait;

    public function __construct(string ...$strings)
    {
        parent::__construct($strings);
    }
}
