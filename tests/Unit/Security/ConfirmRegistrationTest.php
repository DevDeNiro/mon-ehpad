<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Identifier;
use App\Security\Domain\Model\Entity\Status;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Domain\UseCase\ConfirmRegistration\ConfirmRegistration;
use App\Security\Domain\UseCase\ConfirmRegistration\VerifiedUser;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\Constraints\ExpressionValidator;
use Tests\Fixtures\Security\Doctrine\Repository\FakeUserRepository;
use Tests\Unit\UseCaseTestCase;

final class ConfirmRegistrationTest extends UseCaseTestCase
{
    private FakeUserRepository $fakeUserRepository;

    #[\Override]
    protected function setUp(): void
    {
        $this->fakeUserRepository = new FakeUserRepository();
        $this->setValidator([
            'validator.expression' => new ExpressionValidator(new ExpressionLanguage()),
        ]);
        $this->setUseCase(new ConfirmRegistration($this->fakeUserRepository));
    }

    #[Test]
    public function shouldConfirmRegistration(): void
    {
        $user = new User(
            Identifier::generate(),
            Email::create('user@email.com'),
            Password::create('hashed_password'),
            Status::WaitingForConfirmation
        );

        $this->fakeUserRepository->insert($user);

        $verifiedUser = new VerifiedUser($user->email());

        $this->handle($verifiedUser);

        self::assertTrue($user->isActive());
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToUserAlreadyActive(): void
    {
        $user = new User(
            Identifier::generate(),
            Email::create('user@email.com'),
            Password::create('hashed_password'),
            Status::WaitingForConfirmation
        );

        $this->fakeUserRepository->insert($user);
        $user->confirm();
        $this->fakeUserRepository->save($user);

        $verifiedUser = new VerifiedUser($user->email());

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(sprintf('User (id: %s) is already active.', $user->id()));

        $this->handle($verifiedUser);
    }
}
