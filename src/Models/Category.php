<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Category
{
    /**
     * Returns all categories that have at least one post,
     * each populated with their $limit most recent posts.
     *
     * @return list<array<string, mixed>>
     */
    public static function getWithLatestPosts(int $limit = 3): array
    {
        $pdo = Database::getInstance();

        /** @var list<array<string, mixed>> $categories */
        $categories = $pdo->query(
            'SELECT id, name, description
             FROM categories
             WHERE id IN (SELECT DISTINCT category_id FROM post_categories)
             ORDER BY name ASC'
        )->fetchAll();

        if (empty($categories)) {
            return [];
        }

        $categoryIds  = array_column($categories, 'id');
        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $stmt = $pdo->prepare(
            "SELECT ranked.*
             FROM (
                 SELECT p.id, p.title, p.description, p.image, p.views, p.published_at,
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
    public static function findById(int $id): ?array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('SELECT id, name, description FROM categories WHERE id = ?');
        $stmt->execute([$id]);

        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }
}
