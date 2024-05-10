<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Exception;

use App\Core\Domain\Model\Exception\DomainException;
use Symfony\Component\Uid\Ulid;

/**
 * @extends DomainException<array{id: Ulid}>
 */
final class UserNotFoundException extends DomainException
{
    public function __construct(Ulid $id)
    {
        parent::__construct(
            sprintf(
                "L'utilisateur (id: %s) n'existe pas.",
                $id,
            ),
            [
                'id' => $id,
            ]
        );
    }
}
