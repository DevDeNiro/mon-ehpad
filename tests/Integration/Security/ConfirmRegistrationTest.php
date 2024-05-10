<?php

declare(strict_types=1);

namespace Tests\Integration\Security;

use App\Core\Domain\Model\ValueObject\Email;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\Entity\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Tests\FakerTrait;
use Tests\Integration\ApiTestCase;

final class ConfirmRegistrationTest extends ApiTestCase
{
    use FakerTrait;

    #[Test]
    public function shouldConfirmRegistrationSuccessfully(): void
    {
        self::createClient();

        $this->post('/api/security/confirm-registration', [
            'email' => 'admin+1@email.com',
            'oneTimePassword' => '000001',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertMatchesOpenApiResponse();
        self::assertResponseHeaderSame('location', '/welcome');

        $userRepository = $this->getService(UserRepository::class);
        $user = $userRepository->findOneByEmail(Email::fromString('admin+1@email.com'));

        self::assertInstanceOf(User::class, $user);
        self::assertTrue($user->isActive());

        $authorizationChecker = $this->getService(AuthorizationCheckerInterface::class);

        self::assertTrue($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }

    /**
     * @param array<array{message: string, propertyPath: string}> $expectedResponse
     */
    #[Test]
    #[DataProvider('provideInvalidData')]
    public function shouldReturnUnprocessableEntity(
        string $email,
        string $oneTimePassword,
        array $expectedResponse
    ): void {
        self::createClient();

        $this->post('/api/security/confirm-registration', [
            'email' => $email,
            'oneTimePassword' => $oneTimePassword,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertMatchesOpenApiResponse();
        self::assertJsonResponse($expectedResponse);
    }

    /**
     * @return iterable<string, array{email: string, oneTimePassword: string, expectedResponse: array<array{message: string, propertyPath: string}>}>
     */
    public static function provideInvalidData(): iterable
    {
        yield 'invalid email' => [
            'email' => 'fail',
            'oneTimePassword' => '000001',
            'expectedResponse' => [
                [
                    'message' => "Cette valeur n'est pas une adresse email valide.",
                    'propertyPath' => 'email',
                ],
            ],
        ];

        yield 'blank email' => [
            'email' => '',
            'oneTimePassword' => '000001',
            'expectedResponse' => [
                [
                    'message' => 'Cette valeur ne doit pas être vide.',
                    'propertyPath' => 'email',
                ],
            ],
        ];

        yield 'blank OTP' => [
            'email' => 'admin+1@email.com',
            'oneTimePassword' => '',
            'expectedResponse' => [
                [
                    'message' => 'Cette valeur ne doit pas être vide.',
                    'propertyPath' => 'oneTimePassword',
                ],
            ],
        ];

        yield 'invalid OTP' => [
            'email' => 'admin+1@email.com',
            'oneTimePassword' => 'fail',
            'expectedResponse' => [
                [
                    'message' => 'Cette valeur n\'est pas valide.',
                    'propertyPath' => 'oneTimePassword',
                ],
            ],
        ];
    }

    #[Test]
    public function shouldReturnBadRequest(): void
    {
        self::createClient();

        $this->post('/api/security/confirm-registration', [
            'fail' => '',
            'wrong' => '',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertMatchesOpenApiResponse();
    }
}
