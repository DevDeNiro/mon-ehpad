<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Symfony\Security;

use App\Security\Domain\Entity\User as DomainUser;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private function __construct(private DomainUser $user)
    {
    }

    public static function create(DomainUser $user): self
    {
        return new self($user);
    }

    public function user(): DomainUser
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->user->password()->value();
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
        return $this->user->email()->value();
    }
}
