<?php

declare(strict_types=1);

namespace Purr\Collection\Exception;

class IndexOutOfBoundsException extends \OutOfBoundsException
{
    public function __construct(private readonly int|string $offset, private readonly int $size, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('Index %d is out of bounds for list of size %d', $offset, $size), $code, $previous);
    }

    public function getOffset(): int|string
    {
        return $this->offset;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getContext(): array
    {
        return [
            'offset' => $this->offset,
            'size' => $this->size,
        ];
    }
}
