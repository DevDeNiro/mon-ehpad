<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Alice\Processor;

use App\Domain\Company\Model\Company;
use App\Domain\Company\Repository\CompanyRepositoryPort;
use App\Domain\User\Model\User;
use Fidry\AliceDataFixtures\ProcessorInterface;

final readonly class CompanyProcessor implements ProcessorInterface
{
    public function __construct(
        private CompanyRepositoryPort $companyRepository
    )
    {
    }

    public function preProcess(string $id, object $object): void
    {
        if (!$object instanceof User || !$object->getCompany() instanceof Company) {
            return;
        }

        $this->companyRepository->insert($object->getCompany());
    }

    public function postProcess(string $id, object $object): void
    {
    }
}
