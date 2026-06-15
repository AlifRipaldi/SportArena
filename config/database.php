<?php

return array(
    'host' => getenv('DB_HOST') ?: 'localhost',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'database' => getenv('DB_DATABASE') ?: 'arena sport',
    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
);
