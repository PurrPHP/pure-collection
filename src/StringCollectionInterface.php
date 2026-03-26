<?php
declare(strict_types=1);

namespace Purr\Collection;

interface StringCollectionInterface extends CollectionInterface
{
    public static function fromInts(int ...$numbers): static;

    /**
     * Returns collection sorted by values
     */
    public function sortedAlphabetically(bool $desc = false): static;

    public function join(string $separator = ''): string;

    public function implode(string $separator = ''): string;

    public function diff(StringCollectionInterface $list2): static;

    public function intersect(StringCollectionInterface $list2): static;
}
