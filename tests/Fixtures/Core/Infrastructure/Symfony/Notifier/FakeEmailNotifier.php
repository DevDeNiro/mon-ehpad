<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Infrastructure\Symfony\Notifier;

use App\Application\Notifier\Notification;
use App\Application\Notifier\Notifier;

final class FakeEmailNotifier implements Notifier
{
    /**
     * @var Notification[]
     */
    public array $notifications = [];

    public function send(Notification $notification): void
    {
        $this->notifications[] = $notification;
    }
}
