<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Symfony\Security;

use App\Security\Domain\Model\Entity\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class SymfonyUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private User $user
    ) {
    }

    public function user(): User
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->user->getPassword()->value();
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->user->getEmail()->value();
    }
}
