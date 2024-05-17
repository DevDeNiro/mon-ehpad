<?php

declare(strict_types=1);

namespace Tests\Integration\Security;

use App\Domain\User\Enum\Status;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepositoryPort;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\FakerTrait;
use Tests\Integration\ApiTestCase;

final class VerifyEmailTest extends ApiTestCase
{
    use FakerTrait;

    #[Test]
    public function shouldVerifyEmailSuccessfully(): void
    {
        $this->login();

        $this->post('/api/security/verify-email', [
            'code' => '000001',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertMatchesOpenApiResponse();

        $userRepository = $this->getService(UserRepositoryPort::class);
        $user = $userRepository->findOneByEmail('admin+1@email.com');

        self::assertInstanceOf(User::class, $user);
        self::assertTrue($user->getStatus()->equals(Status::Verified));
    }

    #[Test]
    public function shouldReturnForbiddenDueToUserAlreadyVerified(): void
    {
        $user = $this->login('admin+21@email.com');

        $this->post('/api/security/verify-email', [
            'code' => '000001',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        self::assertMatchesOpenApiResponse();
        self::assertJsonResponse([
            'message' => sprintf(
                'L\'utilisateur (id: %s) est déjà vérifié.',
                $user->getId()
            ),
        ]);
    }

    #[Test]
    public function shouldReturnForbiddenDueToInvalidVerificationCode(): void
    {
        $user = $this->login();

        $this->post('/api/security/verify-email', [
            'code' => '999999',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        self::assertMatchesOpenApiResponse();
        self::assertJsonResponse([
            'message' => sprintf(
                'Le code de vérification de l\'utilisateur (id: %s) est invalide.',
                $user->getId()
            ),
        ]);
    }

    #[Test]
    public function shouldReturnForbiddenDueToExpiredVerificationCode(): void
    {
        $user = $this->login();

        Chronos::setTestNow('+16 minutes');

        $this->post('/api/security/verify-email', [
            'code' => '000001',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        self::assertMatchesOpenApiResponse();
        self::assertJsonResponse([
            'message' => sprintf(
                'Le code de vérification de l\'utilisateur (id: %s) a expiré.',
                $user->getId()
            ),
        ]);
    }

    /**
     * @param array<array{message: string, propertyPath: string}> $expectedResponse
     */
    #[Test]
    #[DataProvider('provideInvalidData')]
    public function shouldReturnUnprocessableEntity(string $code, array $expectedResponse): void
    {
        $this->login();

        $this->post('/api/security/verify-email', [
            'code' => $code,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertMatchesOpenApiResponse();
        self::assertJsonResponse($expectedResponse);
    }

    /**
     * @return iterable<string, array{code: string, expectedResponse: array<array{message: string, propertyPath: string}>}>
     */
    public static function provideInvalidData(): iterable
    {
        yield 'blank code' => [
            'code' => '',
            'expectedResponse' => [
                [
                    'message' => 'Cette valeur ne doit pas être vide.',
                    'propertyPath' => 'code',
                ],
            ],
        ];

        yield 'invalid code' => [
            'code' => 'fail',
            'expectedResponse' => [
                [
                    'message' => "Cette valeur n'est pas valide.",
                    'propertyPath' => 'code',
                ],
            ],
        ];
    }

    #[Test]
    public function shouldReturnBadRequest(): void
    {
        $this->login();

        $this->post('/api/security/verify-email', [
            'fail' => '',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertMatchesOpenApiResponse();
    }

    #[Test]
    public function shouldReturnUnauthorized(): void
    {
        $this->post('/api/security/verify-email', [
            'code' => '000001',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertMatchesOpenApiResponse();
    }
}
