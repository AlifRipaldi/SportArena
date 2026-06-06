<?php

namespace App\Models;

class Lapangan extends Model
{
    public function popular($limit = 4)
    {
        $limit = max(1, (int) $limit);
        $rows = array();
        $result = mysqli_query($this->db(), 'SELECT * FROM lapangan LIMIT ' . $limit);

        if (!$result) {
            return $rows;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;
    }
}
