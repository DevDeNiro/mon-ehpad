<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Entity;

use Symfony\Component\Uid\Ulid;

class Company
{
    private Ulid $id;

    private string $companyName;

    private User $owner;

    public static function create(string $companyName, User $owner): self
    {
        $company = new self();
        $company->id = new Ulid();
        $company->companyName = $companyName;
        $company->owner = $owner;

        return $company;
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }
}
