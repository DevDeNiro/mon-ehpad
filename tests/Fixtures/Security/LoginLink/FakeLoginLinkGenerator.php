<?php

declare(strict_types=1);

namespace Tests\Fixtures\Security\LoginLink;

use App\Core\Domain\ValueObject\Url;
use App\Security\Domain\Entity\User;
use App\Security\Domain\LoginLink\LoginLinkGeneratorInterface;

final class FakeLoginLinkGenerator implements LoginLinkGeneratorInterface
{
    public function generate(User $user): Url
    {
        return Url::create(sprintf('http://localhost/login_check?user=%s', $user->email()->value()));
    }
}
