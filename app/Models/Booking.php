<?php

namespace App\Models;

class Booking extends Model
{
    public function create(array $data)
    {
        $connection = $this->db();
        $statement = mysqli_prepare(
            $connection,
            'INSERT INTO booking (ID_Booking, ID_Jadwal, ID_User, Waktu_transaksi, Total_harga) VALUES (?, ?, ?, ?, ?)'
        );

        if (!$statement) {
            return false;
        }

        mysqli_stmt_bind_param(
            $statement,
            'ssssi',
            $data['ID_Booking'],
            $data['ID_Jadwal'],
            $data['ID_User'],
            $data['Waktu_transaksi'],
            $data['Total_harga']
        );

        return mysqli_stmt_execute($statement);
    }
}
