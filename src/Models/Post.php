<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Repositories\PostRepository;

class Post
{
    /**
     * Finds a post by ID and attaches its categories.
     *
     * @return array<string, mixed>|null
     */
    public static function findById(int $id): ?array
    {
        return self::repository()->findByIdWithCategories($id);
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
        $offset = ($page - 1) * $perPage;

        return self::repository()->findByCategory($categoryId, $orderBy, $direction, $perPage, $offset);
    }

    public static function countByCategory(int $categoryId): int
    {
        return self::repository()->countByCategory($categoryId);
    }

    /**
     * Returns posts from the same categories, excluding the given post.
     *
     * @param list<int> $categoryIds
     * @return list<array<string, mixed>>
     */
    public static function getSimilar(int $postId, array $categoryIds, int $limit = 3): array
    {
        return self::repository()->findSimilar($postId, $categoryIds, $limit);
    }

    public static function incrementViews(int $id): void
    {
        self::repository()->incrementViews($id);
    }

    private static function repository(): PostRepository
    {
        return new PostRepository(Database::getInstance());
    }
}
