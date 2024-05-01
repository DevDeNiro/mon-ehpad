<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Entity;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Identifier;
use App\Security\Domain\Model\ValueObject\Password;

final class User
{
    public function __construct(
        private readonly Identifier $identifier,
        private readonly Email $email,
        private readonly Password $password,
        private Status $status
    ) {
    }

    public function id(): Identifier
    {
        return $this->identifier;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): Password
    {
        return $this->password;
    }

    public function isActive(): bool
    {
        return $this->status === Status::Active;
    }

    public function isWaitingForConfirmation(): bool
    {
        return $this->status === Status::WaitingForConfirmation;
    }

    public function confirm(): void
    {
        if ($this->status === Status::Active) {
            throw new \DomainException(sprintf('User (id: %s) is already active.', $this->identifier));
        }

        $this->status = Status::Active;
    }

    public function status(): Status
    {
        return $this->status;
    }
}
