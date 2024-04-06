<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Symfony\Security;

use App\Security\Domain\ValueObject\Password;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final readonly class User implements PasswordAuthenticatedUserInterface
{
    public function __construct(private ?Password $password = null)
    {
    }

    public function getPassword(): ?string
    {
        return null !== $this->password ? (string) $this->password : null;
    }
}
