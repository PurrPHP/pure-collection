<?php
declare(strict_types=1);
namespace Purr\Collection;

/**
 * @template TValue
 * @template-extends AbstractList<TValue>
 */
abstract class AbstractSet extends AbstractList
{
    protected function __construct(array $items)
    {
        /**
         * Psalm cannot understand inheritance
         *
         * @psalm-suppress MixedArgumentTypeCoercion
         * @psalm-suppress InvalidArgument
         */
        $unique = $this->filterUniqValues($items);

        /**
         * @psalm-suppress MixedArgumentTypeCoercion
         * @psalm-suppress InvalidArgument
         */
        parent::__construct($unique);
    }
}
