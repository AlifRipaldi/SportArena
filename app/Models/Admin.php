<?php

namespace App\Models;

class Admin extends Model
{
    protected function table()
    {
        $connection = $this->db();
        $result = mysqli_query($connection, "SHOW TABLES LIKE 'users'");

        return $result && mysqli_num_rows($result) > 0 ? 'users' : 'user';
    }

    public function findByEmail($email)
    {
        $sql = 'SELECT * FROM `' . $this->table() . '` WHERE Email = ? AND LOWER(Role) IN (?, ?, ?) LIMIT 1';
        $statement = mysqli_prepare($this->db(), $sql);

        if (!$statement) {
            return null;
        }

        $admin = 'admin';
        $administrator = 'administrator';
        $superadmin = 'superadmin';

        mysqli_stmt_bind_param($statement, 'ssss', $email, $admin, $administrator, $superadmin);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);

        return $result ? mysqli_fetch_assoc($result) : null;
    }
}
