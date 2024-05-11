<?php

declare(strict_types=1);

namespace App\Security\Domain\Application\Repository;

use App\Security\Domain\Model\Entity\ForgottenPasswordRequest;
use App\Security\Domain\Model\Entity\User;

/**
 * @method ForgottenPasswordRequest|null findOneByUser(User $user)

 */
interface ForgottenPasswordRequestRepository
{
    public function insert(ForgottenPasswordRequest $forgottenPasswordRequest): void;

    public function remove(ForgottenPasswordRequest $forgottenPasswordRequest): void;
}
