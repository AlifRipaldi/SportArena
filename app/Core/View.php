<?php

namespace App\Core;

use RuntimeException;

class View
{
    public static function render($view, array $data = array(), $layout = 'layouts/main')
    {
        $viewFile = self::file($view);

        if (!is_file($viewFile)) {
            throw new RuntimeException('View tidak ditemukan: ' . $view);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutFile = self::file($layout);

        if (!is_file($layoutFile)) {
            throw new RuntimeException('Layout tidak ditemukan: ' . $layout);
        }

        require $layoutFile;
    }

    protected static function file($name)
    {
        return __DIR__ . '/../Views/' . str_replace('.', '/', $name) . '.php';
    }
}
