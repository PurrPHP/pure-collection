<?php

declare(strict_types=1);

namespace Purr\Collection\Exception;

use Throwable;

class InvalidArgumentTypeException extends InvalidArgumentException
{
    public function __construct(
        private readonly string $type,
        private readonly string $expects,
        int                     $code = 0,
        ?Throwable              $previous = null
    ) {
        parent::__construct(sprintf('Invalid type. Got %s. Expects %s', $type, $expects), $code, $previous);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getExpects(): string
    {
        return $this->expects;
    }

    /**
     * @return array{offset:string|int,size:int}
     */
    public function getContext(): array
    {
        return [
            'type' => $this->type,
            'expects' => $this->expects,
        ];
    }
}
