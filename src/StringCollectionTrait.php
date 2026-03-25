<?php
declare(strict_types=1);

namespace Purr\Collection;

trait StringCollectionTrait
{
    public static function fromInts(int ...$numbers): static
    {
        return new static(...array_map(static fn (int $number): string => (string) $number, $numbers));
    }

    public function diff(StringCollectionInterface $list2): static
    {
        return new static(...array_diff($this->collection, $list2->toArray()));
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

    protected function filterUniqValues(array $items): array
    {
        return array_unique($items);
    }
}
