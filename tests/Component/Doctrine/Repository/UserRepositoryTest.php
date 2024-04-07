<?php

declare(strict_types=1);

namespace Tests\Component\Doctrine\Repository;

use App\Core\Domain\ValueObject\Email;
use App\Core\Infrastructure\Doctrine\Repository\UserDoctrineRepository;
use App\Security\Domain\Entity\User;
use App\Security\Domain\Repository\UserRepository;
use App\Security\Domain\ValueObject\Password;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UserRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testRegister(): void
    {
        $container = static::getContainer();

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserDoctrineRepository::class);

        $user = User::register(
            Email::create('user@email.com'),
            Password::create('password')
        );

        $userRepository->register($user);

        self::assertTrue($userRepository->isAlreadyUsed(Email::create('user@email.com')));
    }

    public function testFindByEmail(): void
    {
        $container = static::getContainer();

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserDoctrineRepository::class);

        $user = $userRepository->findByEmail(Email::create('admin+1@email.com'));

        self::assertInstanceOf(User::class, $user);
    }

    public function testConfirm(): void
    {
        $container = static::getContainer();

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserDoctrineRepository::class);

        /** @var User $user */
        $user = $userRepository->findByEmail(Email::create('admin+1@email.com'));

        $user->confirm();

        $userRepository->confirm($user);

        /** @var User $user */
        $user = $userRepository->findByEmail(Email::create('admin+1@email.com'));

        self::assertTrue($user->isActive());
    }

    public function testIsAlreadyRegistered(): void
    {
        $container = static::getContainer();

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserDoctrineRepository::class);

        self::assertTrue($userRepository->isAlreadyUsed(Email::create('admin+1@email.com')));

        self::assertFalse($userRepository->isAlreadyUsed(Email::create('user@email.com')));
    }
}
