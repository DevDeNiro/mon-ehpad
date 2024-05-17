<?php

declare(strict_types=1);

namespace App\Domain\Company\Repository;

use App\Domain\Company\Model\Company;

interface CompanyRepositoryPort
{
    public function insert(Company $company): void;
}
