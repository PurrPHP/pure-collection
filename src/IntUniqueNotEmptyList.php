<?php
declare(strict_types=1);
namespace Purr\Collection;

class IntUniqueNotEmptyList extends IntSet
{
    public function __construct(int ...$numbers)
    {
        if (!$numbers) {
            throw new \InvalidArgumentException('Numbers are empty');
        }

        parent::__construct(...$numbers);
    }
}
