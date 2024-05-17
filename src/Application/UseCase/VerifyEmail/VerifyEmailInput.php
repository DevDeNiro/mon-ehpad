<?php

declare(strict_types=1);

namespace App\Application\UseCase\VerifyEmail;

use App\Application\CQRS\Message\Command;
use App\Domain\User\Model\User;
use Symfony\Component\Validator\Constraints as Assert;

final class VerifyEmailInput implements Command
{
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^\d{6}$/')]
    public string $code;

    public User $user;
}
