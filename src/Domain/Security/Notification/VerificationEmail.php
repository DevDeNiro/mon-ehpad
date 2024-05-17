<?php

declare(strict_types=1);

namespace App\Domain\Security\Notification;

use App\Application\Notifier\Notification;
use App\Domain\core\Validation\Assert;
use App\Domain\User\Model\User;

final readonly class VerificationEmail implements Notification
{
    public function __construct(
        private User $user
    )
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

        return [
            'otp' => $this->user->getVerificationCode()->getCode(),
        ];
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
