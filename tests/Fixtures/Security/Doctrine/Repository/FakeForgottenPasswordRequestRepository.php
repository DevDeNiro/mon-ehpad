<?php

declare(strict_types=1);

namespace Tests\Fixtures\Security\Doctrine\Repository;

use App\Domain\Security\Model\ForgottenPasswordRequest;
use App\Domain\Security\Repository\ForgottenPasswordRequestRepository;
use App\Domain\User\Model\User;

final class FakeForgottenPasswordRequestRepository implements ForgottenPasswordRequestRepository
{
    /**
     * @var array<string, ForgottenPasswordRequest>
     */
    public array $forgottenPasswordRequests = [];

    public function insert(ForgottenPasswordRequest $forgottenPasswordRequest): void
    {
        $this->forgottenPasswordRequests[$forgottenPasswordRequest->getUser()->getEmail()] = $forgottenPasswordRequest;
    }

    public function remove(ForgottenPasswordRequest $forgottenPasswordToken): void
    {
        unset($this->forgottenPasswordRequests[$forgottenPasswordToken->getUser()->getEmail()]);
    }

    public function findOneByUser(User $user): ?ForgottenPasswordRequest
    {
        return $this->forgottenPasswordRequests[$user->getEmail()] ?? null;
    }
}
