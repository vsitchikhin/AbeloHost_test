<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pdo = Database::getInstance();

echo "Clearing existing data for E2E...\n";
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('TRUNCATE TABLE post_categories');
$pdo->exec('TRUNCATE TABLE posts');
$pdo->exec('TRUNCATE TABLE categories');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

$insertCategory = $pdo->prepare(
    'INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)'
);

$categories = [
    'technology' => [
        'Technology',
        'Technology news, tutorials, and practical engineering notes.',
    ],
    'design' => [
        'Design',
        'Product design, user experience, and visual systems.',
    ],
];

$categoryIds = [];

foreach ($categories as $slug => [$name, $description]) {
    $insertCategory->execute([$name, $slug, $description]);
    $categoryIds[$slug] = (int) $pdo->lastInsertId();
}

$insertPost = $pdo->prepare(
    'INSERT INTO posts (title, slug, description, content, image, views, published_at)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);
$insertRelation = $pdo->prepare(
    'INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)'
);

$posts = [
    [
        'title'        => 'Newest Technology Article',
        'slug'         => 'newest-technology-article',
        'views'        => 35,
        'published_at' => '2026-04-20 10:00:00',
        'categories'   => ['technology'],
    ],
    [
        'title'        => 'Most Viewed Technology Article',
        'slug'         => 'most-viewed-technology-article',
        'views'        => 999,
        'published_at' => '2026-04-10 10:00:00',
        'categories'   => ['technology'],
    ],
    [
        'title'        => 'First Post',
        'slug'         => 'first-post',
        'views'        => 10,
        'published_at' => '2026-01-01 10:00:00',
        'categories'   => ['technology', 'design'],
    ],
    [
        'title'        => 'Design Systems Guide',
        'slug'         => 'design-systems-guide',
        'views'        => 41,
        'published_at' => '2026-03-08 10:00:00',
        'categories'   => ['design'],
    ],
    [
        'title'        => 'Views Counter Post',
        'slug'         => 'views-counter-post',
        'views'        => 10,
        'published_at' => '2026-03-09 10:00:00',
        'categories'   => ['design'],
    ],
];

for ($index = 4; $index <= 12; $index++) {
    $posts[] = [
        'title'        => sprintf('Technology Article %02d', $index),
        'slug'         => sprintf('technology-article-%02d', $index),
        'views'        => 100 - $index,
        'published_at' => sprintf('2026-03-%02d 10:00:00', 28 - $index),
        'categories'   => ['technology'],
    ];
}

foreach ($posts as $post) {
    $insertPost->execute([
        $post['title'],
        $post['slug'],
        sprintf('Description for %s.', $post['title']),
        sprintf(
            "%s content paragraph one.\n\n%s content paragraph two.",
            $post['title'],
            $post['title']
        ),
        sprintf('https://picsum.photos/seed/%s/800/450', $post['slug']),
        $post['views'],
        $post['published_at'],
    ]);

    $postId = (int) $pdo->lastInsertId();

    foreach ($post['categories'] as $categorySlug) {
        $insertRelation->execute([$postId, $categoryIds[$categorySlug]]);
    }
}

echo sprintf("E2E seed complete: %d categories, %d posts.\n", count($categoryIds), count($posts));
