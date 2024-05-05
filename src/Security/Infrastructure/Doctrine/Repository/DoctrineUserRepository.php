<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\Repository;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Id;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\Exception\UserException;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Infrastructure\Doctrine\Entity\DoctrineUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
final class DoctrineUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, DoctrineUser::class);
    }

    public function isAlreadyUsed(Email|string $email): bool
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.email = :email')
            ->setParameter('email', (string) $email)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function findByEmail(Email $email): User
    {
        /** @var DoctrineUser|null $doctrineUser */
        $doctrineUser = $this->findOneBy(['email' => (string) $email]);

        if ($doctrineUser === null) {
            throw UserException::emailNotFound($email);
        }

        return $this->hydrateUserFromDoctrineEntity($doctrineUser);
    }

    public function findById(Id $id): User
    {
        /** @var DoctrineUser|null $doctrineUser */
        $doctrineUser = $this->find((string) $id);

        if ($doctrineUser === null) {
            throw UserException::idNotFound($id);
        }

        return $this->hydrateUserFromDoctrineEntity($doctrineUser);
    }

    public function insert(User $user): void
    {
        $this->getEntityManager()->persist(DoctrineUser::fromUser($user));
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    public function save(User $user): void
    {
        /** @var DoctrineUser|null $doctrineUser */
        $doctrineUser = $this->find((string) $user->getId());

        if ($doctrineUser === null) {
            throw UserException::idNotFound($user->getId());
        }

        $doctrineUser->status = $user->getStatus()->value;

        $this->getEntityManager()->flush();
    }

    private function hydrateUserFromDoctrineEntity(DoctrineUser $doctrineUser): User
    {
        return new User(
            Id::fromUlid($doctrineUser->id),
            Email::create($doctrineUser->email),
            Password::create($doctrineUser->password),
            Status::from($doctrineUser->status)
        );
    }
}
