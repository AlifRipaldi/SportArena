<?php

namespace App\Models;

use App\Core\Database;

abstract class Model
{
    protected function db()
    {
        return Database::connection();
    }
}
