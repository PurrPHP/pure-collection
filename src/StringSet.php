<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @phpstan-consistent-constructor
 *
 * @template-extends AbstractSet<string>
 */
class StringSet extends AbstractSet implements StringCollectionInterface
{
    use StringCollectionTrait;
}
