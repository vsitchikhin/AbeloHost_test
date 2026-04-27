<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\NotFoundException;
use App\Core\ServiceFactory;
use App\Core\View;
use App\Services\BlogService;

class PostController
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
        try {
            $data = $this->blog->getPostPageData($slug);
        } catch (NotFoundException) {
            $this->renderNotFound();
            return;
        }

        $this->view->render('post.tpl', $data);
    }

    public function showById(int $id): void
    {
        try {
            $data = $this->blog->getPostPageDataById($id);
        } catch (NotFoundException) {
            $this->renderNotFound();
            return;
        }

        $this->view->render('post.tpl', $data);
    }

    private function renderNotFound(): void
    {
        http_response_code(404);
        $this->view->render('404.tpl');
    }
}
