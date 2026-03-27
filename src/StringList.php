<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @phpstan-consistent-constructor
 *
 * @template-extends AbstractList<string>
 */
class StringList extends AbstractList implements StringCollectionInterface
{
    use StringCollectionTrait;
}
