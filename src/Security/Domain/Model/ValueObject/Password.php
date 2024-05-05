<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\ValueObject;

use App\Core\Domain\Model\ValueObject\Text;
use App\Core\Domain\Validation\Assert;

final readonly class Password extends Text
{
    public static function fromString(string $value): self
    {
        Assert::notEmpty($value);

        return new self($value);
    }
}
