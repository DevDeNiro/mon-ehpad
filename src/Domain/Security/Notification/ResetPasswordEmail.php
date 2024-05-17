<?php

declare(strict_types=1);

namespace App\Domain\Security\Notification;

use App\Application\Notifier\Notification;
use App\Domain\Security\Model\ForgottenPasswordRequest;

final readonly class ResetPasswordEmail implements Notification
{
    public function __construct(
        private ForgottenPasswordRequest $forgottenPasswordRequest,
        private string                   $token
    )
    {
    }

    public function getSubject(): string
    {
        return 'Réinitialisez votre mot de passe !';
    }

    public function getRecipient(): string
    {
        return $this->forgottenPasswordRequest->getUser()->getEmail();
    }

    /**
     * @return array{token: string}
     */
    public function getContext(): array
    {
        return [
            'token' => $this->token,
        ];
    }

    public function getContent(): null
    {
        return null;
    }

    public function getTemplate(): string
    {
        return 'emails/reset_password.mjml.twig';
    }
}
