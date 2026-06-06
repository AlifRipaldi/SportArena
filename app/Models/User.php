<?php

namespace App\Models;

class User extends Model
{
    public function findByEmail($email)
    {
        $statement = mysqli_prepare($this->db(), 'SELECT * FROM user WHERE Email = ? LIMIT 1');

        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 's', $email);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);

        return $result ? mysqli_fetch_assoc($result) : null;
    }
}
