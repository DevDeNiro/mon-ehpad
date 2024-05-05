<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\ValueObject;

use App\Core\Domain\Model\ValueObject\Text;
use App\Core\Domain\Validation\Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final readonly class PlainPassword extends Text
{
    public static function fromString(string $value): self
    {
        Assert::notEmpty($value);
        Assert::passwordStrength($value, PasswordStrength::STRENGTH_WEAK);

        return new self($value);
    }
}
