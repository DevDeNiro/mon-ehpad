<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Notifier\Notification;

trait NotifierAssertionsTrait
{
    public static function assertNotificationSent(Notification $notification): void
    {
        $serializedNotification = serialize($notification);
        $notifications = static::notifier()->notifications;
        self::assertContains($serializedNotification, array_map('serialize', $notifications));
    }
}
