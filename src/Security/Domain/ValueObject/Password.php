<?php

declare(strict_types=1);

namespace App\Security\Domain\ValueObject;

use App\Core\Domain\Assert\Assert;
use App\Core\Domain\ValueObject\Str;

final readonly class Password extends Str
{
    private function __construct(private string $value)
    {
    }

    public static function create(string $password): self
    {
        Assert::notEmpty($password);

        return new self($password);
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
