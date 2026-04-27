<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Database;
use App\Core\MigrationInterface;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pdo = Database::getInstance();

$pdo->exec(<<<'SQL'
    CREATE TABLE IF NOT EXISTS migrations (
        id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
        migration    VARCHAR(255) NOT NULL,
        executed_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    SQL);

/** @var list<string> $executed */
$executed = $pdo->query('SELECT migration FROM migrations ORDER BY id ASC')
    ->fetchAll(PDO::FETCH_COLUMN);

$files = glob(__DIR__ . '/migrations/*.php');

if ($files === false || count($files) === 0) {
    echo "No migration files found.\n";
    exit(0);
}

sort($files);

$ran = 0;

foreach ($files as $file) {
    $name = basename($file);

    if (in_array($name, $executed, true)) {
        echo "  Skipping:  {$name}\n";
        continue;
    }

    /** @var MigrationInterface $migration */
    $migration = require $file;
    $migration->up($pdo);

    $stmt = $pdo->prepare('INSERT INTO migrations (migration) VALUES (?)');
    $stmt->execute([$name]);

    echo "  Migrated:  {$name}\n";
    $ran++;
}

echo $ran > 0
    ? "\nDone. {$ran} migration(s) executed.\n"
    : "\nNothing to migrate.\n";
