<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Application\UseCase\SignUp\SignUpHandler;
use App\Application\UseCase\SignUp\SignUpInput;
use App\Domain\User\Event\UserRegistered;
use App\Domain\User\Validation\Validator\UniqueEmailValidator;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fixtures\Security\Application\Hasher\FakePasswordHasher;
use Tests\Fixtures\Security\Doctrine\Repository\FakeUserRepositoryPort;
use Tests\Unit\UseCaseTestCase;

final class SignUpTest extends UseCaseTestCase
{
    private FakeUserRepositoryPort $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = new FakeUserRepositoryPort();
        $this->setValidator([
            UniqueEmailValidator::class => new UniqueEmailValidator($this->userRepository),
        ]);

        $this->setUseCase(
            new SignUpHandler(
                $this->userRepository,
                new FakePasswordHasher(),
                self::eventBus(),
            )
        );
    }

    #[Test]
    public function shouldSignUp(): void
    {
        self::setTestNow(new Chronos('2024-01-01 00:00:00'));

        $input = new SignUpInput();
        $input->email = 'user@email.com';
        $input->password = '4234df00-45dd-49a4-b303-a75dbf8b10d8!';

        $this->handle($input);

        $user = $this->userRepository->findOneByEmail($input->email);

        self::assertNotNull($user);
        self::assertSame('hashed_password', $user->getPassword());
        self::assertEventDispatched(new UserRegistered($user->getId()));
    }

    /**
     * @param array<array{propertyPath: string, message: string}> $expectedViolations
     */
    #[Test]
    #[DataProvider('provideInvalidData')]
    public function shouldRaiseValidationFailedException(array $expectedViolations, SignUpInput $input): void
    {
        $this->expectViolations($expectedViolations);
        $this->handle($input);
    }

    /**
     * @return iterable<array{
     *     expectedViolations: array<array{propertyPath: string, message: string}>,
     *     input: SignUpInput
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
            'input' => self::createInput('', self::faker()->password(20)),
        ];

        yield 'invalid email' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is not a valid email address.',
                ],
            ],
            'input' => self::createInput('fail', self::faker()->password(20)),
        ];

        yield 'non unique email' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'Cette adresse email est déjà utilisée.',
                ],
            ],
            'input' => self::createInput('admin+1@email.com', self::faker()->password(20)),
        ];

        yield 'blank password' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'The password strength is too low. Please use a stronger password.',
                ],
            ],
            'input' => self::createInput('user@email.com', ''),
        ];

        yield 'compromised password' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This password has been leaked in a data breach, it must not be used. Please use another password.',
                ],
            ],
            'input' => self::createInput('user@email.com', 'Password123!'),
        ];
    }

    private static function createInput(string $email, string $password): SignUpInput
    {
        $input = new SignUpInput();
        $input->email = $email;
        $input->password = $password;

        return $input;
    }
}
