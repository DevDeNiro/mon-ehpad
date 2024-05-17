<?php

declare(strict_types=1);

namespace App\Domain\User\Enum;

use App\Domain\core\Model\Enum\EnumTrait;

enum Status: string
{
    use EnumTrait;

    case Registered = 'registered';
    case Verified = 'verified';
    case Completed = 'completed';
}
