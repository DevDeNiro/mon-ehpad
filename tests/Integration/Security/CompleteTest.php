<?php

declare(strict_types=1);

namespace Tests\Integration\Security;

use App\Domain\User\Enum\Status;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\FakerTrait;
use Tests\Integration\ApiTestCase;

final class CompleteTest extends ApiTestCase
{
    use FakerTrait;

    #[Test]
    public function shouldCompleteSuccessfully(): void
    {
        $user = $this->login('admin+11@email.com');

        $this->post('/api/security/complete', self::createPayload());

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertMatchesOpenApiResponse();

        $this->refresh($user);

        self::assertTrue($user->getStatus()->equals(Status::Completed));
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<array{message: string, propertyPath: string}> $expectedResponse
     */
    #[Test]
    #[DataProvider('provideInvalidData')]
    public function shouldReturnUnprocessableEntity(array $payload, array $expectedResponse): void
    {
        $this->login('admin+11@email.com');

        $this->post('/api/security/complete', $payload);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertMatchesOpenApiResponse();
        self::assertJsonResponse($expectedResponse);
    }

    /**
     * @return iterable<string, array{payload: array<string, mixed>, expectedResponse: array<array{message: string, propertyPath: string}>}>
     */
    public static function provideInvalidData(): iterable
    {
        yield 'blank first name' => [
            'payload' => self::createPayload(firstName: ''),
            'expectedResponse' => [
                [
                    'message' => 'Cette valeur ne doit pas être vide.',
                    'propertyPath' => 'firstName',
                ],
            ],
        ];
        yield 'blank last name' => [
            'payload' => self::createPayload(lastName: ''),
            'expectedResponse' => [
                [
                    'message' => 'Cette valeur ne doit pas être vide.',
                    'propertyPath' => 'lastName',
                ],
            ],
        ];

        yield 'blank company name' => [
            'payload' => self::createPayload(companyName: ''),
            'expectedResponse' => [
                [
                    'message' => 'Cette valeur ne doit pas être vide.',
                    'propertyPath' => 'companyName',
                ],
            ],
        ];

        yield 'blank phone number' => [
            'payload' => self::createPayload(phoneNumber: ''),
            'expectedResponse' => [
                [
                    'message' => 'Cette valeur ne doit pas être vide.',
                    'propertyPath' => 'phoneNumber',
                ],
            ],
        ];

        yield 'invalid phone number' => [
            'payload' => self::createPayload(phoneNumber: 'fail'),
            'expectedResponse' => [
                [
                    'message' => 'Cette valeur n\'est pas un numéro de téléphone valide.',
                    'propertyPath' => 'phoneNumber',
                ],
            ],
        ];
    }

    #[Test]
    public function shouldReturnBadRequest(): void
    {
        $this->login();

        $this->post('/api/security/complete', [
            'fail' => '',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertMatchesOpenApiResponse();
    }

    #[Test]
    public function shouldReturnUnauthorized(): void
    {
        $this->post('/api/security/complete', self::createPayload());

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertMatchesOpenApiResponse();
    }

    /**
     * @return array{firstName: string, lastName: string, companyName: string, phoneNumber: string}
     */
    private static function createPayload(
        string $firstName = 'John',
        string $lastName = 'Doe',
        string $companyName = 'company',
        string $phoneNumber = '+33123456789'
    ): array {
        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'companyName' => $companyName,
            'phoneNumber' => $phoneNumber,
        ];
    }
}
