<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    private \Smarty $smarty;

    public function __construct()
    {
        $root = dirname(__DIR__, 2);

        $this->smarty = new \Smarty();
        $this->smarty->setTemplateDir($root . '/templates');
        $this->smarty->setCompileDir($root . '/storage/smarty/compile');
        $this->smarty->setCacheDir($root . '/storage/smarty/cache');
        $this->smarty->caching = (int) filter_var(
            $_ENV['SMARTY_CACHE'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        /** @var array{name: string, url: string, env: string, debug: bool} $app */
        $app = require $root . '/config/app.php';
        $this->smarty->assign('appName', $app['name']);
        $this->smarty->assign('appUrl', $app['url']);
        $this->smarty->assign('appEnv', $app['env']);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): void
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        $this->smarty->display($template);
    }
}
