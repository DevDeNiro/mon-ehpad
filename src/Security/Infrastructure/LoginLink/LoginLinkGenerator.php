<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\LoginLink;

use App\Core\Domain\Model\ValueObject\Url;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Port\LoginLink\LoginLinkGeneratorInterface;
use App\Security\Infrastructure\Symfony\Security\User as SymfonyUser;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

final readonly class LoginLinkGenerator implements LoginLinkGeneratorInterface
{
    public function __construct(
        private LoginLinkHandlerInterface $loginLinkHandler
    ) {
    }

    public function generate(User $user): Url
    {
        return Url::create($this->loginLinkHandler->createLoginLink(SymfonyUser::create($user))->getUrl());
    }
}
