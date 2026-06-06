<?php

namespace App\Core;

class Controller
{
    protected function view($view, array $data = array(), $layout = 'layouts/main')
    {
        return View::render($view, $data, $layout);
    }

    protected function redirect($path)
    {
        header('Location: ' . app_url($path));
        exit;
    }
}
