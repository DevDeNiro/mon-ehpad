<?php

declare(strict_types=1);

namespace App\Domain\User\Validation\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class UniqueEmail extends Constraint
{
    public string $message = 'Cette adresse email est déjà utilisée.';
}
