<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @phpstan-consistent-constructor
 *
 * @template TValue
 *
 * @template-extends AbstractCollection<array-key,TValue>
 */
abstract class AbstractMap extends AbstractCollection
{
    /**
     * @param callable(TValue):string $keyCallable
     *
     * @return array<string,array<array-key,TValue>>
     */
    final public function groupBy(callable $keyCallable): array
    {
        $result = [];
        foreach ($this->collection as $key => $item) {
            $listKey = $keyCallable($item);
            $result[$listKey][$key] = $item;
        }

        return $result;
    }
}
