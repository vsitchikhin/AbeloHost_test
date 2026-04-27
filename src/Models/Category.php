<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Repositories\CategoryRepository;

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
        return self::repository()->findWithLatestPosts($limit);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findById(int $id): ?array
    {
        return self::repository()->findById($id);
    }

    private static function repository(): CategoryRepository
    {
        return new CategoryRepository(Database::getInstance());
    }
}
