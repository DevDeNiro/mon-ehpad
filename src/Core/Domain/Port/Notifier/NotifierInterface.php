<?php

declare(strict_types=1);

namespace App\Core\Domain\Port\Notifier;

use App\Core\Domain\Model\Notification\Email;

interface NotifierInterface
{
    public function sendEmail(Email $email): void;
}
