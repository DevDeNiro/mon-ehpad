<?php

declare(strict_types=1);

namespace App\Security\Domain\ValueObject;

final readonly class PlainPassword
{
    private function __construct(private string $value)
    {
    }

    public static function create(string $plainPassword): self
    {
        return new self($plainPassword);
    }

    public function value(): string
    {
        return $this->value;
    }
}
