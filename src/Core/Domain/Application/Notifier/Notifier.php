<?php

declare(strict_types=1);

namespace App\Core\Domain\Application\Notifier;

interface Notifier
{
    public function send(Notification $notification): void;
}
