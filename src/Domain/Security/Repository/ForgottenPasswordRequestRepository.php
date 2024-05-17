<?php

declare(strict_types=1);

namespace App\Domain\Security\Repository;

use App\Domain\Security\Model\ForgottenPasswordRequest;
use App\Domain\User\Model\User;

/**
 * @method ForgottenPasswordRequest|null findOneByUser(User $user)
 */
interface ForgottenPasswordRequestRepository
{
    public function insert(ForgottenPasswordRequest $forgottenPasswordRequest): void;

    public function remove(ForgottenPasswordRequest $forgottenPasswordRequest): void;
}
