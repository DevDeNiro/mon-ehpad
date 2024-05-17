<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\Security\Model\VerificationCode;

interface VerificationCodeRepository
{
    public function generateCode(): string;

    public function insert(VerificationCode $verificationCode): void;

    public function remove(VerificationCode $verificationCode): void;
}
