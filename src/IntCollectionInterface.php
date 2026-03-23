<?php

declare(strict_types=1);

namespace Purr\Collection;

/**
 * @template-extends CollectionInterface<int>
 */
interface IntCollectionInterface extends CollectionInterface
{
    public function max(): ?int;

    public function min(): ?int;

    public function notZeroValues(): static;
}
