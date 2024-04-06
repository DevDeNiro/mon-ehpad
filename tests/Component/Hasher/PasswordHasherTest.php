<?php

declare(strict_types=1);

namespace Hasher;

use App\Security\Domain\Hasher\PasswordHasherInterface;
use App\Security\Domain\ValueObject\PlainPassword;
use App\Security\Infrastructure\Hasher\PasswordHasher;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PasswordHasherTest extends WebTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testShouldHashAndVerifyPasswordSuccessfully(): void
    {
        $container = static::getContainer();

        /** @var PasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get(PasswordHasher::class);

        $plainPassword = PlainPassword::create('password');

        self::assertTrue(
            $passwordHasher->verify(
                $plainPassword,
                $plainPassword->hash($passwordHasher)
            )
        );
    }
}
