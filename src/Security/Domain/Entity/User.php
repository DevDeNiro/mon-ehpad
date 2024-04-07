<?php

declare(strict_types=1);

namespace App\Security\Domain\Entity;

use App\Core\Domain\ValueObject\Email;
use App\Core\Domain\ValueObject\Identifier;
use App\Security\Domain\ValueObject\Password;

final class User
{
    private function __construct(
        private readonly Identifier $id,
        private readonly Email $email,
        private readonly Password $password,
        private Status $status = Status::WaitingForConfirmation
    ) {
    }

    public static function create(Identifier $identifier, Email $email, Password $password, Status $status): self
    {
        return new self($identifier, $email, $password, $status);
    }

    public static function register(Email $email, Password $password): self
    {
        return new self(Identifier::generate(), $email, $password);
    }

    public function id(): Identifier
    {
        return $this->id;
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
        return Status::Active === $this->status;
    }

    public function isWaitingForConfirmation(): bool
    {
        return Status::WaitingForConfirmation === $this->status;
    }

    public function confirm(): void
    {
        if (Status::Active === $this->status) {
            throw new \DomainException('User is already active');
        }

        $this->status = Status::Active;
    }

    public function status(): Status
    {
        return $this->status;
    }
}
