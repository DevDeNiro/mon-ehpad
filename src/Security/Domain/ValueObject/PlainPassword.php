<?php

declare(strict_types=1);

namespace App\Security\Domain\ValueObject;

use App\Security\Domain\Hasher\PasswordHasherInterface;

final class PlainPassword
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

    public function hash(PasswordHasherInterface $passwordHasher): Password
    {
        return $passwordHasher->hash($this);
    }
}
