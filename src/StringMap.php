<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @phpstan-consistent-constructor
 *
 * @template-extends AbstractMap<string>
 */
class StringMap extends AbstractMap implements StringCollectionInterface
{
    use StringCollectionTrait;
}
