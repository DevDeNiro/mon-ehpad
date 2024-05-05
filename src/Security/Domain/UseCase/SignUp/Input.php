<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\SignUp;

use App\Core\Domain\UseCase\Command;
use App\Security\Domain\Validation\Validator\UniqueEmail;
use Symfony\Component\Validator\Constraints as Assert;

final class Input implements Command
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[UniqueEmail]
    public string $email;

    #[Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_WEAK)]
    #[Assert\NotCompromisedPassword]
    public string $password;
}
