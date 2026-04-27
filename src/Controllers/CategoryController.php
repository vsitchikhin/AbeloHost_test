<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\NotFoundException;
use App\Core\ServiceFactory;
use App\Core\View;
use App\Services\BlogService;

class CategoryController
{
    private View $view;
    private BlogService $blog;

    public function __construct(?BlogService $blog = null)
    {
        $this->view = new View();
        $this->blog = $blog ?? ServiceFactory::blog();
    }

    public function show(string $slug): void
    {
        $sortBy    = isset($_GET['sort']) ? (string) $_GET['sort'] : 'published_at';
        $direction = isset($_GET['dir'])  ? (string) $_GET['dir']  : 'desc';
        $page      = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        try {
            $data = $this->blog->getCategoryPageData($slug, $sortBy, $direction, $page);
        } catch (NotFoundException) {
            $this->renderNotFound();
            return;
        }

        $this->view->render('category.tpl', $data);
    }

    public function showById(int $id): void
    {
        $sortBy    = isset($_GET['sort']) ? (string) $_GET['sort'] : 'published_at';
        $direction = isset($_GET['dir'])  ? (string) $_GET['dir']  : 'desc';
        $page      = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        try {
            $data = $this->blog->getCategoryPageDataById($id, $sortBy, $direction, $page);
        } catch (NotFoundException) {
            $this->renderNotFound();
            return;
        }

        $this->view->render('category.tpl', $data);
    }

    private function renderNotFound(): void
    {
        http_response_code(404);
        $this->view->render('404.tpl');
    }
}
