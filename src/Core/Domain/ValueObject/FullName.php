<?php

declare(strict_types=1);

namespace App\Core\Domain\ValueObject;

use App\Core\Domain\Assert\Assert;

final readonly class FullName extends Str
{
    private function __construct(private string $value)
    {
    }

    public static function create(string $fullName): self
    {
        Assert::notEmpty($fullName);

        return new self($fullName);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
