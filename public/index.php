<?php

use App\Infrastructure\Core\Symfony\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return static fn (array $context): \App\Infrastructure\Core\Symfony\Kernel => new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
