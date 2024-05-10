<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Event\UserRegistered;
use App\Security\Domain\Model\Exception\InvalidStateException;
use App\Security\Domain\Model\Exception\UserNotFoundException;
use App\Security\Domain\Model\Notification\VerificationEmail;
use App\Security\Domain\UseCase\SendEmailVerification\Handler;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Uid\Ulid;
use Tests\Fixtures\Security\Doctrine\Repository\FakeVerificationCodeRepository;
use Tests\Fixtures\Security\Doctrine\Repository\FakeUserRepository;
use Tests\Unit\UseCaseTestCase;

final class SendEmailVerificationTest extends UseCaseTestCase
{
    private FakeUserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = new FakeUserRepository();
        $this->setUseCase(
            new Handler(
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
        self::expectExceptionMessage(sprintf('L\'utilisateur (id: %s) n\'existe pas.', $id));
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
