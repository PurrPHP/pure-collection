<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template-extends AbstractList<string>
 */
class StringList extends AbstractList
{
    public function __construct(string ...$strings)
    {
        parent::__construct($strings);
    }

    public static function fromInts(int ...$numbers): static
    {
        return new static(...array_map(static fn (int $number): string => (string) $number, $numbers));
    }
}
