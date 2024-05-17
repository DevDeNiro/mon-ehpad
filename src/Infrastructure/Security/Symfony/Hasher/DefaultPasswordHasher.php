<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Symfony\Hasher;

use App\Application\Config\Hasher\PasswordHasher;
use App\Domain\User\Model\User;
use App\Infrastructure\Security\Symfony\Security\SymfonyUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final readonly class DefaultPasswordHasher implements PasswordHasher
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    public function hash(string $plainPassword): string
    {
        return $this->userPasswordHasher->hashPassword(
            new class() implements PasswordAuthenticatedUserInterface {
                /**
                 * @codeCoverageIgnore
                 */
                public function getPassword(): ?string
                {
                    return null;
                }
            },
            $plainPassword
        );
    }

    public function verify(string $plainPassword, User $user): bool
    {
        return $this->userPasswordHasher->isPasswordValid(new SymfonyUser($user), $plainPassword);
    }
}
