<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\VerifyEmail;

use App\Core\Domain\Application\CQRS\Message\Command;
use App\Security\Domain\Model\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

final class Input implements Command
{
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[0-9]{6}$/')]
    public string $code;

    public User $user;
}
