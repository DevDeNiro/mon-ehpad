<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Alice\Factory;

use App\Core\Domain\Model\Entity\PendingOneTimePassword;
use App\Core\Domain\Model\ValueObject\OneTimePassword;
use App\Core\Domain\Model\ValueObject\Target;
use App\Security\Domain\Model\Entity\User;
use Cake\Chronos\Chronos;

final readonly class PendingOneTimePasswordFactory
{
    public function __construct(private IdFactory $idFactory)
    {
    }

    public function create(string $current): PendingOneTimePassword
    {
        return new PendingOneTimePassword(
            $this->idFactory->create('otp', (int) $current),
            OneTimePassword::fromString(sprintf('%06d', (int) $current)),
            Chronos::now()->addHours(1),
            Target::create(User::class, $this->idFactory->create('user', (int) $current))
        );
    }
}
