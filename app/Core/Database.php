<?php

namespace App\Core;

use RuntimeException;

class Database
{
    protected static $connection;

    public static function connection()
    {
        if (self::$connection) {
            return self::$connection;
        }

        mysqli_report(MYSQLI_REPORT_OFF);

        $config = require __DIR__ . '/../../config/database.php';
        $connection = mysqli_connect(
            $config['host'],
            $config['username'],
            $config['password'],
            $config['database']
        );

        if (!$connection) {
            throw new RuntimeException('Koneksi database gagal: ' . mysqli_connect_error());
        }

        if (isset($config['charset'])) {
            mysqli_set_charset($connection, $config['charset']);
        }

        self::$connection = $connection;

        return self::$connection;
    }
}
