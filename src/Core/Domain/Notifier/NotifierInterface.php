<?php

declare(strict_types=1);

namespace App\Core\Domain\Notifier;

use App\Core\Domain\Notifier\Email\Email;

interface NotifierInterface
{
    public function sendEmail(Email $email): void;
}
