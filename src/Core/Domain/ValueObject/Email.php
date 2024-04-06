<?php

declare(strict_types=1);

namespace App\Core\Domain\ValueObject;

final readonly class Email
{
    private function __construct(private string $value)
    {
    }

    public static function create(string $email): self
    {
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
}
