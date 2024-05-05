<?php

declare(strict_types=1);

namespace Tests\Component\Security\Doctrine\Repository;

use App\Core\Domain\Model\ValueObject\Id;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\Exception\UserException;
use App\Security\Domain\Model\ValueObject\Email;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Infrastructure\Doctrine\DataFixtures\UserFixtures;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $userRepository;

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
            new Id(),
            Email::fromString('user@email.com'),
            Password::fromString('password'),
            Status::WaitingForConfirmation
        );

        $this->userRepository->insert($user);
        self::assertTrue($this->userRepository->isAlreadyUsed(Email::fromString('user@email.com')));
    }

    #[Test]
    public function shouldRetrieveUserByEmail(): void
    {
        $user = $this->userRepository->findByEmail(Email::fromString('admin+1@email.com'));
        self::assertSame('admin+1@email.com', $user->getEmail()->value());
    }

    #[Test]
    public function shouldRaiseExceptionWhenRetrievingUserByEmailDueToNonExistingEmail(): void
    {
        self::expectException(UserException::class);
        $this->userRepository->findByEmail(Email::fromString('fail@email.com'));
    }

    #[Test]
    public function shouldRetrieveUserById(): void
    {
        $user = $this->userRepository->findById(Id::fromString(UserFixtures::ADMIN_ID));
        self::assertSame('admin+1@email.com', $user->getEmail()->value());
    }

    #[Test]
    public function shouldRaiseExceptionWhenRetrievingUserByIdDueToNonExistingId(): void
    {
        $identifier = new Id();
        self::expectException(UserException::class);
        $this->userRepository->findById($identifier);
    }

    #[Test]
    public function shouldSaveUser(): void
    {
        $user = $this->userRepository->findByEmail(Email::fromString('admin+1@email.com'));
        self::assertFalse($user->isActive());
        $user->confirm();
        $this->userRepository->save($user);
        $user = $this->userRepository->findByEmail(Email::fromString('admin+1@email.com'));
        self::assertTrue($user->isActive());
    }

    #[Test]
    public function shouldRaiseExceptionWhenSavingUserDueToNonExistingId(): void
    {
        $identifier = new Id();
        $user = new User(
            $identifier,
            Email::fromString('user@email.com'),
            Password::fromString('password'),
            Status::WaitingForConfirmation
        );
        self::expectException(UserException::class);
        $this->userRepository->save($user);
    }

    #[Test]
    public function isAlreadyRegistered(): void
    {
        self::assertTrue($this->userRepository->isAlreadyUsed(Email::fromString('admin+1@email.com')));

        self::assertFalse($this->userRepository->isAlreadyUsed(Email::fromString('user@email.com')));
    }
}
