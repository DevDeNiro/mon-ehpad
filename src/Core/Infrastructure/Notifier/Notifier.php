<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Notifier;

use App\Core\Domain\Model\Notification\Email;
use App\Core\Domain\Port\Notifier\NotifierInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as SymfonyEmail;

final readonly class Notifier implements NotifierInterface
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function sendEmail(Email $email): void
    {
        $this->mailer->send(
            (new SymfonyEmail())
                ->to(
                    new Address(
                        $email->recipient()->emailAddress()->value(),
                        $email->recipient()->fullName()?->value() ?? ''
                    )
                )
                ->from(new Address('no-reply@mon-ehpad.fr', 'Mon EHPAD'))
                ->text('')
        );
    }
}
