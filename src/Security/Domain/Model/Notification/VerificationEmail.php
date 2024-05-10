<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Notification;

use App\Core\Domain\Application\Notifier\Notification;
use App\Core\Domain\Validation\Assert;
use App\Security\Domain\Model\Entity\User;

final readonly class VerificationEmail implements Notification
{
    public function __construct(private User $user)
    {
    }

    public function getSubject(): string
    {
        Assert::notNull($this->user->getVerificationCode());

        return sprintf(
            '%s, confirmez votre inscription !',
            $this->user->getVerificationCode()->getCode()
        );
    }

    public function getRecipient(): string
    {
        return $this->user->getEmail();
    }

    /**
     * @return array{otp: string}
     */
    public function getContext(): array
    {
        Assert::notNull($this->user->getVerificationCode());

        return ['otp' => $this->user->getVerificationCode()->getCode()];
    }

    public function getContent(): null
    {
        return null;
    }

    public function getTemplate(): string
    {
        return 'emails/registration_confirmation.mjml.twig';
    }
}
