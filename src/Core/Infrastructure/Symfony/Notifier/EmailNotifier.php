<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Symfony\Notifier;

use App\Core\Domain\Application\Notifier\Notification;
use App\Core\Domain\Application\Notifier\Notifier;
use App\Core\Infrastructure\Symfony\Mime\MimeFactory;
use Symfony\Component\Mailer\MailerInterface;

final readonly class EmailNotifier implements Notifier
{
    public function __construct(
        private MailerInterface $mailer
    ) {
    }

    public function send(Notification $notification): void
    {
        $this->mailer->send(MimeFactory::fromNotification($notification));
    }
}
