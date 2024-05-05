<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Entity;

use App\Core\Domain\Model\ValueObject\Id;
use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\Exception\UserException;
use App\Security\Domain\Model\ValueObject\Email;
use App\Security\Domain\Model\ValueObject\Password;

class User
{
    public function __construct(
        private readonly Id $id,
        private readonly Email $email,
        private readonly Password $password,
        private Status $status
    ) {
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function isActive(): bool
    {
        return $this->status === Status::Active;
    }

    public function confirm(): void
    {
        if ($this->status === Status::Active) {
            throw UserException::alreadyActive($this);
        }

        $this->status = Status::Active;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}
