<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Alice\Locator;

use Hautelook\AliceBundle\FixtureLocatorInterface;
use Nelmio\Alice\IsAServiceTrait;

final readonly class OrderedFixturesLocator implements FixtureLocatorInterface
{
    use IsAServiceTrait;

    public function __construct(private FixtureLocatorInterface $decoratedFixtureLocator)
    {
    }

    public function locateFiles(array $bundles, string $environment): array
    {
        $files = $this->decoratedFixtureLocator->locateFiles($bundles, $environment);

        /**
         * @var array<string, array{file: string, order: int}> $files
         */
        $files = array_combine(
            array_map('basename', $files),
            array_map(
                static fn (string $file): array => [
                    'file' => $file,
                    'order' => 0,
                ],
                $files
            )
        );

        $files['security_users.yaml']['order'] = 2;
        $files['security_verification_codes.yaml']['order'] = 1;

        uasort(
            $files,
            static fn (array $a, array $b): int => $b['order'] <=> $a['order']
        );

        return array_column($files, 'file');
    }
}
