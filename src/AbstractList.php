<?php
declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template TValue
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

    /**
     * @param callable(TValue):string $keyCallable
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
