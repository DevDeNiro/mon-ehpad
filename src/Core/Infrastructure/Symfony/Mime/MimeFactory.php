<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Symfony\Mime;

use App\Core\Domain\Application\Notifier\Notification;
use App\Core\Domain\Model\ValueObject\Notification\EmailRecipient;
use App\Core\Domain\Validation\Assert;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mime\Address;

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
