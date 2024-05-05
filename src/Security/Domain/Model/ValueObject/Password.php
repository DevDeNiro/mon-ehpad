<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\ValueObject;

use App\Core\Domain\Model\ValueObject\Str;
use App\Core\Domain\Validation\Assert;

final readonly class Password implements Str
{
    private function __construct(private string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
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
}
