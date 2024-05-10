<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\ConfirmRegistration;

use App\Core\Domain\Application\CQRS\Message\Command;
use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\OneTimePassword;
use Symfony\Component\Validator\Constraints as Assert;

final class Input implements Command
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[0-9]{6}$/')]
    public string $oneTimePassword;

    public function email(): Email
    {
        return Email::fromString($this->email);
    }

    public function oneTimePassword(): OneTimePassword
    {
        return OneTimePassword::fromString($this->oneTimePassword);
    }
}
