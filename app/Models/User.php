<?php

namespace App\Models;

class User extends Model
{
    protected function table()
    {
        $connection = $this->db();
        $result = mysqli_query($connection, "SHOW TABLES LIKE 'users'");

        return $result && mysqli_num_rows($result) > 0 ? 'users' : 'user';
    }

    public function findByEmail($email)
    {
        $statement = mysqli_prepare($this->db(), 'SELECT * FROM `' . $this->table() . '` WHERE Email = ? LIMIT 1');

        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 's', $email);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);

        return $result ? mysqli_fetch_assoc($result) : null;
    }
}
