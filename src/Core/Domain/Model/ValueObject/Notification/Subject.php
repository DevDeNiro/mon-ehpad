<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject\Notification;

use App\Core\Domain\Model\ValueObject\Text;
use App\Core\Domain\Validation\Assert;

final readonly class Subject extends Text
{
    public static function fromString(string $value): Subject
    {
        Assert::notEmpty($value);

        return new self($value);
    }
}
