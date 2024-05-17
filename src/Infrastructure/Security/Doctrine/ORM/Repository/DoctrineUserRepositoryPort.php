<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Doctrine\ORM\Repository;

use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepositoryPort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
final class DoctrineUserRepositoryPort extends ServiceEntityRepository implements UserRepositoryPort
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, User::class);
    }

    public function isAlreadyUsed(string $email): bool
    {
        return $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function insert(User $user): void
    {
        $this->getEntityManager()->persist($user);
    }
}
