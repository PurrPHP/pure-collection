<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template-extends AbstractSet<int>
 */
class IntSet extends AbstractSet implements IntCollectionInterface
{
    use IntCollectionTrait;

    public function __construct(int ...$numbers)
    {
        parent::__construct($numbers);
    }

    public static function fromString(string $string, string $separator): self
    {
        if ('' === $string) {
            return new self();
        }

        /** @psalm-suppress ArgumentTypeCoercion $values */
        $values = explode($separator, $string);

        return new self(...array_map('intval', $values));
    }

    public function toStringSet(): StringSet
    {
        return StringSet::fromIntList($this->collection);
    }

    public function join(string $separator = ''): string
    {
        return implode($separator, $this->collection);
    }
}
