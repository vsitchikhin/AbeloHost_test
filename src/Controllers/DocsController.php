<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

class DocsController
{
    private const ASSET_CONTENT_TYPES = [
        'swagger-ui-bundle.js'            => 'application/javascript; charset=utf-8',
        'swagger-ui-standalone-preset.js' => 'application/javascript; charset=utf-8',
        'swagger-ui.css'                  => 'text/css; charset=utf-8',
        'favicon-16x16.png'               => 'image/png',
        'favicon-32x32.png'               => 'image/png',
    ];

    private View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function index(): void
    {
        $this->view->render('docs/swagger.tpl');
    }

    public function openapi(): void
    {
        $path = dirname(__DIR__, 2) . '/docs/openapi.yaml';

        if (!is_file($path)) {
            $this->notFound();
            return;
        }

        header('Content-Type: application/yaml; charset=utf-8');
        readfile($path);
    }

    public function asset(string $filename): void
    {
        if (!array_key_exists($filename, self::ASSET_CONTENT_TYPES)) {
            $this->notFound();
            return;
        }

        $path = dirname(__DIR__, 2) . '/node_modules/swagger-ui-dist/' . $filename;

        if (!is_file($path)) {
            $this->notFound();
            return;
        }

        header('Content-Type: ' . self::ASSET_CONTENT_TYPES[$filename]);
        readfile($path);
    }

    private function notFound(): void
    {
        http_response_code(404);
    }
}
