<?php

declare(strict_types=1);

namespace App\Application\UseCase\SignUp;

use App\Application\CQRS\Message\Command;
use App\Domain\User\Validation\Validator\UniqueEmail;
use Symfony\Component\Validator\Constraints as Assert;

final class SignUpInput implements Command
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[UniqueEmail]
    public string $email;

    #[Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_WEAK)]
    #[Assert\NotCompromisedPassword]
    public string $password;
}
