<?php
$appConfig = require __DIR__ . '/../config/app.php';

date_default_timezone_set(isset($appConfig['timezone']) ? $appConfig['timezone'] : 'Asia/Makassar');

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $prefixLength = strlen($prefix);

    if (strncmp($prefix, $class, $prefixLength) !== 0) {
        return;
    }

    $relativeClass = substr($class, $prefixLength);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

require_once __DIR__ . '/../app/Helpers/url.php';

return new App\Core\App();
