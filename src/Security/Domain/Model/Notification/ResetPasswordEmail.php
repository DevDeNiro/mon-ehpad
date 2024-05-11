<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Notification;

use App\Core\Domain\Application\Notifier\Notification;
use App\Core\Domain\Validation\Assert;
use App\Security\Domain\Model\Entity\ForgottenPasswordRequest;
use App\Security\Domain\Model\Entity\User;

final readonly class ResetPasswordEmail implements Notification
{
    public function __construct(
        private ForgottenPasswordRequest $forgottenPasswordRequest,
        private string $token
    ) {
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
