<?php

declare(strict_types=1);

namespace App\Core\Domain\Application\Repository;

use App\Core\Domain\Model\Entity\PendingOneTimePassword;
use App\Core\Domain\Model\Exception\OneTimePasswordException;
use App\Core\Domain\Model\ValueObject\Id;
use App\Core\Domain\Model\ValueObject\OneTimePassword;

interface PendingOneTimePasswordRepository
{
    /**
     * @throws OneTimePasswordException
     */
    public function generateOneTimePassword(): OneTimePassword;

    public function insert(PendingOneTimePassword $pendingOneTimePassword): void;

    /**
     * @throws OneTimePasswordException
     */
    public function remove(PendingOneTimePassword $pendingOneTimePassword): void;

    /**
     * @throws OneTimePasswordException
     */
    public function findByOneTimePassword(OneTimePassword $oneTimePassword): PendingOneTimePassword;
}
