<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template-extends AbstractSet<int>
 */
class IntSet extends AbstractSet implements IntCollectionInterface
{
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

    public function max(): ?int
    {
        return max($this->collection);
    }

    public function min(): ?int
    {
        return min($this->collection);
    }

    public function notZeroValues(): static
    {
        return new static(...$this->filter(static fn (int $i): bool => 0 !== $i));
    }
}
