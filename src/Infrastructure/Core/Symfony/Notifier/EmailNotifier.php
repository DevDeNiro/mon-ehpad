<?php

declare(strict_types=1);

namespace App\Infrastructure\Core\Symfony\Notifier;

use App\Application\Notifier\Notification;
use App\Application\Notifier\Notifier;
use App\Infrastructure\Core\Symfony\Mime\MimeFactory;
use Symfony\Component\Mailer\MailerInterface;

final readonly class EmailNotifier implements Notifier
{
    public function __construct(
        private MailerInterface $mailer
    )
    {
    }

    public function send(Notification $notification): void
    {
        $this->mailer->send(MimeFactory::fromNotification($notification));
    }
}
