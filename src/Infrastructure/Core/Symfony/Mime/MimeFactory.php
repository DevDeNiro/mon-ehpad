<?php

declare(strict_types=1);

namespace App\Infrastructure\Core\Symfony\Mime;

use App\Application\Notifier\Notification;
use App\Domain\core\Validation\Assert;
use Symfony\Bridge\Twig\Mime\NotificationEmail;

final class MimeFactory
{
    public static function fromNotification(Notification $notification): NotificationEmail
    {
        Assert::notNull($notification->getTemplate());

        return (new NotificationEmail())
            ->to($notification->getRecipient())
            ->subject($notification->getSubject())
            ->htmlTemplate($notification->getTemplate())
            ->context($notification->getContext());
    }
}
