<?php

declare(strict_types=1);

namespace App\Security\Domain\Application\Repository;

use App\Security\Domain\Model\Entity\Company;

interface CompanyRepository
{
    public function insert(Company $company): void;
}
