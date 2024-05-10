<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Exception;

use App\Core\Domain\Model\Exception\DomainException;
use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Id;
use App\Security\Domain\Model\Entity\User;

final class UserException extends DomainException
{
    public static function alreadyActive(User $user): self
    {
        return new self(
            sprintf(
                'L\'utilisateur (id: %s) est déjà actif.',
                $user->getId(),
            ),
            ['user' => $user]
        );
    }

    public static function emailNotFound(Email $email): self
    {
        return new self(
            sprintf(
                'L\'utilisateur (email: %s) n\'existe pas.',
                $email,
            ),
            ['email' => $email]
        );
    }

    public static function idNotFound(Id $id): self
    {
        return new self(
            sprintf(
                'L\'utilisateur (id: %s) n\'existe pas.',
                $id,
            ),
            ['id' => $id]
        );
    }
}
