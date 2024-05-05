<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject;

use App\Core\Domain\Validation\Assert;

final readonly class OneTimePassword extends Text
{
    public static function fromString(string $value): self
    {
        Assert::regex($value, '/^\d{6}$/', 'Invalid one-time password');

        return new self($value);
    }
}
