<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Symfony\Security;

use App\Domain\User\Repository\UserRepositoryPort;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<SymfonyUser>
 */
final readonly class UserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepositoryPort $userRepository
    )
    {
    }

    public function refreshUser(UserInterface $user): SymfonyUser
    {
        if (!$user instanceof SymfonyUser) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function loadUserByIdentifier(string $identifier): SymfonyUser
    {
        $user = $this->userRepository->findOneByEmail($identifier);

        if ($user === null) {
            throw new UserNotFoundException();
        }

        return new SymfonyUser($user);
    }

    public function supportsClass(string $class): bool
    {
        return $class === SymfonyUser::class;
    }
}
