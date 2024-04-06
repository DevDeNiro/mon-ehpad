<?php

declare(strict_types=1);

namespace App\Core\Domain\ValueObject;

final readonly class FullName
{
    private function __construct(private string $value)
    {
    }

    public static function create(string $fullName): self
    {
        return new self($fullName);
    }

    public function value(): string
    {
        return $this->value;
    }
}
