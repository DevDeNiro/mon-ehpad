<?php

declare(strict_types=1);

namespace App\Application\UseCase\RequestForgottenPassword;

use App\Application\CQRS\Message\Command;
use Symfony\Component\Validator\Constraints as Assert;

final class RequestForgottenPasswordInput implements Command
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;
}
