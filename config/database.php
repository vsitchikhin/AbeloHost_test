<?php

declare(strict_types=1);

return [
    'host'     => (string) ($_ENV['DB_HOST'] ?? 'db'),
    'port'     => (string) ($_ENV['DB_PORT'] ?? '3306'),
    'database' => (string) ($_ENV['DB_DATABASE'] ?? 'blog'),
    'username' => (string) ($_ENV['DB_USERNAME'] ?? 'user'),
    'password' => (string) ($_ENV['DB_PASSWORD'] ?? 'password'),
];
