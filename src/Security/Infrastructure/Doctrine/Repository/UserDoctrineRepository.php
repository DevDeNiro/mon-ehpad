<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\Repository;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Identifier;
use App\Security\Domain\Model\Entity\Status;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Domain\Port\Repository\Exception\UserNotFoundException;
use App\Security\Domain\Port\Repository\UserRepository;
use App\Security\Infrastructure\Doctrine\Entity\DoctrineUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserDoctrineRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, DoctrineUser::class);
    }

    #[\Override]
    public function isAlreadyUsed(Email|string $email): bool
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.email = :email')
            ->setParameter('email', (string) $email)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    #[\Override]
    public function findByEmail(Email $email): User
    {
        /** @var DoctrineUser|null $doctrineUser */
        $doctrineUser = $this->findOneBy([
            'email' => (string) $email,
        ]);

        if ($doctrineUser === null) {
            throw new UserNotFoundException(sprintf('User (email: %s) not found', $email));
        }

        return $this->hydrateUserFromDoctrineEntity($doctrineUser);
    }

    #[\Override]
    public function findById(Identifier $identifier): User
    {
        /** @var DoctrineUser|null $doctrineUser */
        $doctrineUser = $this->find((string) $identifier);

        if ($doctrineUser === null) {
            throw new UserNotFoundException(sprintf('User (id: %s) not found', $identifier));
        }

        return $this->hydrateUserFromDoctrineEntity($doctrineUser);
    }

    #[\Override]
    public function insert(User $user): void
    {
        $this->getEntityManager()->persist(DoctrineUser::fromUser($user));
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    #[\Override]
    public function save(User $user): void
    {
        /** @var DoctrineUser|null $doctrineUser */
        $doctrineUser = $this->find((string) $user->id());

        if ($doctrineUser === null) {
            throw new UserNotFoundException(sprintf('User (id: %s) not found', $user->id()));
        }

        $doctrineUser->status = $user->status()->value;

        $this->getEntityManager()->flush();
    }

    private function hydrateUserFromDoctrineEntity(DoctrineUser $doctrineUser): User
    {
        return new User(
            Identifier::fromUlid($doctrineUser->id),
            Email::create($doctrineUser->email),
            Password::create($doctrineUser->password),
            Status::from($doctrineUser->status)
        );
    }
}
