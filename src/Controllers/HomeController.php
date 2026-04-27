<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\ServiceFactory;
use App\Core\View;
use App\Services\BlogService;

class HomeController
{
    private View $view;
    private BlogService $blog;

    public function __construct(?BlogService $blog = null)
    {
        $this->view = new View();
        $this->blog = $blog ?? ServiceFactory::blog();
    }

    public function index(): void
    {
        $this->view->render('home.tpl', $this->blog->getHomePageData());
    }
}
