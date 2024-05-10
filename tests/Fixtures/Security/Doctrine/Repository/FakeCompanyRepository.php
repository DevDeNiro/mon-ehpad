<?php

declare(strict_types=1);

namespace Tests\Fixtures\Security\Doctrine\Repository;

use App\Security\Domain\Application\Repository\CompanyRepository;
use App\Security\Domain\Model\Entity\Company;

final class FakeCompanyRepository implements CompanyRepository
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
