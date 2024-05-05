<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject;

use App\Core\Domain\Validation\Assert;

final readonly class FullName implements Str
{
    private function __construct(private string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
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
}
