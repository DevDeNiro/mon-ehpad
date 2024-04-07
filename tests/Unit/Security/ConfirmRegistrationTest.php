<?php

declare(strict_types=1);

namespace Security;

use App\Core\Domain\ValueObject\Email;
use App\Security\Domain\Entity\User;
use App\Security\Domain\UseCase\ConfirmRegistration\ConfirmRegistration;
use App\Security\Domain\UseCase\ConfirmRegistration\VerifiedUser;
use App\Security\Domain\ValueObject\Password;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\Constraints\ExpressionValidator;
use Tests\Fixtures\Core\Doctrine\Repository\FakeUserRepository;
use Tests\Unit\UseCaseTestCase;

final class ConfirmRegistrationTest extends UseCaseTestCase
{
    private FakeUserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = new FakeUserRepository();
        $this->setValidator([
            'validator.expression' => new ExpressionValidator(new ExpressionLanguage()),
        ]);
        $this->setUseCase(new ConfirmRegistration($this->userRepository));
    }

    public function testShouldConfirmRegistration(): void
    {
        $user = User::register(
            Email::create('user@email.com'),
            Password::create('hashed_password')
        );

        $this->userRepository->register($user);

        $verifiedUser = new VerifiedUser($user);

        $this->handle($verifiedUser);

        self::assertTrue($user->isActive());
    }

    public function testShouldRaiseAndExceptionDueToUserAlreadyActive(): void
    {
        $user = User::register(
            Email::create('user@email.com'),
            Password::create('hashed_password')
        );

        $this->userRepository->register($user);
        $user->confirm();
        $this->userRepository->confirm($user);

        $verifiedUser = new VerifiedUser($user);

        $this->expectedViolations([]);

        $this->handle($verifiedUser);
    }
}
