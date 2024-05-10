<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\Complete;

use App\Core\Domain\Application\CQRS\Message\Command;
use App\Security\Domain\Model\Entity\User;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

final class Input implements Command
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
}
