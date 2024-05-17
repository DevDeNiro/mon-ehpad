<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Doctrine\DBAL\Types;

use App\Domain\User\Enum\Status;
use App\Infrastructure\Core\Doctrine\DBAL\Types\EnumType;

/**
 * @extends EnumType<Status>
 */
final class StatusType extends EnumType
{
    public function getName(): string
    {
        return 'status';
    }

    public function getEnum(): string
    {
        return Status::class;
    }
}
