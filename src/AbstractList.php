<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template TValue
 *
 * @template-extends AbstractCollection<int<0, max>,TValue>
 */
abstract class AbstractList extends AbstractCollection
{
    /**
     * @param TValue[] $collection
     */
    protected function __construct(array $collection)
    {
        /** @var array<int<0, max>, TValue> $values */
        $values = array_values($collection);

        parent::__construct($values);
    }

    final public function indexOf(mixed $value): ?int
    {
        $key = array_search($value, $this->collection, true);

        return false === $key ? null : $key;
    }

    final public function lastIndexOf(mixed $value): ?int
    {
        $result = null;

        foreach ($this->collection as $key => $item) {
            if ($item === $value) {
                $result = $key;
            }
        }

        return $result;
    }

    /**
     * @param callable(TValue):string $keyCallable
     *
     * @return array<string,list<TValue>>
     */
    final public function groupBy(callable $keyCallable): array
    {
        $result = [];
        foreach ($this->collection as $item) {
            $listKey = $keyCallable($item);
            $result[$listKey][] = $item;
        }

        return $result;
    }
}
