<?php

namespace App\Core;

class App
{
    protected $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function router()
    {
        return $this->router;
    }

    public function run()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';

        $this->router->dispatch($method, $this->currentPath());
    }

    protected function currentPath()
    {
        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $uri = parse_url($requestUri, PHP_URL_PATH);

        if ($uri === false || $uri === null || $uri === '') {
            $uri = '/';
        }

        $uri = rawurldecode($uri);
        $scriptName = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';
        $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        $scriptFile = basename($scriptName);

        if ($scriptFile !== '' && substr($uri, -strlen('/' . $scriptFile)) === '/' . $scriptFile) {
            return '/';
        }

        if ($scriptDir !== '' && $scriptDir !== '.' && $scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

        $uri = '/' . trim($uri, '/');

        return $uri === '/' ? '/' : rtrim($uri, '/');
    }
}
