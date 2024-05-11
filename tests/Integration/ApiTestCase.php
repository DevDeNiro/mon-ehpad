<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Core\Domain\Validation\Assert;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\Entity\User;
use App\Security\Infrastructure\Symfony\Security\SymfonyUser;
use Cake\Chronos\Chronos;
use Doctrine\ORM\EntityManagerInterface;
use Safe\Exceptions\JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use function Safe\json_encode;

abstract class ApiTestCase extends WebTestCase
{
    use ApiAssertionsTrait;

    protected function setUp(): void
    {
        self::createClient();
        Chronos::setTestNow();
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public function getService(string $id): object
    {
        /** @var T $service */
        $service = self::getContainer()->get($id);

        return $service;
    }

    public function refresh(object $entity): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $entityManager->refresh($entity);
    }

    protected function login(string $email = 'admin+1@email.com'): User
    {
        $userRepository = $this->getService(UserRepository::class);
        $user = $userRepository->findOneByEmail($email);

        Assert::notNull($user);

        /** @var KernelBrowser $client */
        $client = self::getClient();

        $client->loginUser(new SymfonyUser($user));

        return $user;
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $query
     *
     * @throws JsonException
     */
    protected function post(string $url, array $body = [], ?array $query = null): Response
    {
        /** @var KernelBrowser $client */
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
