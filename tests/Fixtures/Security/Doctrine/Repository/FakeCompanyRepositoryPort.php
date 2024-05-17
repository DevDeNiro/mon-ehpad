<?php

declare(strict_types=1);

namespace Tests\Fixtures\Security\Doctrine\Repository;

use App\Domain\Company\Model\Company;
use App\Domain\Company\Repository\CompanyRepositoryPort;

final class FakeCompanyRepositoryPort implements CompanyRepositoryPort
{
    /**
     * @var array<string, Company>
     */
    public array $companies = [];

    public function insert(Company $company): void
    {
        $this->companies[$company->getId()->toRfc4122()] = $company;
    }
}
