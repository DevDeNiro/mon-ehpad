<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\ValueObject;

use App\Core\Domain\Model\ValueObject\Str;
use App\Core\Domain\Validation\Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final readonly class PlainPassword implements Str
{
    private function __construct(
        private string $value
    ) {
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->value;
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
}
