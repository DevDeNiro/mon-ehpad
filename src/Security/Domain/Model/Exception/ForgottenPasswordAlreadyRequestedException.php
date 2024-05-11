<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Exception;

use App\Core\Domain\Model\Exception\DomainException;
use App\Security\Domain\Model\Entity\ForgottenPasswordRequest;

/**
 * @extends DomainException<array{forgotten_password_request: ForgottenPasswordRequest}>
 */
final class ForgottenPasswordAlreadyRequestedException extends DomainException
{
    public function __construct(ForgottenPasswordRequest $forgottenPasswordRequest)
    {
        parent::__construct(
            "Une demande de réinitialisation de mot de passe a déjà été effectuée pour l'utilisateur",
            [
                'forgotten_password_request' => $forgottenPasswordRequest,
            ]
        );
    }
}
