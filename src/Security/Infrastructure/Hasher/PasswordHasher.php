<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Hasher;

use App\Core\Domain\Model\ValueObject\Email;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Domain\Model\ValueObject\PlainPassword;
use App\Security\Domain\Port\Hasher\PasswordHasherInterface;
use App\Security\Infrastructure\Symfony\Security\User as SymfonyUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class PasswordHasher implements PasswordHasherInterface
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function hash(PlainPassword $plainPassword): Password
    {
        $symfonyUser = SymfonyUser::create(
            User::register(
                Email::create('fake@email.com'),
                Password::create('fake_password')
            )
        );

        return Password::create($this->userPasswordHasher->hashPassword($symfonyUser, $plainPassword->value()));
    }

    public function verify(PlainPassword $plainPassword, User $user): bool
    {
        return $this->userPasswordHasher->isPasswordValid(SymfonyUser::create($user), $plainPassword->value());
    }
}
