<?php

declare(strict_types=1);

namespace Tests\Component\Security\Doctrine\Repository;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Identifier;
use App\Security\Domain\Model\Entity\Status;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Domain\Port\Repository\Exception\UserNotFoundException;
use App\Security\Domain\Port\Repository\UserRepository;
use App\Security\Infrastructure\Doctrine\DataFixtures\UserFixtures;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $userRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $container = self::getContainer();
        $this->userRepository = $container->get(UserRepository::class);
    }

    #[Test]
    public function shouldInsertUser(): void
    {
        $user = new User(
            Identifier::generate(),
            Email::create('user@email.com'),
            Password::create('password'),
            Status::WaitingForConfirmation
        );

        $this->userRepository->insert($user);
        self::assertTrue($this->userRepository->isAlreadyUsed(Email::create('user@email.com')));
    }

    #[Test]
    public function shouldRetrieveUserByEmail(): void
    {
        $user = $this->userRepository->findByEmail(Email::create('admin+1@email.com'));
        self::assertSame('admin+1@email.com', $user->email()->value());
    }

    #[Test]
    public function shouldRaiseExceptionWhenRetrievingUserByEmailDueToNonExistingEmail(): void
    {
        self::expectException(UserNotFoundException::class);
        self::expectExceptionMessage('User (email: fail@email.com) not found');
        $this->userRepository->findByEmail(Email::create('fail@email.com'));
    }

    #[Test]
    public function shouldRetrieveUserById(): void
    {
        $user = $this->userRepository->findById(Identifier::fromString(UserFixtures::ADMIN_ID));
        self::assertSame('admin+1@email.com', $user->email()->value());
    }

    #[Test]
    public function shouldRaiseExceptionWhenRetrievingUserByIdDueToNonExistingId(): void
    {
        $identifier = Identifier::generate();
        self::expectException(UserNotFoundException::class);
        self::expectExceptionMessage(sprintf('User (id: %s) not found', $identifier));
        $this->userRepository->findById($identifier);
    }

    #[Test]
    public function shouldSaveUser(): void
    {
        $user = $this->userRepository->findByEmail(Email::create('admin+1@email.com'));
        self::assertFalse($user->isActive());
        $user->confirm();
        $this->userRepository->save($user);
        $user = $this->userRepository->findByEmail(Email::create('admin+1@email.com'));
        self::assertTrue($user->isActive());
    }

    #[Test]
    public function shouldRaiseExceptionWhenSavingUserDueToNonExistingId(): void
    {
        $identifier = Identifier::generate();
        $user = new User(
            $identifier,
            Email::create('user@email.com'),
            Password::create('password'),
            Status::WaitingForConfirmation
        );
        self::expectException(UserNotFoundException::class);
        self::expectExceptionMessage(sprintf('User (id: %s) not found', $identifier));
        $this->userRepository->save($user);
    }

    #[Test]
    public function isAlreadyRegistered(): void
    {
        self::assertTrue($this->userRepository->isAlreadyUsed(Email::create('admin+1@email.com')));

        self::assertFalse($this->userRepository->isAlreadyUsed(Email::create('user@email.com')));
    }
}
