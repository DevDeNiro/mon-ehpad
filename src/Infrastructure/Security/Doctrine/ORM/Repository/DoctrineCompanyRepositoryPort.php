<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Doctrine\ORM\Repository;

use App\Domain\Company\Model\Company;
use App\Domain\Company\Repository\CompanyRepositoryPort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 */
final class DoctrineCompanyRepositoryPort extends ServiceEntityRepository implements CompanyRepositoryPort
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Company::class);
    }

    public function insert(Company $company): void
    {
        $this->getEntityManager()->persist($company);
    }
}
