<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Application\UseCase\CompleteProfile\CompleteProfileHandler;
use App\Application\UseCase\CompleteProfile\CompleteProfileInput;
use App\Domain\Security\Model\VerificationCode;
use App\Domain\User\Enum\Status;
use App\Domain\User\Exception\InvalidStateException;
use App\Domain\User\Model\User;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fixtures\Security\Doctrine\Repository\FakeCompanyRepositoryPort;
use Tests\Fixtures\Security\Doctrine\Repository\FakeUserRepositoryPort;
use Tests\Unit\UseCaseTestCase;

final class CompleteTest extends UseCaseTestCase
{
    private FakeUserRepositoryPort $userRepository;

    private FakeCompanyRepositoryPort $companyRepository;

    protected function setUp(): void
    {
        $this->userRepository = new FakeUserRepositoryPort();
        $this->companyRepository = new FakeCompanyRepositoryPort();

        $this->setUseCase(new CompleteProfileHandler($this->companyRepository));
    }

    #[Test]
    public function shouldComplete(): void
    {
        $user = $this->verifyUser();

        self::setTestNow(new Chronos('2024-01-01 00:00:00'));

        $input = self::createInput();

        $input->user = $user;

        $this->handle($input);

        self::assertTrue($user->getStatus()->equals(Status::Completed));
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToUserNotVerified(): void
    {
        $user = User::register('user@email.com', 'hashed_password');
        $this->userRepository->insert($user);

        $input = self::createInput();
        $input->user = $user;

        $this->expectException(InvalidStateException::class);
        $this->expectExceptionMessage(
            sprintf(
                'L\'utilisateur (id: %s) n\'est pas vérifié.',
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
    public function shouldRaiseValidationFailedException(array $expectedViolations, CompleteProfileInput $input): void
    {
        $this->expectViolations($expectedViolations);
        $this->handle($input);
    }

    /**
     * @return iterable<array{
     *     expectedViolations: array<array{propertyPath: string, message: string}>,
     *     input: CompleteProfileInput
     * }>
     */
    public static function provideInvalidData(): iterable
    {
        yield 'blank first name' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'firstName',
                    'message' => 'This value should not be blank.',
                ],
            ],
            'input' => self::createInput(firstName: ''),
        ];

        yield 'blank last name' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'lastName',
                    'message' => 'This value should not be blank.',
                ],
            ],
            'input' => self::createInput(lastName: ''),
        ];

        yield 'blank company name' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'companyName',
                    'message' => 'This value should not be blank.',
                ],
            ],
            'input' => self::createInput(companyName: ''),
        ];

        yield 'blank phone number' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'phoneNumber',
                    'message' => 'This value should not be blank.',
                ],
            ],
            'input' => self::createInput(phoneNumber: ''),
        ];

        yield 'invalid phone number' => [
            'expectedViolations' => [
                [
                    'propertyPath' => 'phoneNumber',
                    'message' => 'This value is not a valid phone number.',
                ],
            ],
            'input' => self::createInput(phoneNumber: 'fail'),
        ];
    }

    private static function createInput(
        string $firstName = 'John',
        string $lastName = 'Doe',
        string $companyName = 'company',
        string $phoneNumber = '+33123456789'
    ): CompleteProfileInput {
        $input = new CompleteProfileInput();
        $input->firstName = $firstName;
        $input->lastName = $lastName;
        $input->companyName = $companyName;
        $input->phoneNumber = $phoneNumber;

        return $input;
    }

    private function verifyUser(): User
    {
        $user = User::register('user@email.com', 'hashed_password');
        $user->sendVerificationCode(VerificationCode::create('000000'));
        $user->verify('000000');

        $this->userRepository->insert($user);
        return $user;
    }
}
