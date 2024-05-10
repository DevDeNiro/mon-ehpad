<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject;

use ArrayAccess;

/**
 * @template TValue
 * @implements ArrayAccess<string, TValue>
 */
abstract class SimpleArray implements ArrayAccess
{
    /**
     * @param array<string, TValue> $values
     */
    protected function __construct(private array $values)
    {
    }

    /**
     * @param array<string, TValue> $values
     * @return self<TValue>
     */
    abstract public static function fromArray(array $values): self;

    /**
     * @param string $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->values[$offset]);
    }

    /**
     * @param string $offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->values[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->values[$offset] = $value;
    }

    /**
     * @param string $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->values[$offset]);
    }

    /**
     * @return array<string, TValue>
     */
    public function toArray(): array
    {
        return $this->values;
    }
}
