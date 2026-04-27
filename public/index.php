<?php

declare(strict_types=1);

use App\Controllers\CategoryController;
use App\Controllers\DocsController;
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

$router->get('/docs', function (): void {
    (new DocsController())->index();
});

$router->get('/docs/openapi.yaml', function (): void {
    (new DocsController())->openapi();
});

$router->get('/docs/assets/([a-z0-9.-]+)', function (string $filename): void {
    (new DocsController())->asset($filename);
});

$router->get('/category/(\d+)', function (string $id): void {
    (new CategoryController())->showById((int) $id);
});

$router->get('/category/([a-z0-9-]+)', function (string $slug): void {
    (new CategoryController())->show($slug);
});

$router->get('/post/(\d+)', function (string $id): void {
    (new PostController())->showById((int) $id);
});

$router->get('/post/([a-z0-9-]+)', function (string $slug): void {
    (new PostController())->show($slug);
});

$router->set404(function (): void {
    http_response_code(404);
    (new View())->render('404.tpl');
});

$router->run();
