<?php

declare(strict_types=1);

namespace App\Security\Domain\LoginLink;

use App\Core\Domain\ValueObject\Url;
use App\Security\Domain\Entity\User;

interface LoginLinkGeneratorInterface
{
    public function generate(User $user): Url;
}
