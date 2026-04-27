<?php

declare(strict_types=1);

namespace App\Core;

use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Services\BlogService;

class ServiceFactory
{
    public static function blog(): BlogService
    {
        $pdo = Database::getInstance();

        return new BlogService(
            new CategoryRepository($pdo),
            new PostRepository($pdo)
        );
    }
}
