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

        $recipient = $notification->getRecipient();

        Assert::isInstanceOf($recipient, EmailRecipient::class);

        return (new NotificationEmail())
            ->to(new Address((string) $recipient->getEmail(), (string) $recipient->getName()))
            ->subject((string) $notification->getSubject())
            ->htmlTemplate((string) $notification->getTemplate())
            ->context($notification->getContext()->toArray());
    }
}
