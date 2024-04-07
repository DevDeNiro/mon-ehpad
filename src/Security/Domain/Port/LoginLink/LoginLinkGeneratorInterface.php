<?php

declare(strict_types=1);

namespace App\Security\Domain\Port\LoginLink;

use App\Core\Domain\Model\ValueObject\Url;
use App\Security\Domain\Model\Entity\User;

interface LoginLinkGeneratorInterface
{
    public function generate(User $user): Url;
}
