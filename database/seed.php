<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Database;
use Dotenv\Dotenv;
use Faker\Factory;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pdo   = Database::getInstance();
$faker = Factory::create();

echo "Clearing existing data...\n";
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('TRUNCATE TABLE post_categories');
$pdo->exec('TRUNCATE TABLE posts');
$pdo->exec('TRUNCATE TABLE categories');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

// --- Categories ---
echo "Seeding categories...\n";

$categoryData = [
    ['Technology',  'Latest trends and insights from the world of technology and software.'],
    ['Design',      'UI/UX principles, tools, typography, and visual inspiration.'],
    ['Development', 'Web and software development tutorials, patterns, and best practices.'],
    ['Business',    'Entrepreneurship, startups, product strategy, and growth.'],
    ['Science',     'Discoveries, research, and breakthroughs from across scientific fields.'],
    ['Culture',     'Art, literature, lifestyle, and cultural perspectives from around the world.'],
];

$categoryIds    = [];
$insertCategory = $pdo->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');

foreach ($categoryData as [$name, $description]) {
    $insertCategory->execute([$name, $description]);
    $categoryIds[] = (int) $pdo->lastInsertId();
}

echo sprintf("  %d categories inserted.\n", count($categoryIds));

// --- Posts ---
echo "Seeding posts...\n";

$insertPost = $pdo->prepare(
    'INSERT INTO posts (title, description, content, image, views, published_at)
     VALUES (?, ?, ?, ?, ?, ?)'
);
$insertRelation = $pdo->prepare(
    'INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)'
);

for ($i = 0; $i < 40; $i++) {
    $seed = $faker->unique()->numberBetween(1, 9999);

    $insertPost->execute([
        ucfirst($faker->sentence(rand(4, 8), false)),
        $faker->paragraph(2),
        implode("\n\n", $faker->paragraphs(rand(4, 8))),
        "https://picsum.photos/seed/{$seed}/800/450",
        $faker->numberBetween(0, 9999),
        $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
    ]);

    $postId   = (int) $pdo->lastInsertId();
    $shuffled = $categoryIds;
    shuffle($shuffled);
    $assigned = array_slice($shuffled, 0, rand(1, min(3, count($categoryIds))));

    foreach ($assigned as $catId) {
        $insertRelation->execute([$postId, $catId]);
    }
}

echo "  40 posts inserted with random categories.\n";
echo "\nSeeding complete.\n";
