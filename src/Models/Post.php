<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Post
{
    private const ALLOWED_ORDER     = ['views', 'published_at'];
    private const ALLOWED_DIRECTION = ['asc', 'desc'];

    /**
     * Finds a post by ID and attaches its categories.
     *
     * @return array<string, mixed>|null
     */
    public static function findById(int $id): ?array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT id, title, slug, description, content, image, views, published_at
             FROM posts
             WHERE id = ?'
        );
        $stmt->execute([$id]);

        $post = $stmt->fetch();

        if ($post === false) {
            return null;
        }

        $stmt = $pdo->prepare(
            'SELECT c.id, c.name, c.slug
             FROM categories c
             INNER JOIN post_categories pc ON pc.category_id = c.id
             WHERE pc.post_id = ?'
        );
        $stmt->execute([$id]);

        /** @var list<array<string, mixed>> $categories */
        $categories        = $stmt->fetchAll();
        $post['categories'] = $categories;

        return $post;
    }

    /**
     * Returns paginated posts for a category with configurable sort.
     *
     * @return list<array<string, mixed>>
     */
    public static function getByCategoryId(
        int $categoryId,
        string $orderBy = 'published_at',
        string $direction = 'desc',
        int $page = 1,
        int $perPage = 9
    ): array {
        // Whitelist validation to prevent SQL injection
        $orderBy   = in_array($orderBy, self::ALLOWED_ORDER, true) ? $orderBy : 'published_at';
        $direction = in_array($direction, self::ALLOWED_DIRECTION, true) ? $direction : 'desc';
        $offset    = ($page - 1) * $perPage;

        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT p.id, p.title, p.slug, p.description, p.image, p.views, p.published_at
             FROM posts p
             INNER JOIN post_categories pc ON pc.post_id = p.id
             WHERE pc.category_id = ?
             ORDER BY p.{$orderBy} {$direction}
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$categoryId, $perPage, $offset]);

        /** @var list<array<string, mixed>> $result */
        $result = $stmt->fetchAll();

        return $result;
    }

    public static function countByCategory(int $categoryId): int
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT COUNT(DISTINCT p.id)
             FROM posts p
             INNER JOIN post_categories pc ON pc.post_id = p.id
             WHERE pc.category_id = ?'
        );
        $stmt->execute([$categoryId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Returns posts from the same categories, excluding the given post.
     *
     * @param list<int> $categoryIds
     * @return list<array<string, mixed>>
     */
    public static function getSimilar(int $postId, array $categoryIds, int $limit = 3): array
    {
        $pdo = Database::getInstance();

        if (empty($categoryIds)) {
            $stmt = $pdo->prepare(
                'SELECT id, title, slug, description, image, views, published_at
                 FROM posts
                 WHERE id != ?
                 ORDER BY published_at DESC
                 LIMIT ?'
            );
            $stmt->execute([$postId, $limit]);

            /** @var list<array<string, mixed>> $result */
            $result = $stmt->fetchAll();

            return $result;
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $stmt         = $pdo->prepare(
            "SELECT DISTINCT p.id, p.title, p.slug, p.description, p.image, p.views, p.published_at
             FROM posts p
             INNER JOIN post_categories pc ON pc.post_id = p.id
             WHERE pc.category_id IN ({$placeholders})
               AND p.id != ?
             ORDER BY p.published_at DESC
             LIMIT ?"
        );
        $stmt->execute([...$categoryIds, $postId, $limit]);

        /** @var list<array<string, mixed>> $result */
        $result = $stmt->fetchAll();

        return $result;
    }

    public static function incrementViews(int $id): void
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = ?');
        $stmt->execute([$id]);
    }
}
