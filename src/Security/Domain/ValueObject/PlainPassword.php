<?php

declare(strict_types=1);

namespace App\Security\Domain\ValueObject;

use App\Core\Domain\Assert\Assert;
use App\Core\Domain\ValueObject\Str;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final readonly class PlainPassword extends Str
{
    private function __construct(private string $value)
    {
    }

    public static function create(string $plainPassword): self
    {
        Assert::notEmpty($plainPassword);
        Assert::passwordStrength($plainPassword, PasswordStrength::STRENGTH_WEAK);

        return new self($plainPassword);
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
