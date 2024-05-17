<?php

declare(strict_types=1);

namespace Tests\Integration\Security;

use App\Core\Domain\Model\ValueObject\Email;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepositoryPort;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Tests\FakerTrait;
use Tests\Integration\ApiTestCase;

final class SignUpTest extends ApiTestCase
{
    use FakerTrait;

    #[Test]
    public function shouldSignUpSuccessfully(): void
    {
        $this->post('/api/security/sign-up', [
            'email' => 'user@email.com',
            'password' => self::faker()->password(20),
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertMatchesOpenApiResponse();

        $userRepository = $this->getService(UserRepositoryPort::class);
        $user = $userRepository->findOneByEmail('user@email.com');

        self::assertInstanceOf(User::class, $user);
        self::assertEmailCount(1);

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
        string $password,
        array $expectedResponse
    ): void {
        $this->post('/api/security/sign-up', [
            'email' => $email,
            'password' => $password,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertMatchesOpenApiResponse();
        self::assertJsonResponse($expectedResponse);
    }

    /**
     * @return iterable<string, array{email: string, password: string, expectedResponse: array<array{message: string, propertyPath: string}>}>
     */
    public static function provideInvalidData(): iterable
    {
        yield 'invalid email' => [
            'email' => 'fail',
            'password' => self::faker()->password(20),
            'expectedResponse' => [
                [
                    'message' => "Cette valeur n'est pas une adresse email valide.",
                    'propertyPath' => 'email',
                ],
            ],
        ];

        yield 'blank email' => [
            'email' => '',
            'password' => self::faker()->password(20),
            'expectedResponse' => [
                [
                    'message' => 'Cette valeur ne doit pas être vide.',
                    'propertyPath' => 'email',
                ],
            ],
        ];

        yield 'email non unique' => [
            'email' => 'admin+1@email.com',
            'password' => self::faker()->password(20),
            'expectedResponse' => [
                [
                    'message' => 'Cette adresse email est déjà utilisée.',
                    'propertyPath' => 'email',
                ],
            ],
        ];

        yield 'password strength too low' => [
            'email' => 'user@email.com',
            'password' => 'fail',
            'expectedResponse' => [
                [
                    'message' => 'La force du mot de passe est trop faible. Veuillez utiliser un mot de passe plus fort.',
                    'propertyPath' => 'password',
                ],
            ],
        ];
    }

    #[Test]
    public function shouldReturnBadRequest(): void
    {
        $this->post('/api/security/sign-up', [
            'fail' => '',
            'wrong' => '',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertMatchesOpenApiResponse();
    }
}
