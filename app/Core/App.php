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
        if ((string) app_setting('maintenance_mode', '0') === '1') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $role = isset($_SESSION['role_user']) ? $_SESSION['role_user'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');
            if (!in_array(strtolower((string) $role), array('admin', 'administrator', 'superadmin'), true)) {
                http_response_code(503);
                echo '<!doctype html><html lang="id"><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Pemeliharaan | Arena Sport</title><body style="font-family:sans-serif;background:#0b1320;color:#fff;display:grid;place-items:center;min-height:100vh;margin:0"><main style="text-align:center;padding:24px"><h1>Arena Sport sedang dalam pemeliharaan</h1><p>Silakan kembali beberapa saat lagi.</p></main></body></html>';
                return;
            }
        }
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
