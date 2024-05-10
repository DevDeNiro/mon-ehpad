<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Entity\VerificationCode;
use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\Exception\InvalidStateException;
use App\Security\Domain\UseCase\VerifyEmail\Handler;
use App\Security\Domain\UseCase\VerifyEmail\Input;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fixtures\Security\Doctrine\Repository\FakeUserRepository;
use Tests\Fixtures\Security\Doctrine\Repository\FakeVerificationCodeRepository;
use Tests\Unit\UseCaseTestCase;

final class VerifyEmailTest extends UseCaseTestCase
{
    private FakeUserRepository $userRepository;

    private FakeVerificationCodeRepository $verificationCodeRepository;

    protected function setUp(): void
    {
        $this->userRepository = new FakeUserRepository();
        $this->verificationCodeRepository = new FakeVerificationCodeRepository();
        $this->setUseCase(new Handler($this->verificationCodeRepository));
    }

    #[Test]
    public function shouldVerifyEmail(): void
    {
        $user = $this->registerUser();

        $input = new Input();
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

        $input = new Input();
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

        $input = new Input();
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

        $input = new Input();
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

        $input = new Input();
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
    public function shouldRaiseValidationFailedException(array $expectedViolations, Input $input): void
    {
        $this->expectViolations($expectedViolations);
        $this->handle($input);
    }

    /**
     * @return iterable<array{
     *     expectedViolations: array<array{propertyPath: string, message: string}>,
     *     input: Input
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

    private static function createInput(string $code): Input
    {
        $input = new Input();
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
