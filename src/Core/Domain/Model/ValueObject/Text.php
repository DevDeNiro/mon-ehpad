<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject;

abstract readonly class Text implements \Stringable
{
    protected function __construct(protected string $value)
    {
    }

    abstract public static function fromString(string $value): self;

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
