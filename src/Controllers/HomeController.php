<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\ServiceFactory;
use App\Services\BlogService;

class HomeController extends BaseController
{
    private BlogService $blog;

    public function __construct(?BlogService $blog = null)
    {
        parent::__construct();
        $this->blog = $blog ?? ServiceFactory::blog();
    }

    public function index(): void
    {
        $this->view->render('home.tpl', $this->blog->getHomePageData());
    }
}
