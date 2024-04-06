<?php

namespace App\Core\Infrastructure\Doctrine\DataFixtures;

use App\Security\Domain\UseCase\SignUp\NewUser;
use App\Security\Domain\UseCase\SignUp\SignUp;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixtures extends Fixture
{
    public function __construct(private readonly SignUp $signUp)
    {
    }

    public function load(ObjectManager $manager): void
    {
        ($this->signUp)(self::createNewUser());
    }

    private static function createNewUser(
        string $email = 'admin+1@email.com',
        string $password = 'password'
    ): NewUser {
        $newUser = new NewUser();
        $newUser->email = $email;
        $newUser->password = $password;

        return $newUser;
    }
}
