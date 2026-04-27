<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class CategoryRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findWithLatestPosts(int $limit = 3): array
    {
        /** @var list<array<string, mixed>> $categories */
        $categories = $this->pdo->query(
            'SELECT id, name, slug, description
             FROM categories
             WHERE id IN (SELECT DISTINCT category_id FROM post_categories)
             ORDER BY name ASC'
        )->fetchAll();

        if (empty($categories)) {
            return [];
        }

        $categoryIds  = array_map('intval', array_column($categories, 'id'));
        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $stmt = $this->pdo->prepare(
            "SELECT ranked.*
             FROM (
                 SELECT p.id, p.title, p.slug, p.description, p.image, p.views, p.published_at,
                        pc.category_id,
                        ROW_NUMBER() OVER (PARTITION BY pc.category_id ORDER BY p.published_at DESC) AS rn
                 FROM posts p
                 INNER JOIN post_categories pc ON pc.post_id = p.id
                 WHERE pc.category_id IN ({$placeholders})
             ) ranked
             WHERE ranked.rn <= ?
             ORDER BY ranked.category_id, ranked.published_at DESC"
        );
        $stmt->execute([...$categoryIds, $limit]);

        /** @var list<array<string, mixed>> $allPosts */
        $allPosts = $stmt->fetchAll();

        $postsByCategory = [];
        foreach ($allPosts as $post) {
            $postsByCategory[(int) $post['category_id']][] = $post;
        }

        foreach ($categories as &$category) {
            $category['posts'] = $postsByCategory[(int) $category['id']] ?? [];
        }
        unset($category);

        return $categories;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, slug, description FROM categories WHERE slug = ?');
        $stmt->execute([$slug]);

        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, slug, description FROM categories WHERE id = ?');
        $stmt->execute([$id]);

        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }
}
