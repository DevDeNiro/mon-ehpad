<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Core\Domain\Model\Entity\PendingOneTimePassword;
use App\Core\Domain\Model\Exception\OneTimePasswordException;
use App\Core\Domain\Model\ValueObject\Id;
use App\Core\Domain\Model\ValueObject\OneTimePassword;
use App\Core\Domain\Model\ValueObject\Target;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\Exception\UserException;
use App\Security\Domain\Model\ValueObject\Email;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Domain\UseCase\ConfirmRegistration\Handler;
use App\Security\Domain\UseCase\ConfirmRegistration\Input;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\Constraints\ExpressionValidator;
use Tests\Fixtures\Core\Infrastructure\Doctrine\FakePendingOneTimePasswordRepository;
use Tests\Fixtures\Security\Application\Security\FakeLoginProgrammatically;
use Tests\Fixtures\Security\Doctrine\Repository\FakeUserRepository;
use Tests\Unit\UseCaseTestCase;

final class ConfirmRegistrationTest extends UseCaseTestCase
{
    private FakeUserRepository $userRepository;

    private FakePendingOneTimePasswordRepository $pendingOneTimePasswordRepository;

    private FakeLoginProgrammatically $loginProgrammatically;

    protected function setUp(): void
    {
        $this->userRepository = new FakeUserRepository();
        $this->pendingOneTimePasswordRepository = new FakePendingOneTimePasswordRepository();
        $this->loginProgrammatically = new FakeLoginProgrammatically();
        $this->setValidator([
            'validator.expression' => new ExpressionValidator(new ExpressionLanguage()),
        ]);
        $this->setUseCase(
            new Handler(
                $this->userRepository,
                $this->pendingOneTimePasswordRepository,
                $this->loginProgrammatically
            )
        );
    }

    #[Test]
    public function shouldConfirmRegistration(): void
    {
        $user = new User(
            new Id(),
            Email::fromString('user@email.com'),
            Password::fromString('hashed_password'),
            Status::WaitingForConfirmation
        );

        $this->userRepository->insert($user);

        $pendingOneTimePassword = new PendingOneTimePassword(
            new Id(),
            OneTimePassword::fromString('000000'),
            new Chronos('+1 hour'),
            Target::create($user::class, $user->getId())
        );

        $this->pendingOneTimePasswordRepository->insert($pendingOneTimePassword);

        $input = new Input();
        $input->oneTimePassword = '000000';
        $input->email = 'user@email.com';

        $this->handle($input);

        self::assertTrue($user->isActive());
        self::assertTrue($this->loginProgrammatically->logged);
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToUserAlreadyActive(): void
    {
        $user = new User(
            new Id(),
            Email::fromString('user@email.com'),
            Password::fromString('hashed_password'),
            Status::Active
        );

        $this->userRepository->insert($user);

        $pendingOneTimePassword = new PendingOneTimePassword(
            new Id(),
            OneTimePassword::fromString('000000'),
            new Chronos('+1 hour'),
            Target::create($user::class, $user->getId())
        );

        $this->pendingOneTimePasswordRepository->insert($pendingOneTimePassword);

        $input = new Input();
        $input->oneTimePassword = '000000';
        $input->email = 'user@email.com';

        $this->expectException(UserException::class);

        $this->handle($input);
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToNonExistingOneTimePassword(): void
    {
        $user = new User(
            new Id(),
            Email::fromString('user@email.com'),
            Password::fromString('hashed_password'),
            Status::Active
        );

        $this->userRepository->insert($user);

        $input = new Input();
        $input->oneTimePassword = '000000';
        $input->email = 'user@email.com';

        $this->expectException(OneTimePasswordException::class);

        $this->handle($input);
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToExpiredOneTimePassword(): void
    {
        $user = new User(
            new Id(),
            Email::fromString('user@email.com'),
            Password::fromString('hashed_password'),
            Status::Active
        );

        $this->userRepository->insert($user);

        $pendingOneTimePassword = new PendingOneTimePassword(
            new Id(),
            OneTimePassword::fromString('000000'),
            new Chronos('-1 hour'),
            Target::create($user::class, $user->getId())
        );

        $this->pendingOneTimePasswordRepository->insert($pendingOneTimePassword);

        $input = new Input();
        $input->oneTimePassword = '000000';
        $input->email = 'user@email.com';

        $this->expectException(OneTimePasswordException::class);

        $this->handle($input);
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToMismatchWithTargetId(): void
    {
        $user = new User(
            new Id(),
            Email::fromString('user@email.com'),
            Password::fromString('hashed_password'),
            Status::Active
        );

        $this->userRepository->insert($user);

        $pendingOneTimePassword = new PendingOneTimePassword(
            new Id(),
            OneTimePassword::fromString('000000'),
            new Chronos('+1 hour'),
            Target::create($user::class, new Id())
        );

        $this->pendingOneTimePasswordRepository->insert($pendingOneTimePassword);

        $input = new Input();
        $input->oneTimePassword = '000000';
        $input->email = 'user@email.com';

        $this->expectException(OneTimePasswordException::class);

        $this->handle($input);
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToMismatchWithTargetEntity(): void
    {
        $user = new User(
            new Id(),
            Email::fromString('user@email.com'),
            Password::fromString('hashed_password'),
            Status::Active
        );

        $this->userRepository->insert($user);

        $pendingOneTimePassword = new PendingOneTimePassword(
            new Id(),
            OneTimePassword::fromString('000000'),
            new Chronos('+1 hour'),
            Target::create(\stdClass::class, new Id())
        );

        $this->pendingOneTimePasswordRepository->insert($pendingOneTimePassword);

        $input = new Input();
        $input->oneTimePassword = '000000';
        $input->email = 'user@email.com';

        $this->expectException(OneTimePasswordException::class);

        $this->handle($input);
    }
}
