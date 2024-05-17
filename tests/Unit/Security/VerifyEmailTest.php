<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Application\UseCase\VerifyEmail\VerifyEmailHandler;
use App\Application\UseCase\VerifyEmail\VerifyEmailInput;
use App\Domain\Security\Model\VerificationCode;
use App\Domain\User\Enum\Status;
use App\Domain\User\Exception\InvalidStateException;
use App\Domain\User\Model\User;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fixtures\Security\Doctrine\Repository\FakeUserRepositoryPort;
use Tests\Fixtures\Security\Doctrine\Repository\FakeVerificationCodeRepository;
use Tests\Unit\UseCaseTestCase;

final class VerifyEmailTest extends UseCaseTestCase
{
    private FakeUserRepositoryPort $userRepository;

    private FakeVerificationCodeRepository $verificationCodeRepository;

    protected function setUp(): void
    {
        $this->userRepository = new FakeUserRepositoryPort();
        $this->verificationCodeRepository = new FakeVerificationCodeRepository();
        $this->setUseCase(new VerifyEmailHandler($this->verificationCodeRepository));
    }

    #[Test]
    public function shouldVerifyEmail(): void
    {
        $user = $this->registerUser();

        $input = new VerifyEmailInput();
        $input->code = '000000';
        $input->user = $user;

        $this->handle($input);

        self::assertTrue($user->getStatus()->equals(Status::Verified));
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToUserAlreadyVerified(): void
    {
        $user = $this->registerUser();

        $user->verify('000000');

        $input = new VerifyEmailInput();
        $input->code = '000000';
        $input->user = $user;

        $this->expectException(InvalidStateException::class);
        $this->expectExceptionMessage(
            sprintf(
                'L\'utilisateur (id: %s) est déjà vérifié.',
                $user->getId()
            )
        );

        $this->handle($input);
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToInvalidVerificationCode(): void
    {
        $user = $this->registerUser();

        $input = new VerifyEmailInput();
        $input->code = '000001';
        $input->user = $user;

        $this->expectException(InvalidStateException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Le code de vérification de l\'utilisateur (id: %s) est invalide.',
                $user->getId()
            )
        );

        $this->handle($input);
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToExpiredVerificationCode(): void
    {
        $user = $this->registerUser();

        self::setTestNow(new Chronos('+16 minutes'));

        $input = new VerifyEmailInput();
        $input->code = '000000';
        $input->user = $user;

        $this->expectException(InvalidStateException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Le code de vérification de l\'utilisateur (id: %s) a expiré.',
                $user->getId()
            )
        );

        $this->handle($input);
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToNonExistingVerificationCode(): void
    {
        $user = $this->registerUser();

        self::setValue($user, 'verificationCode', null);

        $input = new VerifyEmailInput();
        $input->code = '000000';
        $input->user = $user;

        $this->expectException(InvalidStateException::class);
        $this->expectExceptionMessage(
            sprintf(
                'L\'utilisateur (id: %s) n\'a pas de code de vérification.',
                $user->getId()
            )
        );

        $this->handle($input);
    }

    /**
     * @param array<array{propertyPath: string, message: string}> $expectedViolations
     */
    #[Test]
    #[DataProvider('provideInvalidData')]
    public function shouldRaiseValidationFailedException(array $expectedViolations, VerifyEmailInput $input): void
    {
        $this->expectViolations($expectedViolations);
        $this->handle($input);
    }

    /**
     * @return iterable<array{
     *     expectedViolations: array<array{propertyPath: string, message: string}>,
     *     input: VerifyEmailInput
     * }>
     */
    public static function provideInvalidData(): iterable
    {
        yield 'blank code' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'code',
                    'message' => 'This value should not be blank.',
                ],
            ],
            'input' => self::createInput(''),
        ];

        yield 'invalid code' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'code',
                    'message' => 'This value is not valid.',
                ],
            ],
            'input' => self::createInput('fail'),
        ];
    }

    private static function createInput(string $code): VerifyEmailInput
    {
        $input = new VerifyEmailInput();
        $input->code = $code;

        return $input;
    }

    private function registerUser(): User
    {
        $user = User::register('user@email.com', 'hashed_password');
        $this->userRepository->insert($user);
        $verificationCode = VerificationCode::create('000000');
        $user->sendVerificationCode($verificationCode);
        $this->verificationCodeRepository->insert($verificationCode);
        return $user;
    }
}
