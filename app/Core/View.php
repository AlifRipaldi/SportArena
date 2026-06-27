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

        if ($layout === 'layouts/admin') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (empty($_SESSION['admin_csrf'])) {
                $_SESSION['admin_csrf'] = bin2hex(random_bytes(24));
            }
            $data['adminToken'] = $_SESSION['admin_csrf'];
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
