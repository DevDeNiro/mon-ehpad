<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Infrastructure\Doctrine;

use App\Core\Domain\Application\Repository\PendingOneTimePasswordRepository;
use App\Core\Domain\Model\Entity\PendingOneTimePassword;
use App\Core\Domain\Model\Exception\OneTimePasswordException;
use App\Core\Domain\Model\ValueObject\OneTimePassword;

final class FakePendingOneTimePasswordRepository implements PendingOneTimePasswordRepository
{
    /**
     * @var string[]
     */
    private array $usedCodes = [];

    /**
     * @var array<string, PendingOneTimePassword>
     */
    public array $pendingOneTimePasswords = [];

    public function generateOneTimePassword(): OneTimePassword
    {
        do {
            $code = sprintf('%06d', random_int(0, 999999));
        } while (in_array($code, $this->usedCodes, true));

        return OneTimePassword::create($code);
    }

    public function insert(PendingOneTimePassword $pendingOneTimePassword): void
    {
        $this->pendingOneTimePasswords[$pendingOneTimePassword->getOneTimePassword()->value()] = $pendingOneTimePassword;
    }

    public function remove(PendingOneTimePassword $pendingOneTimePassword): void
    {
        if (!isset($this->pendingOneTimePasswords[$pendingOneTimePassword->getOneTimePassword()->value()])) {
            throw OneTimePasswordException::pendingOneTimePasswordNotFound($pendingOneTimePassword->getId());
        }
        unset($this->pendingOneTimePasswords[$pendingOneTimePassword->getOneTimePassword()->value()]);
    }

    public function findByOneTimePassword(OneTimePassword $oneTimePassword): PendingOneTimePassword
    {
        if (!isset($this->pendingOneTimePasswords[$oneTimePassword->value()])) {
            throw OneTimePasswordException::pendingOneTimePasswordNotFoundByOneTimePassword($oneTimePassword);
        }

        return $this->pendingOneTimePasswords[$oneTimePassword->value()];
    }
}
