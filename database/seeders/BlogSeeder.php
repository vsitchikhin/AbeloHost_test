<?php

declare(strict_types=1);

namespace Database\Seeders;

use Faker\Factory;
use Faker\Generator;
use PDO;

class BlogSeeder
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function run(PDO $pdo): void
    {
        $this->clearData($pdo);
        $categoryIds = $this->seedCategories($pdo);
        $this->seedPosts($pdo, $categoryIds);

        echo "\nSeeding complete.\n";
    }

    private function clearData(PDO $pdo): void
    {
        echo "Clearing existing data...\n";
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $pdo->exec('TRUNCATE TABLE post_categories');
        $pdo->exec('TRUNCATE TABLE posts');
        $pdo->exec('TRUNCATE TABLE categories');
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * @return list<int>
     */
    private function seedCategories(PDO $pdo): array
    {
        echo "Seeding categories...\n";

        $rows = [
            ['Technology',  'Latest trends and insights from the world of technology and software.'],
            ['Design',      'UI/UX principles, tools, typography, and visual inspiration.'],
            ['Development', 'Web and software development tutorials, patterns, and best practices.'],
            ['Business',    'Entrepreneurship, startups, product strategy, and growth.'],
            ['Science',     'Discoveries, research, and breakthroughs from across scientific fields.'],
            ['Culture',     'Art, literature, lifestyle, and cultural perspectives from around the world.'],
        ];

        $stmt = $pdo->prepare('INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)');
        $ids  = [];

        foreach ($rows as [$name, $description]) {
            $stmt->execute([$name, $this->slugify($name), $description]);
            $ids[] = (int) $pdo->lastInsertId();
        }

        echo sprintf("  %d categories inserted.\n", count($ids));

        return $ids;
    }

    /**
     * @param list<int> $categoryIds
     */
    private function seedPosts(PDO $pdo, array $categoryIds): void
    {
        echo "Seeding posts...\n";

        $insertPost = $pdo->prepare(
            'INSERT INTO posts (title, slug, description, content, image, views, published_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $insertRelation = $pdo->prepare(
            'INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)'
        );

        for ($i = 0; $i < 40; $i++) {
            $seed  = $this->faker->unique()->numberBetween(1, 9999);
            $title = ucfirst($this->faker->sentence(rand(4, 8), false));

            $insertPost->execute([
                $title,
                sprintf('%s-%d', $this->slugify($title), $i + 1),
                $this->faker->paragraph(2),
                implode("\n\n", $this->faker->paragraphs(rand(4, 8))),
                "https://picsum.photos/seed/{$seed}/800/450",
                $this->faker->numberBetween(0, 9999),
                $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
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
    }

    private function slugify(string $value): string
    {
        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');

        return $slug !== '' ? $slug : 'item';
    }
}
