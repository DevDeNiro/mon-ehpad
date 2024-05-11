<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\ORM\Repository;

use App\Security\Domain\Application\Repository\ForgottenPasswordRequestRepository;
use App\Security\Domain\Model\Entity\ForgottenPasswordRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForgottenPasswordRequest>
 */
final class DoctrineForgottenPasswordRequestRepository extends ServiceEntityRepository implements ForgottenPasswordRequestRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, ForgottenPasswordRequest::class);
    }

    public function insert(ForgottenPasswordRequest $forgottenPasswordRequest): void
    {
        $this->getEntityManager()->persist($forgottenPasswordRequest);
    }

    public function remove(ForgottenPasswordRequest $forgottenPasswordRequest): void
    {
        $this->getEntityManager()->remove($forgottenPasswordRequest);
        $this->getEntityManager()->flush();
    }
}
