<?php

declare(strict_types=1);

use App\Core\MigrationInterface;

return new class implements MigrationInterface {
    public function up(\PDO $pdo): void
    {
        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS categories (
                id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
                name        VARCHAR(255) NOT NULL,
                description TEXT         NOT NULL DEFAULT '',
                created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL);
    }

    public function down(\PDO $pdo): void
    {
        $pdo->exec('DROP TABLE IF EXISTS categories');
    }
};
