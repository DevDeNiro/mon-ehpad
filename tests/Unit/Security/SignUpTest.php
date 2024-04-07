<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Core\Domain\Model\ValueObject\Email;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Event\UserRegistered;
use App\Security\Domain\Model\Factory\RegisterUserFactory;
use App\Security\Domain\UseCase\SignUp\NewUser;
use App\Security\Domain\UseCase\SignUp\SignUp;
use App\Security\Domain\Validation\Validator\UniqueEmailValidator;
use Cake\Chronos\Chronos;
use Tests\FakerTrait;
use Tests\Fixtures\Core\Doctrine\Repository\FakeUserRepository;
use Tests\Fixtures\Security\Hasher\FakePasswordHash;
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

        /** @var RegisterUserFactory $factory */
        $factory = self::entityFactory(RegisterUserFactory::class);

        $this->setUseCase(
            new SignUp(
                $this->userRepository,
                new FakePasswordHash(),
                $factory
            )
        );
    }

    public function testShouldSignUp(): void
    {
        static::setTestNow(new Chronos('2024-01-01 00:00:00'));

        $newUser = new NewUser();
        $newUser->email = 'user@email.com';
        $newUser->password = '4234df00-45dd-49a4-b303-a75dbf8b10d8!';

        $this->handle($newUser);

        $user = $this->userRepository->findByEmail(Email::create($newUser->email));

        self::assertInstanceOf(User::class, $user);
        self::assertSame('hashed_password', $user->password()->value());
        self::assertEventDispatched(UserRegistered::create($user->id()));
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
