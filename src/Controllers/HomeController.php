<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Category;

class HomeController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function index(): void
    {
        $categories = Category::getWithLatestPosts(3);

        $this->view->render('home.tpl', [
            'categories' => $categories,
        ]);
    }
}
