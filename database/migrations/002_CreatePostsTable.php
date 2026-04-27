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
                slug         VARCHAR(200) NOT NULL,
                description  TEXT         NOT NULL,
                content      LONGTEXT     NOT NULL,
                image        VARCHAR(500) NOT NULL,
                views        INT UNSIGNED NOT NULL DEFAULT 0,
                published_at DATETIME     NOT NULL,
                created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uq_posts_slug (slug),
                INDEX idx_posts_published_at (published_at),
                INDEX idx_posts_views (views),
                INDEX idx_posts_published_id (published_at, id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL);
    }

    public function down(\PDO $pdo): void
    {
        $pdo->exec('DROP TABLE IF EXISTS posts');
    }
};
