<?php

namespace App\Core\Infrastructure\Doctrine\DataFixtures;

use App\Security\Domain\UseCase\SignUp\NewUser;
use App\Security\Domain\UseCase\SignUp\SignUp;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class UserFixtures extends Fixture
{
    public function __construct(private readonly SignUp $signUp, private readonly RequestStack $requestStack)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->requestStack->push(Request::create('http://localhost'));
        ($this->signUp)(self::createNewUser());
    }

    private static function createNewUser(
        string $email = 'admin+1@email.com',
        string $password = 'Password123!'
    ): NewUser {
        $newUser = new NewUser();
        $newUser->email = $email;
        $newUser->password = $password;

        return $newUser;
    }
}
