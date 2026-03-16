<?php
declare(strict_types=1);
namespace Purr\Collection;

/**
 * @template-extends AbstractSet<string>
 */
class StringSet extends AbstractSet
{
    public function __construct(string ...$strings)
    {
        parent::__construct($strings);
    }

    /**
     * @param string[] $strings
     */
    public static function fromArray(array $strings): static
    {
        /**
         * It perfectly works. Collections don't support arguments except collection items
         *
         * @psalm-suppress UnsafeInstantiation
         */
        return new static(...$strings);
    }

    /**
     * @param int[] $numbers
     */
    public static function fromIntList(array $numbers): static
    {
        /**
         * It perfectly works. Collections don't support arguments except collection items
         *
         * @psalm-suppress UnsafeInstantiation
         */
        return new static(...array_map(static fn(int $number): string => (string) $number, $numbers));
    }

    public static function fromInt(int ...$numbers): static
    {
        /**
         * It perfectly works. Collections don't support arguments except collection items
         *
         * @psalm-suppress UnsafeInstantiation
         */
        return new static(...array_map(static fn(int $number): string => (string) $number, $numbers));
    }

    public static function create(string ...$strings): static
    {
        /**
         * It perfectly works. Collections don't support arguments except collection items
         *
         * @psalm-suppress UnsafeInstantiation
         */
        return new static(...$strings);
    }

    public function has(string $value): bool
    {
        return in_array($value, $this->collection, true);
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     */
    public function sortedAlphabetically(): static
    {
        /**
         * @psalm-suppress MoreSpecificReturnType
         * @psalm-suppress LessSpecificReturnStatement
         */
        return $this->sorted(fn(string $a, string $b): int => $a <=> $b);
    }

    public function join(string $separator = ''): string
    {
        return implode($separator, $this->collection);
    }

    public function diff(StringSet $list2): self
    {
        return new self(...array_diff($this->collection, $list2->toArray()));
    }
}
