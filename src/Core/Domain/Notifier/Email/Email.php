<?php

declare(strict_types=1);

namespace App\Core\Domain\Notifier\Email;

use App\Core\Domain\Notifier\Notification;

interface Email extends Notification
{
    public function recipient(): Recipient;
}
