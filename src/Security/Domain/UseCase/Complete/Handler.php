<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\Complete;

use App\Core\Domain\Application\CQRS\Handler\CommandHandler;
use App\Security\Domain\Application\Repository\CompanyRepository;
use App\Security\Domain\Model\Entity\Company;

final readonly class Handler implements CommandHandler
{
    public function __construct(
        private CompanyRepository $companyRepository
    ) {
    }

    public function __invoke(Input $input): void
    {
        $user = $input->user;

        $user->complete(
            $input->firstName,
            $input->lastName,
            $input->phoneNumber,
            $input->companyName
        );

        /** @var Company $company */
        $company = $user->getCompany();

        $this->companyRepository->insert($company);
    }
}
