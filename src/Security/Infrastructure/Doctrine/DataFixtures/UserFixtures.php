<?php

namespace App\Security\Infrastructure\Doctrine\DataFixtures;

use App\Security\Domain\Application\Hasher\PasswordHasher;
use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\ValueObject\PlainPassword;
use App\Security\Infrastructure\Doctrine\Entity\DoctrineUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Ulid;

final class UserFixtures extends Fixture
{
    public const string ADMIN_ID = '01HWT18J8WJ0YMSZ0S98KY0AHJ';

    public const string EMAIL_FORMAT = 'admin+%d@email.com';

    public function __construct(private PasswordHasher $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $doctrineUser = $this->createNewUser(1);
        $manager->persist($doctrineUser);

        $manager->flush();
    }

    private function createNewUser(int $index): DoctrineUser
    {
        $doctrineUser = new DoctrineUser();
        $doctrineUser->id = new Ulid(self::ADMIN_ID);
        $doctrineUser->email = sprintf(self::EMAIL_FORMAT, $index);
        $doctrineUser->password = (string) $this->passwordHasher->hash(PlainPassword::create('Password123!'));
        $doctrineUser->status = Status::WaitingForConfirmation->value;

        return $doctrineUser;
    }
}
