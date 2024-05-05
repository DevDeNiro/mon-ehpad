<?php

declare(strict_types=1);

namespace Tests\Component\Security\Doctrine\Repository;

use App\Core\Domain\Model\ValueObject\Id;
use App\Core\Infrastructure\Alice\Factory\IdFactory;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\Exception\UserException;
use App\Security\Domain\Model\ValueObject\Email;
use App\Security\Domain\Model\ValueObject\Password;
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
    public function isAlreadyRegistered(): void
    {
        self::assertTrue($this->userRepository->isAlreadyUsed(Email::fromString('admin+1@email.com')));

        self::assertFalse($this->userRepository->isAlreadyUsed(Email::fromString('user@email.com')));
    }
}
