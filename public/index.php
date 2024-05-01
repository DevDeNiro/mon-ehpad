<?php

use App\Core\Infrastructure\Symfony\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return static fn (array $context): \App\Core\Infrastructure\Symfony\Kernel => new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
