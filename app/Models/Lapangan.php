<?php

namespace App\Models;

class Lapangan extends Model
{
    public function popular($limit = 4)
    {
        return $this->all($limit);
    }

    public function all($limit = null)
    {
        $connection = $this->db();
        $limitSql = '';

        if ($limit !== null) {
            $limit = max(1, (int) $limit);
            $limitSql = ' LIMIT ' . $limit;
        }

        $rows = array();
        $result = mysqli_query($connection, 'SELECT * FROM lapangan' . $limitSql);

        if (!$result) {
            return $rows;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function countAll()
    {
        $connection = $this->db();
        $result = mysqli_query($connection, 'SELECT COUNT(*) AS total FROM lapangan');

        if (!$result) {
            return 0;
        }

        $row = mysqli_fetch_assoc($result);

        return isset($row['total']) ? (int) $row['total'] : 0;
    }
}
