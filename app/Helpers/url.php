<?php

if (!function_exists('app_url')) {
    function app_url($path = '')
    {
        if (preg_match('#^https?://#', $path)) {
            return $path;
        }

        $base = '';

        if (isset($_SERVER['SCRIPT_NAME'])) {
            $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
            $base = ($base === '/' || $base === '.') ? '' : rtrim($base, '/');
            $base = app_encode_path(rawurldecode($base));
        }

        $path = ltrim($path, '/');

        return $path === '' ? $base . '/' : $base . '/' . $path;
    }
}

if (!function_exists('app_encode_path')) {
    function app_encode_path($path)
    {
        $segments = explode('/', $path);

        foreach ($segments as $index => $segment) {
            $segments[$index] = rawurlencode($segment);
        }

        return implode('/', $segments);
    }
}

if (!function_exists('app_asset')) {
    function app_asset($path)
    {
        return app_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('app_asset_versioned')) {
    function app_asset_versioned($path)
    {
        $cleanPath = ltrim($path, '/');
        $assetPath = __DIR__ . '/../../assets/' . $cleanPath;
        $separator = strpos($cleanPath, '?') === false ? '?' : '&';
        $version = is_file($assetPath) ? filemtime($assetPath) : time();

        return app_asset($cleanPath . $separator . 'v=' . $version);
    }
}

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
