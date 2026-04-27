<?php

declare(strict_types=1);

use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use App\Core\View;
use Bramus\Router\Router;
use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$router = new Router();

$router->get('/', function (): void {
    (new HomeController())->index();
});

$router->get('/category/(\d+)', function (string $id): void {
    (new CategoryController())->show((int) $id);
});

$router->get('/post/(\d+)', function (string $id): void {
    (new PostController())->show((int) $id);
});

$router->set404(function (): void {
    http_response_code(404);
    (new View())->render('404.tpl');
});

$router->run();
