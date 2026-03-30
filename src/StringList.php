<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @phpstan-consistent-constructor
 *
 * @template-extends AbstractList<string>
 */
final class StringList extends AbstractList implements StringCollectionInterface
{
    use StringCollectionTrait;

    public function __construct(string ...$strings)
    {
        parent::__construct($strings);
    }
}
