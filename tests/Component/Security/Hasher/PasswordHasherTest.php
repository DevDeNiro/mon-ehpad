<?php

declare(strict_types=1);

namespace Tests\Component\Hasher;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Id;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\ValueObject\PlainPassword;
use App\Security\Infrastructure\Hasher\DefaultPasswordHasher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[CoversClass(DefaultPasswordHasher::class)]
final class PasswordHasherTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    #[Test]
    public function shouldHashAndVerifyPasswordSuccessfully(): void
    {
        $container = static::getContainer();

        /** @var UserPasswordHasherInterface $userPasswordHasher */
        $userPasswordHasher = $container->get(UserPasswordHasherInterface::class);

        $defaultPasswordHasher = new DefaultPasswordHasher($userPasswordHasher);

        $plainPassword = PlainPassword::create('Password123!');

        self::assertTrue(
            $defaultPasswordHasher->verify(
                $plainPassword,
                new User(
                    Id::generate(),
                    Email::create('user@email.com'),
                    $defaultPasswordHasher->hash($plainPassword),
                    Status::Active
                )
            )
        );
    }
}
