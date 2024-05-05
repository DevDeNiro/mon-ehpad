<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Alice\Factory;

use App\Core\Domain\Model\ValueObject\Id;

final class IdFactory
{
    public const string ADMIN_1 = '01HX56HM4SR2MJKBMGYZ7JH6XX';

    /**
     * @var array<string, Id>
     */
    private array $ids = [];

    public function __construct()
    {
        $this->ids['user_1'] = Id::fromString(self::ADMIN_1);
    }

    public function create(string $scope, int $current): Id
    {
        $key = sprintf('%s_%d', $scope, $current);

        if (!isset($this->ids[$key])) {
            $this->ids[$key] = new Id();
        }

        return $this->ids[$key];
    }
}
