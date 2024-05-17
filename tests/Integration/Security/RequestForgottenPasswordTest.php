<?php

declare(strict_types=1);

namespace Tests\Integration\Security;

use App\Domain\Security\Model\ForgottenPasswordRequest;
use App\Domain\Security\Repository\ForgottenPasswordRequestRepository;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepositoryPort;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\FakerTrait;
use Tests\Integration\ApiTestCase;

final class RequestForgottenPasswordTest extends ApiTestCase
{
    use FakerTrait;

    #[Test]
    public function shouldRequestForgottenPasswordSuccessfully(): void
    {
        $this->post('/api/security/request-forgotten-password', [
            'email' => 'admin+1@email.com',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertMatchesOpenApiResponse();

        $userRepository = $this->getService(UserRepositoryPort::class);
        $user = $userRepository->findOneByEmail('admin+1@email.com');
        self::assertInstanceOf(User::class, $user);

        $forgottenPasswordRequestRepository = $this->getService(ForgottenPasswordRequestRepository::class);
        $forgottenPasswordRequest = $forgottenPasswordRequestRepository->findOneByUser($user);
        self::assertInstanceOf(ForgottenPasswordRequest::class, $forgottenPasswordRequest);
        self::assertEmailCount(1);
    }

    #[Test]
    public function shouldReturnForbiddenDueToNonExpiredRequest(): void
    {
        $this->post('/api/security/request-forgotten-password', [
            'email' => 'admin+1@email.com',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertMatchesOpenApiResponse();

        $this->post('/api/security/request-forgotten-password', [
            'email' => 'admin+1@email.com',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        self::assertMatchesOpenApiResponse();
    }

    #[Test]
    public function shouldRequestForgottenPasswordAgain(): void
    {
        Chronos::setTestNow(Chronos::now());

        $this->post('/api/security/request-forgotten-password', [
            'email' => 'admin+1@email.com',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertMatchesOpenApiResponse();

        Chronos::setTestNow(Chronos::now()->addHours(2));

        $this->post('/api/security/request-forgotten-password', [
            'email' => 'admin+1@email.com',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertMatchesOpenApiResponse();
    }

    /**
     * @param array<array{message: string, propertyPath: string}> $expectedResponse
     */
    #[Test]
    #[DataProvider('provideInvalidData')]
    public function shouldReturnUnprocessableEntity(
        string $email,
        array $expectedResponse
    ): void {
        $this->post('/api/security/request-forgotten-password', [
            'email' => $email,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertMatchesOpenApiResponse();
        self::assertJsonResponse($expectedResponse);
    }

    /**
     * @return iterable<string, array{email: string, expectedResponse: array<array{message: string, propertyPath: string}>}>
     */
    public static function provideInvalidData(): iterable
    {
        yield 'invalid email' => [
            'email' => 'fail',
            'expectedResponse' => [
                [
                    'message' => "Cette valeur n'est pas une adresse email valide.",
                    'propertyPath' => 'email',
                ],
            ],
        ];

        yield 'blank email' => [
            'email' => '',
            'expectedResponse' => [
                [
                    'message' => 'Cette valeur ne doit pas être vide.',
                    'propertyPath' => 'email',
                ],
            ],
        ];
    }

    #[Test]
    public function shouldReturnBadRequest(): void
    {
        $this->post('/api/security/request-forgotten-password', [
            'fail' => '',
            'wrong' => '',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertMatchesOpenApiResponse();
    }
}
