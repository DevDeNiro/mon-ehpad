<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Alice\Processor;

use App\Security\Domain\Application\Repository\CompanyRepository;
use App\Security\Domain\Model\Entity\User;
use Fidry\AliceDataFixtures\ProcessorInterface;

final readonly class CompanyProcessor implements ProcessorInterface
{
    public function __construct(private CompanyRepository $companyRepository)
    {
    }

    public function preProcess(string $id, object $object): void
    {
        if (!$object instanceof User || $object->getCompany() === null) {
            return;
        }

        $this->companyRepository->insert($object->getCompany());
    }

    public function postProcess(string $id, object $object): void
    {
    }
}
