<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Enum;

use App\Core\Domain\Model\Enum\EnumTrait;

enum Status: string
{
    use EnumTrait;

    case Registered = 'registered';
    case Verified = 'verified';
    case Completed = 'completed';
}
