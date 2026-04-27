<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

abstract class BaseController
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    protected function renderNotFound(): void
    {
        http_response_code(404);
        $this->view->render('404.tpl');
    }
}
