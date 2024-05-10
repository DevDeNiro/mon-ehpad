<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\SignUp;

use App\Core\Domain\Application\CQRS\Message\Command;
use App\Core\Domain\Model\ValueObject\Email;
use App\Security\Domain\Model\ValueObject\PlainPassword;
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

    public function email(): Email
    {
        return Email::fromString($this->email);
    }

    public function plainPassword(): PlainPassword
    {
        return PlainPassword::fromString($this->password);
    }
}
