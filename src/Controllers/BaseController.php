<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class BaseController
{
    protected Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../Views');

        $this->twig = new Environment($loader, [
            'cache' => __DIR__ . '/../../storage/cache',
            'debug' => true,
            'auto_reload' => true,
        ]);

        $this->twig->addExtension(new DebugExtension());
    }

    public function render(string $template, array $data = []): string
    {
        return $this->twig->render($template, $data);
    }
}