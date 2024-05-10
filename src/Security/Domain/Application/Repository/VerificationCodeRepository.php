<?php

declare(strict_types=1);

namespace App\Security\Domain\Application\Repository;

use App\Security\Domain\Model\Entity\VerificationCode;

interface VerificationCodeRepository
{
    public function generateCode(): string;

    public function insert(VerificationCode $verificationCode): void;

    public function remove(VerificationCode $verificationCode): void;
}
