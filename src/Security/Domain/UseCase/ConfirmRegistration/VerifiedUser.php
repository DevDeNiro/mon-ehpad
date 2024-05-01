<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\ConfirmRegistration;

use App\Core\Domain\CQRS\Command;
use App\Core\Domain\Model\ValueObject\Email;

final readonly class VerifiedUser implements Command
{
    public function __construct(
        private Email $email
    ) {
    }

    public function email(): Email
    {
        return $this->email;
    }
}
