<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject\Notification;

use App\Core\Domain\Model\ValueObject\SimpleArray;

/**
 * @extends SimpleArray<mixed>
 */
final class Context extends SimpleArray
{
    /**
     * @param array<string, mixed> $values
     */
    public static function fromArray(array $values): Context
    {
        return new self($values);
    }
}
