<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\ORM\Repository;

use App\Core\Domain\Model\ValueObject\Email;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\Entity\User;
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
        parent::__construct($managerRegistry, User::class);
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

    public function insert(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function save(User $user): void
    {
        $this->getEntityManager()->flush();
    }
}
