<?php
declare(strict_types=1);
namespace Purr\Collection;

/**
 * @template-extends AbstractList<string>
 */
class StringList extends AbstractList
{
    public function __construct(string ...$strings)
    {
        parent::__construct($strings);
    }
}
