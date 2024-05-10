<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Entity;

use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\Exception\InvalidStateException;
use Symfony\Component\Uid\Ulid;

class User
{
    private Ulid $id;

    private string $email;

    private string $password;

    private Status $status = Status::Registered;

    private ?VerificationCode $verificationCode = null;

    private ?string $firstName = null;

    private ?string $lastName = null;

    private ?string $phoneNumber = null;

    private ?Company $company = null;

    public static function register(string $email, string $password): self
    {
        $user = new self();
        $user->id = new Ulid();
        $user->email = $email;
        $user->password = $password;

        return $user;
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function sendVerificationCode(VerificationCode $verificationCode): void
    {
        if (!$this->status->equals(Status::Registered)) {
            throw InvalidStateException::alreadyVerified($this);
        }

        $this->verificationCode = $verificationCode;
    }

    public function verify(string $verificationCode): void
    {
        if (!$this->status->equals(Status::Registered)) {
            throw InvalidStateException::alreadyVerified($this);
        }

        if (null === $this->verificationCode) {
            throw InvalidStateException::noVerificationCode($this);
        }

        if ($this->verificationCode->isExpired()) {
            throw InvalidStateException::verificationCodeExpired($this);
        }

        if (!$this->verificationCode->equals($verificationCode)) {
            throw InvalidStateException::invalidVerificationCode($this, $verificationCode);
        }

        $this->status = Status::Verified;
        $this->verificationCode = null;
    }

    public function complete(string $firstName, string $lastName, string $phoneNumber, string $companyName): void
    {
        if (!$this->status->equals(Status::Verified)) {
            throw InvalidStateException::notVerified($this);
        }

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phoneNumber = $phoneNumber;
        $this->company = Company::create($companyName, $this);
        $this->status = Status::Completed;
    }

    public function getVerificationCode(): ?VerificationCode
    {
        return $this->verificationCode;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }
}
