<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use App\Domain\core\Model\Exception\DomainException;
use App\Domain\User\Model\User;

/**
 * @extends DomainException<array{user: User}>
 */
final class InvalidStateException extends DomainException
{
    public static function alreadyVerified(User $user): self
    {
        return new self(
            sprintf(
                'L\'utilisateur (id: %s) est déjà vérifié.',
                $user->getId(),
            ),
            [
                'user' => $user,
            ]
        );
    }

    public static function notVerified(User $user): self
    {
        return new self(
            sprintf(
                'L\'utilisateur (id: %s) n\'est pas vérifié.',
                $user->getId(),
            ),
            [
                'user' => $user,
            ]
        );
    }

    public static function noVerificationCode(User $user): self
    {
        return new self(
            sprintf(
                'L\'utilisateur (id: %s) n\'a pas de code de vérification.',
                $user->getId(),
            ),
            [
                'user' => $user,
            ]
        );
    }

    public static function verificationCodeExpired(User $user): self
    {
        return new self(
            sprintf(
                'Le code de vérification de l\'utilisateur (id: %s) a expiré.',
                $user->getId(),
            ),
            [
                'user' => $user,
            ]
        );
    }

    public static function invalidVerificationCode(User $user, string $verificationCode): self
    {
        return new self(
            sprintf(
                'Le code de vérification de l\'utilisateur (id: %s) est invalide.',
                $user->getId(),
            ),
            [
                'user' => $user,
                'verificationCode' => $verificationCode,
            ]
        );
    }
}
