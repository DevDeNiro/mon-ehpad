<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => [
        'all' => true,
    ],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => [
        'all' => true,
    ],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => [
        'all' => true,
    ],
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => [
        'dev' => true,
        'test' => true,
    ],
    DAMA\DoctrineTestBundle\DAMADoctrineTestBundle::class => [
        'test' => true,
    ],
    Symfony\Bundle\TwigBundle\TwigBundle::class => [
        'all' => true,
    ],
    Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => [
        'all' => true,
    ],
    Symfony\Bundle\MakerBundle\MakerBundle::class => [
        'dev' => true,
    ],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => [
        'all' => true,
    ],
    Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle::class => [
        'dev' => true,
        'test' => true,
    ],
    Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle::class => [
        'dev' => true,
        'test' => true,
    ],
    Hautelook\AliceBundle\HautelookAliceBundle::class => [
        'dev' => true,
        'test' => true,
    ],
    NotFloran\MjmlBundle\MjmlBundle::class => [
        'all' => true,
    ],
    Misd\PhoneNumberBundle\MisdPhoneNumberBundle::class => [
        'all' => true,
    ],
];
