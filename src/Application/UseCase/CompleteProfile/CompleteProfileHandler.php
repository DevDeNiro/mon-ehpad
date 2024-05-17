<?php

declare(strict_types=1);

namespace App\Application\UseCase\CompleteProfile;

use App\Application\CQRS\Handler\CommandHandler;
use App\Domain\Company\Model\Company;
use App\Domain\Company\Repository\CompanyRepositoryPort;

final readonly class CompleteProfileHandler implements CommandHandler
{
    public function __construct(
        private CompanyRepositoryPort $companyRepository
    )
    {
    }

    public function __invoke(CompleteProfileInput $input): void
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
