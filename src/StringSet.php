<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template-extends AbstractSet<string>
 */
class StringSet extends AbstractSet implements StringCollectionInterface
{
    use StringCollectionTrait;

    public function __construct(string ...$strings)
    {
        parent::__construct($strings);
    }
}
