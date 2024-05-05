<?php

declare(strict_types=1);

namespace Tests\Component\Security\Symfony\Security;

use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\ValueObject\Email;
use App\Security\Infrastructure\Symfony\Security\SymfonyUser;
use App\Security\Infrastructure\Symfony\Security\UserProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

#[CoversClass(UserProvider::class)]
final class UserProviderTest extends KernelTestCase
{
    private UserProvider $userProvider;

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->userRepository = self::getContainer()->get(UserRepository::class);

        $this->userProvider = new UserProvider($this->userRepository);
    }

    #[Test]
    public function shouldSupportClass(): void
    {
        self::assertTrue($this->userProvider->supportsClass(SymfonyUser::class));
    }

    #[Test]
    public function shouldRefreshUser(): void
    {
        $user = $this->userRepository->findByEmail(Email::fromString('admin+1@email.com'));
        $symfonyUser = $this->userProvider->refreshUser(new SymfonyUser($user));
        self::assertEquals($symfonyUser->getUserIdentifier(), 'admin+1@email.com');
    }

    #[Test]
    public function shouldRaiseUnsupportedUserExceptionWhenRefreshingUser(): void
    {
        self::expectException(UnsupportedUserException::class);
        $this->userProvider->refreshUser(new class() implements UserInterface {
            public function getRoles(): array
            {
                return [];
            }

            public function eraseCredentials(): void
            {
            }

            public function getUserIdentifier(): string
            {
                return 'user@email.com';
            }
        });
    }
}
