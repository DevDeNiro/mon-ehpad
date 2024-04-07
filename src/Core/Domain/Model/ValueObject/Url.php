<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject;

use App\Core\Domain\Validation\Assert;

final readonly class Url implements Str
{
    public function __construct(private string $value)
    {
    }

    public static function create(string $value): self
    {
        Assert::notEmpty($value);
        Assert::url($value);

        return new self($value);
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
