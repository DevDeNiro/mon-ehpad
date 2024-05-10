<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject;

use App\Core\Domain\Validation\Assert;

final readonly class Name extends Text
{
    public static function fromString(string $value): Name
    {
        Assert::notEmpty($value);

        return new self($value);
    }
}
