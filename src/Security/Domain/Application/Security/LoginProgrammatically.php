<?php

declare(strict_types=1);

namespace App\Security\Domain\Application\Security;

use App\Security\Domain\Model\Entity\User;

interface LoginProgrammatically
{
    public function login(User $user): void;
}
