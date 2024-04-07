<?php

declare(strict_types=1);

namespace App\Security\Domain\Validation\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class UniqueEmail extends Constraint
{
    public string $message = 'Cette adresse email est déjà utilisée.';
}
