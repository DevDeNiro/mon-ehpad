<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\DBAL\Types;

use App\Core\Infrastructure\Doctrine\DBAL\Types\EnumType;
use App\Security\Domain\Model\Enum\Status;

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
