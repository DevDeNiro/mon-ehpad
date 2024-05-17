<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Application\UseCase\SendEmailVerification\SendEmailVerificationHandler;
use App\Domain\Security\Notification\VerificationEmail;
use App\Domain\User\Event\UserRegistered;
use App\Domain\User\Exception\InvalidStateException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Model\User;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Uid\Ulid;
use Tests\Fixtures\Security\Doctrine\Repository\FakeUserRepositoryPort;
use Tests\Fixtures\Security\Doctrine\Repository\FakeVerificationCodeRepository;
use Tests\Unit\UseCaseTestCase;

final class SendEmailVerificationTest extends UseCaseTestCase
{
    private FakeUserRepositoryPort $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = new FakeUserRepositoryPort();
        $this->setUseCase(
            new SendEmailVerificationHandler(
                $this->userRepository,
                new FakeVerificationCodeRepository(),
                self::notifier()
            )
        );
    }

    #[Test]
    public function shouldSendVerificationEmail(): void
    {
        $user = $this->registerUser();

        $this->handle(new UserRegistered($user->getId()));

        self::assertNotificationSent(new VerificationEmail($user));
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToNonExistingUser(): void
    {
        $id = new Ulid();
        self::expectException(UserNotFoundException::class);
        self::expectExceptionMessage(sprintf("L'utilisateur (id: %s) n'existe pas.", $id));
        $this->handle(new UserRegistered($id));
    }

    #[Test]
    public function shouldRaiseAndExceptionDueToUserAlreadyVerified(): void
    {
        $user = $this->registerUser();

        $this->handle(new UserRegistered($user->getId()));

        self::assertNotNull($user->getVerificationCode());

        $user->verify($user->getVerificationCode()->getCode());

        $this->expectException(InvalidStateException::class);
        $this->expectExceptionMessage(
            sprintf(
                'L\'utilisateur (id: %s) est déjà vérifié.',
                $user->getId()
            )
        );

        $this->handle(new UserRegistered($user->getId()));
    }

    private function registerUser(): User
    {
        $user = User::register('user@email.com', 'hashed_password');
        $this->userRepository->insert($user);
        return $user;
    }
}
