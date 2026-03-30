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

    public function intersect(StringCollectionInterface $list2): static
    {
        return new static(...array_intersect($this->collection, $list2->toArray()));
    }

    public function sortedAlphabetically(bool $desc = false): static
    {
        return $desc
            ? $this->sorted(fn (string $a, string $b): int => $b <=> $a)
            : $this->sorted(fn (string $a, string $b): int => $a <=> $b);
    }

    public function join(string $separator = ''): string
    {
        return implode($separator, $this->collection);
    }

    public function implode(string $separator = ''): string
    {
        return $this->join($separator);
    }

    protected function isSupportedType(mixed $value): bool
    {
        return is_string($value);
    }

    protected function getId(mixed $value): int|string
    {
        return $value;
    }
}
