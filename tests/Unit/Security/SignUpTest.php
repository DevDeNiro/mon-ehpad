<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Core\Domain\ValueObject\Email;
use App\Security\Domain\UseCase\SignUp\NewUser;
use App\Security\Domain\UseCase\SignUp\SignUp;
use App\Security\Domain\Validator\UniqueEmailValidator;
use Tests\FakerTrait;
use Tests\Fixtures\Core\Doctrine\Repository\FakeUserRepository;
use Tests\Fixtures\Security\Hasher\FakePasswordHash;
use Tests\Fixtures\Security\LoginLink\FakeLoginLinkGenerator;
use Tests\Unit\UseCaseTestCase;

final class SignUpTest extends UseCaseTestCase
{
    use FakerTrait;

    private FakeUserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = new FakeUserRepository();
        $this->setValidator([
            UniqueEmailValidator::class => new UniqueEmailValidator($this->userRepository),
        ]);

        $this->setUseCase(
            new SignUp(
                $this->userRepository,
                new FakePasswordHash(),
                self::notifier(),
                new FakeLoginLinkGenerator()
            )
        );
    }

    public function testShouldSignUp(): void
    {
        $newUser = new NewUser();
        $newUser->email = 'user@email.com';
        $newUser->password = '4234df00-45dd-49a4-b303-a75dbf8b10d8!';

        $this->handle($newUser);

        self::assertTrue($this->userRepository->isAlreadyUsed(Email::create($newUser->email)));
        self::assertSame('hashed_password', $this->userRepository->users[$newUser->email]->password()->value());
        self::assertEmailSent();
    }

    /**
     * @dataProvider provideInvalidData
     *
     * @param array<array{propertyPath: string, message: string}> $expectedViolations
     */
    public function testShouldRaiseValidationFailedException(array $expectedViolations, NewUser $newUser): void
    {
        $this->expectedViolations($expectedViolations);
        $this->handle($newUser);
    }

    /**
     * @return iterable<array{
     *     expectedViolations: array<array{propertyPath: string, message: string}>,
     *     newUser: NewUser
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
            'newUser' => self::createNewUser('', self::faker()->password(20)),
        ];

        yield 'invalid email' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is not a valid email address.',
                ],
            ],
            'newUser' => self::createNewUser('fail', self::faker()->password(20)),
        ];

        yield 'non unique email' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is not a valid email address.',
                ],
            ],
            'newUser' => self::createNewUser('fail', self::faker()->password(20)),
        ];

        yield 'blank password' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'The password strength is too low. Please use a stronger password.',
                ],
            ],
            'newUser' => self::createNewUser('user@email.com', ''),
        ];

        yield 'compromised password' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This password has been leaked in a data breach, it must not be used. Please use another password.',
                ],
            ],
            'newUser' => self::createNewUser('user@email.com', 'Password123!'),
        ];
    }

    private static function createNewUser(string $email, string $password): NewUser
    {
        $newUser = new NewUser();
        $newUser->email = $email;
        $newUser->password = $password;

        return $newUser;
    }
}
