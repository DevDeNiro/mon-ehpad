<?php

declare(strict_types=1);

namespace Tests\Fixtures\Security\Application\Security;

use App\Security\Domain\Application\Security\LoginProgrammatically;
use App\Security\Domain\Model\Entity\User;

final class FakeLoginProgrammatically implements LoginProgrammatically
{
    public bool $logged = false;

    public function login(User $user): void
    {
        $this->logged = true;
    }
}
