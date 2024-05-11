<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Exception;

use App\Core\Domain\Model\Exception\DomainException;
use Symfony\Component\Uid\Ulid;

/**
 * @extends DomainException<array{id: Ulid}|array{email: string}>
 */
final class UserNotFoundException extends DomainException
{
    public static function idNotFound(Ulid $userId): self
    {
        return new self(
            sprintf(
                "L'utilisateur (id: %s) n'existe pas.",
                $userId,
            ),
            [
                'id' => $userId,
            ]
        );
    }

    public static function emailNotFound(string $email): self
    {
        return new self(
            sprintf(
                "L'utilisateur (email: %s) n'existe pas.",
                $email,
            ),
            [
                'email' => $email,
            ]
        );
    }
}
