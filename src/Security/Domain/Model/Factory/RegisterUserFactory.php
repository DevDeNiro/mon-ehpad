<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Factory;

use App\Core\Domain\Model\Factory\Factory;
use App\Core\Domain\Model\ValueObject\Email;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\ValueObject\Password;

final class RegisterUserFactory extends Factory
{
    private Email $email;

    private Password $password;

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

    public function build(): User
    {
        $user = User::register($this->email, $this->password);
        $user->setEventBus($this->eventBus);

        return $user;
    }
}
