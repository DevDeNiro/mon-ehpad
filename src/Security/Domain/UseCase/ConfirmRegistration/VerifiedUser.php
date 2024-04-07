<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\ConfirmRegistration;

use App\Core\Domain\CQRS\Command;
use App\Security\Domain\Model\Entity\User;
use Symfony\Component\Validator\Constraints\Expression;

#[Expression('this.user().isWaitingForConfirmation()', message: 'User is already verified.')]
final readonly class VerifiedUser implements Command
{
    public function __construct(private User $user)
    {
    }

    public function user(): User
    {
        return $this->user;
    }
}
