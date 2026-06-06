<?php

namespace App\Core;

use RuntimeException;

class Router
{
    protected $routes = array();

    public function get($uri, $handler)
    {
        $this->add('GET', $uri, $handler);
    }

    public function post($uri, $handler)
    {
        $this->add('POST', $uri, $handler);
    }

    public function add($method, $uri, $handler)
    {
        $method = strtoupper($method);
        $uri = $this->normalize($uri);

        if (!isset($this->routes[$method])) {
            $this->routes[$method] = array();
        }

        $this->routes[$method][] = array(
            'uri' => $uri,
            'regex' => $this->compile($uri),
            'handler' => $handler,
        );
    }

    public function dispatch($method, $uri)
    {
        $method = strtoupper($method);
        $uri = $this->normalize($uri);
        $routes = isset($this->routes[$method]) ? $this->routes[$method] : array();

        foreach ($routes as $route) {
            if (preg_match($route['regex'], $uri, $matches)) {
                $params = array();

                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $params[] = $value;
                    }
                }

                return $this->execute($route['handler'], $params);
            }
        }

        http_response_code(404);

        return View::render('errors/404', array(
            'title' => 'Halaman tidak ditemukan | Arena Sport',
        ));
    }

    protected function execute($handler, array $params)
    {
        if (is_array($handler)) {
            $class = $handler[0];
            $method = $handler[1];
            $controller = new $class();

            return call_user_func_array(array($controller, $method), $params);
        }

        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        throw new RuntimeException('Route handler tidak valid.');
    }

    protected function normalize($uri)
    {
        $uri = '/' . trim($uri, '/');

        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    protected function compile($uri)
    {
        if ($uri === '/') {
            return '#^/$#';
        }

        $segments = explode('/', trim($uri, '/'));
        $compiled = array();

        foreach ($segments as $segment) {
            if (preg_match('/^\{([A-Za-z_][A-Za-z0-9_]*)\}$/', $segment, $matches)) {
                $compiled[] = '(?P<' . $matches[1] . '>[^/]+)';
                continue;
            }

            $compiled[] = preg_quote($segment, '#');
        }

        return '#^/' . implode('/', $compiled) . '$#';
    }
}
