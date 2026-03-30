<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @phpstan-consistent-constructor
 *
 * @template-extends AbstractMap<string>
 */
final class StringMap extends AbstractMap implements StringCollectionInterface
{
    use StringCollectionTrait;

    public function __construct(string ...$strings)
    {
        parent::__construct($strings);
    }
}
