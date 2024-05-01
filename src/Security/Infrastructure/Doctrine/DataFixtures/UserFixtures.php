<?php

namespace App\Security\Infrastructure\Doctrine\DataFixtures;

use App\Security\Domain\Model\Entity\Status;
use App\Security\Infrastructure\Doctrine\Entity\DoctrineUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Ulid;

final class UserFixtures extends Fixture
{
    public const string ADMIN_ID = '01HWT18J8WJ0YMSZ0S98KY0AHJ';

    #[\Override]
    public function load(ObjectManager $objectManager): void
    {
        $doctrineUser = $this->createNewUser();
        $objectManager->persist($doctrineUser);
        $objectManager->flush();
    }

    private function createNewUser(string $email = 'admin+1@email.com', string $password = 'Password123!'): DoctrineUser
    {
        $doctrineUser = new DoctrineUser();
        $doctrineUser->id = new Ulid(self::ADMIN_ID);
        $doctrineUser->email = $email;
        $doctrineUser->password = $password;
        $doctrineUser->status = Status::WaitingForConfirmation->value;

        return $doctrineUser;
    }
}
