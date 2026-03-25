<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template-extends AbstractSet<string>
 */
class StringSet extends AbstractSet
{
    public function __construct(string ...$strings)
    {
        parent::__construct($strings);
    }

    public static function fromInts(int ...$numbers): static
    {
        return new static(...array_map(static fn (int $number): string => (string) $number, $numbers));
    }

    public function has(string $value): bool
    {
        return in_array($value, $this->collection, true);
    }

    public function sortedAlphabetically(): static
    {
        return $this->sorted(fn (string $a, string $b): int => $a <=> $b);
    }

    public function join(string $separator = ''): string
    {
        return implode($separator, $this->collection);
    }

    public function diff(StringSet $list2): self
    {
        return new self(...array_diff($this->collection, $list2->toArray()));
    }
}
