<?php

declare(strict_types=1);

namespace Tests\Integration\Security;

use App\Core\Domain\Model\ValueObject\Email;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\Event\UserRegistered;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\FakerTrait;
use Tests\Integration\ApiTestCase;

final class SignUpTest extends ApiTestCase
{
    use FakerTrait;

    #[Test]
    public function shouldSignUpSuccessfully(): void
    {
        self::createClient();

        $this->post('/api/security/sign-up', [
            'email' => 'user@email.com',
            'password' => self::faker()->password(20),
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertMatchesOpenApiResponse();
        self::assertResponseHeaderSame('location', '/welcome');

        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $user = $userRepository->findByEmail(Email::create('user@email.com'));

        self::assertEventDispatched(new UserRegistered($user->getId()));
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
        self::createClient();

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
        self::createClient();

        $this->post('/api/security/sign-up', [
            'fail' => '',
            'wrong' => '',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertMatchesOpenApiResponse();
    }
}
