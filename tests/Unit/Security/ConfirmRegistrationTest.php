<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Core\Domain\Model\ValueObject\Email;
use App\Security\Domain\Model\Factory\RegisterUserFactory;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Domain\UseCase\ConfirmRegistration\ConfirmRegistration;
use App\Security\Domain\UseCase\ConfirmRegistration\VerifiedUser;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\Constraints\ExpressionValidator;
use Tests\Fixtures\Core\Doctrine\Repository\FakeUserRepository;
use Tests\Unit\UseCaseTestCase;

final class ConfirmRegistrationTest extends UseCaseTestCase
{
    private FakeUserRepository $userRepository;

    private RegisterUserFactory $factory;

    protected function setUp(): void
    {
        $this->userRepository = new FakeUserRepository();
        $this->setValidator([
            'validator.expression' => new ExpressionValidator(new ExpressionLanguage()),
        ]);
        $this->setUseCase(new ConfirmRegistration($this->userRepository));
        $this->factory = self::entityFactory(RegisterUserFactory::class);
    }

    public function testShouldConfirmRegistration(): void
    {
        $user = $this->factory
            ->withEmail(Email::create('user@email.com'))
            ->withPassword(Password::create('hashed_password'))
            ->build();

        $this->userRepository->register($user);

        $verifiedUser = new VerifiedUser($user);

        $this->handle($verifiedUser);

        self::assertTrue($user->isActive());
    }

    public function testShouldRaiseAndExceptionDueToUserAlreadyActive(): void
    {
        $user = $this->factory
            ->withEmail(Email::create('user@email.com'))
            ->withPassword(Password::create('hashed_password'))
            ->build();

        $this->userRepository->register($user);
        $user->confirm();
        $this->userRepository->confirm($user);

        $verifiedUser = new VerifiedUser($user);

        $this->expectedViolations([
            [
                'propertyPath' => '',
                'message' => 'User is already verified.',
            ],
        ]);

        $this->handle($verifiedUser);
    }
}
