<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Symfony\Security;

use App\Core\Domain\Model\ValueObject\Email;
use App\Security\Domain\Port\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<SymfonyUser>
 */
final readonly class UserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    #[\Override]
    public function refreshUser(UserInterface $user): SymfonyUser
    {
        if (! $user instanceof SymfonyUser) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    #[\Override]
    public function supportsClass(string $class): bool
    {
        return $class === SymfonyUser::class;
    }

    #[\Override]
    public function loadUserByIdentifier(string $identifier): SymfonyUser
    {
        $user = $this->userRepository->findByEmail(Email::create($identifier));

        return new SymfonyUser($user);
    }
}
