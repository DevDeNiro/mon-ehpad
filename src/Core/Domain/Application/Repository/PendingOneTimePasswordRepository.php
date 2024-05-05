<?php

declare(strict_types=1);

namespace App\Core\Domain\Application\Repository;

use App\Core\Domain\Model\Entity\PendingOneTimePassword;
use App\Core\Domain\Model\Exception\OneTimePasswordException;
use App\Core\Domain\Model\ValueObject\OneTimePassword;

/**
 * @method PendingOneTimePassword|null findOneByOneTimePassword(OneTimePassword $oneTimePassword)
 */
interface PendingOneTimePasswordRepository
{
    /**
     * @throws OneTimePasswordException
     */
    public function generateOneTimePassword(): OneTimePassword;

    public function insert(PendingOneTimePassword $pendingOneTimePassword): void;

    public function remove(PendingOneTimePassword $pendingOneTimePassword): void;
}
