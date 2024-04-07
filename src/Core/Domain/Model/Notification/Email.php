<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Notification;

interface Email extends Notification
{
    public function recipient(): Recipient;
}
