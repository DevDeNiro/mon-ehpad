<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Core\Domain\Application\CQRS\EventBus;
use Safe\Exceptions\JsonException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;
use Tests\Fixtures\Core\Infrastructure\Symfony\CQRS\FakeEventBus;
use function Safe\json_encode;

abstract class ApiTestCase extends WebTestCase
{
    use ApiAssertionsTrait;

    public static function eventBus(): FakeEventBus
    {
        /** @var FakeEventBus $eventBus */
        $eventBus = self::getContainer()->get(EventBus::class);

        return $eventBus;
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $query
     *
     * @throws JsonException
     */
    protected function post(string $url, array $body = [], ?array $query = null): Response
    {
        /** @var AbstractBrowser $client */
        $client = self::getClient();

        if ($query !== null) {
            $url = sprintf('%s?%s', $url, http_build_query($query));
        }

        $client->request(
            'post',
            $url,
            [],
            [],
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            json_encode($body)
        );

        /** @var Response $response */
        $response = $client->getResponse();

        return $response;
    }
}
