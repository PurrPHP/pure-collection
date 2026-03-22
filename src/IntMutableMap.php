<?php
declare(strict_types=1);

namespace Purr\Collection;


use Purr\Collection\Exception\InvalidArgumentException;

/**
 * @template-extends AbstractMap<int>
 */
class IntMutableMap extends AbstractMutableMap implements IntCollectionInterface
{
    public function __construct(int ...$numbers)
    {
        parent::__construct($numbers);
    }

    public function max(): ?int
    {
        return max($this->collection);
    }

    public function min(): ?int
    {
        return min($this->collection);
    }

    public function notZeroValues(): static
    {
        return new static(...$this->filter(static fn(int $i): bool => $i !== 0));
    }

    protected function ensureType(mixed $value): void
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException(sprintf("Invalid type. Got %s. Expects int", gettype($value)));
        }
    }
}
