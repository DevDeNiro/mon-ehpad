<?php

declare(strict_types=1);

namespace Tests\Integration\Security;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Integration\ApiTestCase;

final class SendEmailVerificationTest extends ApiTestCase
{
    #[Test]
    public function shouldVerifyEmailSuccessfully(): void
    {
        $this->login();

        $this->post('/api/security/send-email-verification');

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertMatchesOpenApiResponse();
    }

    #[Test]
    public function shouldReturnUnauthorized(): void
    {
        $this->post('/api/security/send-email-verification');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertMatchesOpenApiResponse();
    }
}
