<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Doctrine\Repository;

use App\Core\Domain\ValueObject\Email;
use App\Core\Domain\ValueObject\Identifier;
use App\Core\Infrastructure\Doctrine\Entity\User as DoctrineUser;
use App\Security\Domain\Entity\Status;
use App\Security\Domain\Entity\User;
use App\Security\Domain\Repository\UserRepository;
use App\Security\Domain\ValueObject\Password;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserDoctrineRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DoctrineUser::class);
    }

    public function register(User $user): void
    {
        $this->getEntityManager()->persist(DoctrineUser::fromSecurityUser($user));
        $this->getEntityManager()->flush();
    }

    public function isAlreadyUsed(Email $email): bool
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.email = :email')
            ->setParameter('email', $email->value())
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function findByEmail(Email $email): ?User
    {
        /** @var DoctrineUser|null $doctrineUser */
        $doctrineUser = $this->findOneBy(['email' => $email->value()]);

        return null === $doctrineUser ? null : User::create(
            Identifier::fromUlid($doctrineUser->id),
            Email::create($doctrineUser->email),
            Password::create($doctrineUser->password),
            Status::from($doctrineUser->status)
        );
    }

    public function confirm(User $user): void
    {
        /** @var DoctrineUser|null $doctrineUser */
        $doctrineUser = $this->findOneBy(['email' => $user->email()->value()]);

        if (null === $doctrineUser) {
            throw new \RuntimeException('User not found');
        }

        $doctrineUser->status = $user->status()->value;

        $this->getEntityManager()->flush();
    }
}
