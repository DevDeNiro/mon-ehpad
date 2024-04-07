<?php

declare(strict_types=1);

namespace App\Core\Domain\ValueObject;

use App\Core\Domain\Assert\Assert;

final readonly class Email extends Str
{
    private function __construct(private string $value)
    {
    }

    public static function create(string $email): self
    {
        Assert::notEmpty($email);
        Assert::email($email);

        return new self($email);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Email $email): bool
    {
        return $this->value === $email->value();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
