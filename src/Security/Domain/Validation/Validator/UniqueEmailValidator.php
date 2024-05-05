<?php

declare(strict_types=1);

namespace App\Security\Domain\Validation\Validator;

use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\ValueObject\Email;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class UniqueEmailValidator extends ConstraintValidator
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $constraint instanceof UniqueEmail) {
            return;
        }

        if (! $value instanceof Email && ! is_string($value)) {
            return;
        }

        if (! $this->userRepository->isAlreadyUsed((string) $value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
