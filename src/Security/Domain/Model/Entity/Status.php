<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Entity;

enum Status: string
{
    case WaitingForConfirmation = 'waiting_for_confirmation';

    case Active = 'active';
}
