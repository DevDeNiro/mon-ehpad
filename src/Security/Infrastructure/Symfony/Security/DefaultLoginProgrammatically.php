<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Symfony\Security;

use App\Security\Domain\Application\Security\LoginProgrammatically;
use App\Security\Domain\Model\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class DefaultLoginProgrammatically implements LoginProgrammatically
{
    public function __construct(private Security $security)
    {
    }

    public function login(User $user): void
    {
        $this->security->login(new SymfonyUser($user));
    }
}
