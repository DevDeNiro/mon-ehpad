<?php

declare(strict_types=1);

namespace App\Core\Domain\ValueObject;

final readonly class Url
{
    public function __construct(private string $value)
    {
    }

    public static function create(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }
}
