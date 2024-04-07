<?php

declare(strict_types=1);

namespace App\Security\Domain\Entity;

enum Status: string
{
    case WaitingForConfirmation = 'waiting_for_confirmation';

    case Active = 'active';
}
