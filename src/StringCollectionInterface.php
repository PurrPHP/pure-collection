<?php
declare(strict_types=1);

namespace Purr\Collection;

interface StringCollectionInterface
{
    public static function fromInts(int ...$numbers): static;

    public function has(string $value): bool;

    public function sortedAlphabetically(): static;

    public function join(string $separator = ''): string;

    public function diff(StringCollectionInterface $list2): static;
}
