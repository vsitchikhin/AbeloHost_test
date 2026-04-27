<?php

declare(strict_types=1);

use App\Core\MigrationInterface;

return new class implements MigrationInterface {
    public function up(\PDO $pdo): void
    {
        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS posts (
                id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
                title        VARCHAR(255) NOT NULL,
                description  TEXT         NOT NULL DEFAULT '',
                content      LONGTEXT     NOT NULL,
                image        VARCHAR(500) NULL,
                views        INT UNSIGNED NOT NULL DEFAULT 0,
                published_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                INDEX idx_published_at (published_at),
                INDEX idx_views (views)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL);
    }

    public function down(\PDO $pdo): void
    {
        $pdo->exec('DROP TABLE IF EXISTS posts');
    }
};
