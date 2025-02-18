<?php

declare(strict_types=1);

namespace Tests\Fixtures\Security\Doctrine\Repository;

use App\Security\Domain\Application\Repository\VerificationCodeRepository;
use App\Security\Domain\Model\Entity\VerificationCode;

final class FakeVerificationCodeRepository implements VerificationCodeRepository
{
    /**
     * @var array<string, VerificationCode>
     */
    public array $verificationCodes = [];

    public function generateCode(): string
    {
        return sprintf('%06d', rand(0, 999999));
    }

    public function insert(VerificationCode $verificationCode): void
    {
        $this->verificationCodes[$verificationCode->getCode()] = $verificationCode;
    }

    public function remove(VerificationCode $verificationCode): void
    {
        unset($this->verificationCodes[$verificationCode->getCode()]);
    }
}
