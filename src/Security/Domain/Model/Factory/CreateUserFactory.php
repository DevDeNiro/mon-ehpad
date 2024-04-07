<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Factory;

use App\Core\Domain\Model\Factory\Factory;
use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Identifier;
use App\Security\Domain\Model\Entity\Status;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\ValueObject\Password;

final class CreateUserFactory extends Factory
{
    private Identifier $id;

    private Email $email;

    private Password $password;

    private Status $status;

    public function withId(Identifier $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withEmail(Email $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function withPassword(Password $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function withStatus(Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function build(): User
    {
        $user = User::create($this->id, $this->email, $this->password, $this->status);
        $user->setEventBus($this->eventBus);

        return $user;
    }
}
