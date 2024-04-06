<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Hasher;

use App\Security\Domain\Hasher\PasswordHasherInterface;
use App\Security\Domain\ValueObject\Password;
use App\Security\Domain\ValueObject\PlainPassword;
use App\Security\Infrastructure\Symfony\Security\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class PasswordHasher implements PasswordHasherInterface
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function hash(PlainPassword $plainPassword): Password
    {
        $symfonyUser = new User();

        return Password::create($this->userPasswordHasher->hashPassword($symfonyUser, $plainPassword->value()));
    }

    public function verify(PlainPassword $plainPassword, Password $password): bool
    {
        return $this->userPasswordHasher->isPasswordValid(new User($password), $plainPassword->value());
    }
}
