<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class PostRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findBySlugWithCategories(string $slug): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, slug, description, content, image, views, published_at
             FROM posts
             WHERE slug = ?'
        );
        $stmt->execute([$slug]);

        $post = $stmt->fetch();

        if ($post === false) {
            return null;
        }

        $post['categories'] = $this->findCategoriesForPost((int) $post['id']);

        return $post;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByIdWithCategories(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, slug, description, content, image, views, published_at
             FROM posts
             WHERE id = ?'
        );
        $stmt->execute([$id]);

        $post = $stmt->fetch();

        if ($post === false) {
            return null;
        }

        $post['categories'] = $this->findCategoriesForPost((int) $post['id']);

        return $post;
    }

    private const ALLOWED_ORDER_FIELDS     = ['published_at', 'views'];
    private const ALLOWED_ORDER_DIRECTIONS = ['asc', 'desc'];

    /**
     * @return list<array<string, mixed>>
     */
    public function findByCategory(
        int $categoryId,
        string $orderBy,
        string $direction,
        int $limit,
        int $offset
    ): array {
        $safeOrderBy   = in_array($orderBy, self::ALLOWED_ORDER_FIELDS, true) ? $orderBy : 'published_at';
        $safeDirection = in_array($direction, self::ALLOWED_ORDER_DIRECTIONS, true) ? $direction : 'desc';

        $stmt = $this->pdo->prepare(
            "SELECT p.id, p.title, p.slug, p.description, p.image, p.views, p.published_at
             FROM posts p
             INNER JOIN post_categories pc ON pc.post_id = p.id
             WHERE pc.category_id = ?
             ORDER BY p.{$safeOrderBy} {$safeDirection}
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$categoryId, $limit, $offset]);

        /** @var list<array<string, mixed>> $result */
        $result = $stmt->fetchAll();

        return $result;
    }

    public function countByCategory(int $categoryId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(DISTINCT p.id)
             FROM posts p
             INNER JOIN post_categories pc ON pc.post_id = p.id
             WHERE pc.category_id = ?'
        );
        $stmt->execute([$categoryId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param list<int> $categoryIds
     * @return list<array<string, mixed>>
     */
    public function findSimilar(int $postId, array $categoryIds, int $limit = 3): array
    {
        if (empty($categoryIds)) {
            $stmt = $this->pdo->prepare(
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
        $stmt         = $this->pdo->prepare(
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

    public function incrementViews(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function findCategoriesForPost(int $postId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.id, c.name, c.slug
             FROM categories c
             INNER JOIN post_categories pc ON pc.category_id = c.id
             WHERE pc.post_id = ?'
        );
        $stmt->execute([$postId]);

        /** @var list<array<string, mixed>> $categories */
        $categories = $stmt->fetchAll();

        return $categories;
    }
}
