<?php

declare(strict_types=1);

return [
    'name'  => (string) ($_ENV['APP_NAME'] ?? 'Blog'),
    'env'   => (string) ($_ENV['APP_ENV'] ?? 'production'),
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url'   => (string) ($_ENV['APP_URL'] ?? 'http://localhost:8080'),
];
