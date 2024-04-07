<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Entity;

use App\Core\Domain\Model\Entity\EventsInterface;
use App\Core\Domain\Model\Entity\EventTrait;
use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Identifier;
use App\Security\Domain\Model\Event\UserRegistered;
use App\Security\Domain\Model\Event\UserRegistrationConfirmed;
use App\Security\Domain\Model\ValueObject\Password;

final class User implements EventsInterface
{
    use EventTrait;

    private function __construct(
        private readonly Identifier $id,
        private readonly Email $email,
        private readonly Password $password,
        private Status $status = Status::WaitingForConfirmation
    ) {
    }

    public static function create(Identifier $id, Email $email, Password $password, Status $status): self
    {
        return new self($id, $email, $password, $status);
    }

    public static function register(Email $email, Password $password): self
    {
        $user = new self(Identifier::generate(), $email, $password);
        $user->dispatch(UserRegistered::create($user->id));

        return $user;
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
        $this->dispatch(UserRegistrationConfirmed::create($this->id));
    }

    public function status(): Status
    {
        return $this->status;
    }
}
