<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Core\Domain\Model\Entity\PendingOneTimePassword;
use App\Core\Domain\Model\Exception\OneTimePasswordException;
use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Id;
use App\Core\Domain\Model\ValueObject\OneTimePassword;
use App\Core\Domain\Model\ValueObject\Target;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\Exception\UserException;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Domain\UseCase\ConfirmRegistration\Handler;
use App\Security\Domain\UseCase\ConfirmRegistration\Input;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\Constraints\ExpressionValidator;
use Tests\Fixtures\Core\Infrastructure\Doctrine\FakePendingOneTimePasswordRepository;
use Tests\Fixtures\Security\Doctrine\Repository\FakeUserRepository;
use Tests\Unit\UseCaseTestCase;

final class ConfirmRegistrationTest extends UseCaseTestCase
{
    private FakeUserRepository $fakeUserRepository;

    private FakePendingOneTimePasswordRepository $fakePendingOneTimePasswordRepository;

    protected function setUp(): void
    {
        $this->fakeUserRepository = new FakeUserRepository();
        $this->fakePendingOneTimePasswordRepository = new FakePendingOneTimePasswordRepository();
        $this->setValidator([
            'validator.expression' => new ExpressionValidator(new ExpressionLanguage()),
        ]);
        $this->setUseCase(
            new Handler(
                $this->fakeUserRepository,
                $this->fakePendingOneTimePasswordRepository
            )
        );
    }

    #[Test]
    public function shouldConfirmRegistration(): void
    {
        $user = new User(
            Id::generate(),
            Email::create('user@email.com'),
            Password::create('hashed_password'),
            Status::WaitingForConfirmation
        );

        $this->fakeUserRepository->insert($user);

        $pendingOneTimePassword = new PendingOneTimePassword(
            Id::generate(),
            OneTimePassword::create('000000'),
            new Chronos('+1 hour'),
            Target::create($user::class, $user->getId())
        );

        $this->fakePendingOneTimePasswordRepository->insert($pendingOneTimePassword);

        $input = new Input();
        $input->oneTimePassword = '000000';
        $input->email = 'user@email.com';

        $this->handle($input);

        self::assertTrue($user->isActive());
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToUserAlreadyActive(): void
    {
        $user = new User(
            Id::generate(),
            Email::create('user@email.com'),
            Password::create('hashed_password'),
            Status::Active
        );

        $this->fakeUserRepository->insert($user);

        $pendingOneTimePassword = new PendingOneTimePassword(
            Id::generate(),
            OneTimePassword::create('000000'),
            new Chronos('+1 hour'),
            Target::create($user::class, $user->getId())
        );

        $this->fakePendingOneTimePasswordRepository->insert($pendingOneTimePassword);

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
            Id::generate(),
            Email::create('user@email.com'),
            Password::create('hashed_password'),
            Status::Active
        );

        $this->fakeUserRepository->insert($user);

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
            Id::generate(),
            Email::create('user@email.com'),
            Password::create('hashed_password'),
            Status::Active
        );

        $this->fakeUserRepository->insert($user);

        $pendingOneTimePassword = new PendingOneTimePassword(
            Id::generate(),
            OneTimePassword::create('000000'),
            new Chronos('-1 hour'),
            Target::create($user::class, $user->getId())
        );

        $this->fakePendingOneTimePasswordRepository->insert($pendingOneTimePassword);

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
            Id::generate(),
            Email::create('user@email.com'),
            Password::create('hashed_password'),
            Status::Active
        );

        $this->fakeUserRepository->insert($user);

        $pendingOneTimePassword = new PendingOneTimePassword(
            Id::generate(),
            OneTimePassword::create('000000'),
            new Chronos('+1 hour'),
            Target::create($user::class, Id::generate())
        );

        $this->fakePendingOneTimePasswordRepository->insert($pendingOneTimePassword);

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
            Id::generate(),
            Email::create('user@email.com'),
            Password::create('hashed_password'),
            Status::Active
        );

        $this->fakeUserRepository->insert($user);

        $pendingOneTimePassword = new PendingOneTimePassword(
            Id::generate(),
            OneTimePassword::create('000000'),
            new Chronos('+1 hour'),
            Target::create(\stdClass::class, Id::generate())
        );

        $this->fakePendingOneTimePasswordRepository->insert($pendingOneTimePassword);

        $input = new Input();
        $input->oneTimePassword = '000000';
        $input->email = 'user@email.com';

        $this->expectException(OneTimePasswordException::class);

        $this->handle($input);
    }
}
