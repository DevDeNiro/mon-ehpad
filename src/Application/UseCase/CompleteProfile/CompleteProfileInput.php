<?php

declare(strict_types=1);

namespace App\Application\UseCase\CompleteProfile;

use App\Application\CQRS\Message\Command;
use App\Domain\User\Model\User;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

final class CompleteProfileInput implements Command
{
    #[Assert\NotBlank]
    public string $firstName;

    #[Assert\NotBlank]
    public string $lastName;

    #[Assert\NotBlank]
    public string $companyName;

    #[Assert\NotBlank]
    #[AssertPhoneNumber]
    public string $phoneNumber;

    public User $user;

    // Tu souhaites vérifier que l'objet User est bien instancié non ?
    // Pourquoi ne pas le faire dans modèle de la couche domaine
}
