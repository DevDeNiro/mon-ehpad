<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Symfony\Security;

use App\Core\Domain\ValueObject\Email;
use App\Security\Domain\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as TUser;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<TUser>
 */
final readonly class UserProvider implements UserProviderInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function refreshUser(TUser $user): User
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): User
    {
        $user = $this->userRepository->findByEmail(Email::create($identifier));

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return User::create($user);
    }
}
