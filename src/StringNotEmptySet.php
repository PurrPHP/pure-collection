<?php
declare(strict_types=1);
namespace Purr\Collection;


use TheSoul\Library\Collection\Exception\InvalidArgumentException;

class StringNotEmptySet extends StringSet
{
    public function __construct(string ...$strings)
    {
        if (!$strings) {
            throw new InvalidArgumentException('Strings are empty');
        }

        parent::__construct(...$strings);
    }
}
