<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Security\Domain\Application\TokenGenerator\TokenGenerator;
use App\Security\Domain\Model\Exception\ForgottenPasswordAlreadyRequestedException;
use App\Security\Domain\Model\Exception\UserNotFoundException;
use App\Security\Domain\Model\Notification\ResetPasswordEmail;
use App\Security\Domain\UseCase\RequestForgottenPassword\Handler;
use App\Security\Domain\UseCase\RequestForgottenPassword\Input;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fixtures\Security\Doctrine\Repository\FakeForgottenPasswordRequestRepository;
use Tests\Fixtures\Security\Doctrine\Repository\FakeUserRepository;
use Tests\Unit\UseCaseTestCase;

final class RequestForgottenPasswordTest extends UseCaseTestCase
{
    private FakeUserRepository $userRepository;

    private FakeForgottenPasswordRequestRepository $forgottenPasswordRequestRepository;

    protected function setUp(): void
    {
        $this->userRepository = new FakeUserRepository();
        $this->forgottenPasswordRequestRepository = new FakeForgottenPasswordRequestRepository();

        $tokenGenerator = $this->createMock(TokenGenerator::class);
        $tokenGenerator->method('generateToken')->willReturn('token');
        $tokenGenerator->method('generateHashedToken')->willReturn('hashed_token');

        $this->setUseCase(
            new Handler(
                $this->userRepository,
                $this->forgottenPasswordRequestRepository,
                $tokenGenerator,
                self::notifier()
            )
        );
    }

    #[Test]
    public function shouldRequestForgottenPassword(): void
    {
        $input = new Input();
        $input->email = 'admin+1@email.com';

        $this->handle($input);

        $user = $this->userRepository->findOneByEmail($input->email);
        self::assertNotNull($user);

        $forgottenPasswordRequest = $this->forgottenPasswordRequestRepository->findOneByUser($user);
        self::assertNotNull($forgottenPasswordRequest);
        self::assertNotificationSent(new ResetPasswordEmail($forgottenPasswordRequest, 'token'));
    }

    #[Test]
    public function shouldRequestForgottenPasswordAgain(): void
    {
        self::setTestNow(new Chronos('2024-01-01 00:00:00'));

        $this->shouldRequestForgottenPassword();

        self::setTestNow(new Chronos('2024-01-02 00:00:00'));

        $this->shouldRequestForgottenPassword();
    }

    #[Test]
    public function shouldRaiseExceptionDueToNonExpiredRequest(): void
    {
        self::setTestNow(new Chronos('2024-01-01 00:00:00'));

        $this->shouldRequestForgottenPassword();

        $this->expectException(ForgottenPasswordAlreadyRequestedException::class);
        $this->expectExceptionMessage("Une demande de réinitialisation de mot de passe a déjà été effectuée pour l'utilisateur");

        $this->shouldRequestForgottenPassword();
    }

    #[Test]
    public function shouldRaiseExceptionDueToNonExistingUser(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("L'utilisateur (email: fail@email.com) n'existe pas");

        $input = new Input();
        $input->email = 'fail@email.com';

        $this->handle($input);
    }


    /**
     * @param array<array{propertyPath: string, message: string}> $expectedViolations
     */
    #[Test]
    #[DataProvider('provideInvalidData')]
    public function shouldRaiseValidationFailedException(array $expectedViolations, string $email): void
    {
        $this->expectViolations($expectedViolations);

        $input = new Input();
        $input->email = $email;

        $this->handle($input);
    }

    /**
     * @return iterable<array{
     *     expectedViolations: array<array{propertyPath: string, message: string}>,
     *     email: string
     * }>
     */
    public static function provideInvalidData(): iterable
    {
        yield 'blank email' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value should not be blank.',
                ],
            ],
            'email' => '',
        ];

        yield 'invalid email' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is not a valid email address.',
                ],
            ],
            'email' => 'fail',
        ];
    }
}
