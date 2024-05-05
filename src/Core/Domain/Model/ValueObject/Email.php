<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject;

use App\Core\Domain\Validation\Assert;

final readonly class Email implements Str
{
    private function __construct(private string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
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

    public function equals(self $email): bool
    {
        return $this->value === $email->value();
    }
}
