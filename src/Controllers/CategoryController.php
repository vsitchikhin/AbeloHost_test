<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Category;
use App\Models\Post;

class CategoryController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function show(int $id): void
    {
        $category = Category::findById($id);

        if ($category === null) {
            http_response_code(404);
            $this->view->render('404.tpl');
            return;
        }

        $sortBy    = isset($_GET['sort']) ? (string) $_GET['sort'] : 'published_at';
        $direction = isset($_GET['dir'])  ? (string) $_GET['dir']  : 'desc';
        $page      = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage   = 9;

        $posts      = Post::getByCategoryId($id, $sortBy, $direction, $page, $perPage);
        $total      = Post::countByCategory($id);
        $totalPages = (int) ceil($total / $perPage);

        $this->view->render('category.tpl', [
            'category'   => $category,
            'posts'      => $posts,
            'page'       => $page,
            'totalPages' => $totalPages,
            'sortBy'     => $sortBy,
            'direction'  => $direction,
        ]);
    }
}
