<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Application\Hasher;

use App\Security\Domain\Application\Hasher\PasswordHasher;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Domain\Model\ValueObject\PlainPassword;
use App\Security\Infrastructure\Symfony\Security\SymfonyUser;
use PHPUnit\Framework\Attributes\CodeCoverageIgnore;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final readonly class DefaultPasswordHasher implements PasswordHasher
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function hash(PlainPassword $plainPassword): Password
    {
        return Password::fromString(
            $this->userPasswordHasher->hashPassword(
                new class() implements PasswordAuthenticatedUserInterface {
                    /**
                     * @codeCoverageIgnore
                     */
                    public function getPassword(): ?string
                    {
                        return null;
                    }
                },
                $plainPassword->value()
            )
        );
    }

    public function verify(PlainPassword $plainPassword, User $user): bool
    {
        return $this->userPasswordHasher->isPasswordValid(new SymfonyUser($user), $plainPassword->value());
    }
}
