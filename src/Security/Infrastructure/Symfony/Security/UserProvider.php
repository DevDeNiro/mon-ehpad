<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Symfony\Security;

use App\Security\Domain\Application\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<SymfonyUser>
 */
final readonly class UserProvider implements UserProviderInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function refreshUser(UserInterface $user): SymfonyUser
    {
        if (!$user instanceof SymfonyUser) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === SymfonyUser::class;
    }

    public function loadUserByIdentifier(string $identifier): SymfonyUser
    {
        $user = $this->userRepository->findOneByEmail($identifier);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return new SymfonyUser($user);
    }
}
