<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Notification;

use App\Core\Domain\Application\Notifier\Notification;
use App\Core\Domain\Model\Entity\PendingOneTimePassword;
use App\Core\Domain\Model\ValueObject\Notification\Context;
use App\Core\Domain\Model\ValueObject\Notification\EmailRecipient;
use App\Core\Domain\Model\ValueObject\Notification\Subject;
use App\Core\Domain\Model\ValueObject\Notification\Template;
use App\Security\Domain\Model\Entity\User;

final readonly class RegistrationConfirmationEmail implements Notification
{
    public function __construct(
        private User $user,
        private PendingOneTimePassword $pendingOneTimePassword
    ) {
    }

    public function getSubject(): Subject
    {
        return Subject::fromString(
            sprintf(
                '%s, confirmez votre inscription !',
                $this->pendingOneTimePassword->getOneTimePassword()
            )
        );
    }

    public function getRecipient(): EmailRecipient
    {
        return new EmailRecipient($this->user->getEmail());
    }

    public function getContext(): Context
    {
        return Context::fromArray([
            'otp' => (string) $this->pendingOneTimePassword->getOneTimePassword(),
        ]);
    }

    public function getContent(): null
    {
        return null;
    }

    public function getTemplate(): Template
    {
        return Template::fromString('emails/registration_confirmation.mjml.twig');
    }
}
