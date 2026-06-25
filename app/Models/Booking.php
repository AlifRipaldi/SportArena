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

    public function upcomingByUser($userId, $limit = 6)
    {
        if (!$userId) {
            return array();
        }

        $limit = max(1, (int) $limit);
        $connection = $this->db();
        $statement = mysqli_prepare(
            $connection,
            'SELECT b.ID_Booking, b.Total_harga, j.Tanggal, j.Jam_mulai, j.Jam_selesai, l.Nama_lapangan, l.Jenis_olahraga, l.Lokasi
             FROM booking b
             JOIN jadwal j ON b.ID_Jadwal = j.ID_Jadwal
             JOIN lapangan l ON j.ID_Lapangan = l.ID_Lapangan
             WHERE b.ID_User = ? AND TIMESTAMP(j.Tanggal, j.Jam_mulai) >= NOW()
             ORDER BY j.Tanggal ASC, j.Jam_mulai ASC
             LIMIT ?'
        );

        if (!$statement) {
            return array();
        }

        mysqli_stmt_bind_param($statement, 'si', $userId, $limit);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);

        return $this->mapBookingRows($result, 'upcoming');
    }

    public function pastByUser($userId, $limit = 8)
    {
        if (!$userId) {
            return array();
        }

        $limit = max(1, (int) $limit);
        $connection = $this->db();
        $statement = mysqli_prepare(
            $connection,
            'SELECT b.ID_Booking, b.Total_harga, j.Tanggal, j.Jam_mulai, j.Jam_selesai, l.Nama_lapangan, l.Jenis_olahraga, l.Lokasi
             FROM booking b
             JOIN jadwal j ON b.ID_Jadwal = j.ID_Jadwal
             JOIN lapangan l ON j.ID_Lapangan = l.ID_Lapangan
             WHERE b.ID_User = ? AND TIMESTAMP(j.Tanggal, j.Jam_mulai) < NOW()
             ORDER BY j.Tanggal DESC, j.Jam_mulai DESC
             LIMIT ?'
        );

        if (!$statement) {
            return array();
        }

        mysqli_stmt_bind_param($statement, 'si', $userId, $limit);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);

        return $this->mapBookingRows($result, 'past');
    }

    public function nextUpcomingByUser($userId)
    {
        if (!$userId) {
            return null;
        }

        $connection = $this->db();
        $statement = mysqli_prepare(
            $connection,
            'SELECT b.ID_Booking, b.Total_harga, j.Tanggal, j.Jam_mulai, j.Jam_selesai, l.Nama_lapangan, l.Jenis_olahraga, l.Lokasi
             FROM booking b
             JOIN jadwal j ON b.ID_Jadwal = j.ID_Jadwal
             JOIN lapangan l ON j.ID_Lapangan = l.ID_Lapangan
             WHERE b.ID_User = ? AND TIMESTAMP(j.Tanggal, j.Jam_mulai) >= NOW()
             ORDER BY j.Tanggal ASC, j.Jam_mulai ASC
             LIMIT 1'
        );

        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 's', $userId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $row = mysqli_fetch_assoc($result);

        if (!$row) {
            return null;
        }

        $array = $this->mapBookingRow($row, 'upcoming');
        return $array;
    }

    public function countUpcomingByUser($userId)
    {
        if (!$userId) {
            return 0;
        }

        $connection = $this->db();
        $statement = mysqli_prepare(
            $connection,
            'SELECT COUNT(*) AS total FROM booking b JOIN jadwal j ON b.ID_Jadwal = j.ID_Jadwal WHERE b.ID_User = ? AND TIMESTAMP(j.Tanggal, j.Jam_mulai) >= NOW()'
        );

        if (!$statement) {
            return 0;
        }

        mysqli_stmt_bind_param($statement, 's', $userId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $row = mysqli_fetch_assoc($result);

        return isset($row['total']) ? (int) $row['total'] : 0;
    }

    public function countPastByUser($userId)
    {
        if (!$userId) {
            return 0;
        }

        $connection = $this->db();
        $statement = mysqli_prepare(
            $connection,
            'SELECT COUNT(*) AS total FROM booking b JOIN jadwal j ON b.ID_Jadwal = j.ID_Jadwal WHERE b.ID_User = ? AND TIMESTAMP(j.Tanggal, j.Jam_mulai) < NOW()'
        );

        if (!$statement) {
            return 0;
        }

        mysqli_stmt_bind_param($statement, 's', $userId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $row = mysqli_fetch_assoc($result);

        return isset($row['total']) ? (int) $row['total'] : 0;
    }

    public function recentAll($limit = 6)
    {
        $limit = max(1, (int) $limit);
        $connection = $this->db();
        $accountTable = $this->accountTable($connection);

        $statement = mysqli_prepare(
            $connection,
            "SELECT b.ID_Booking, b.Total_harga, b.ID_User, j.Tanggal, j.Jam_mulai, j.Jam_selesai, l.Nama_lapangan, l.Jenis_olahraga, l.Lokasi,
                    COALESCE(u.Nama, us.Nama, 'Pelanggan') AS Nama_User
             FROM booking b
             JOIN jadwal j ON b.ID_Jadwal = j.ID_Jadwal
             JOIN lapangan l ON j.ID_Lapangan = l.ID_Lapangan
             LEFT JOIN user u ON u.ID_User = b.ID_User
             LEFT JOIN users us ON us.ID_User = b.ID_User
             ORDER BY j.Tanggal DESC, j.Jam_mulai DESC
             LIMIT ?"
        );

        if (!$statement) {
            return array();
        }

        mysqli_stmt_bind_param($statement, 'i', $limit);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);

        return $this->mapBookingRows($result, 'all');
    }

    public function countTodayBookings()
    {
        $connection = $this->db();
        $today = date('Y-m-d');
        $statement = mysqli_prepare(
            $connection,
            'SELECT COUNT(*) AS total FROM booking b JOIN jadwal j ON b.ID_Jadwal = j.ID_Jadwal WHERE j.Tanggal = ?'
        );

        if (!$statement) {
            return 0;
        }

        mysqli_stmt_bind_param($statement, 's', $today);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $row = mysqli_fetch_assoc($result);

        return isset($row['total']) ? (int) $row['total'] : 0;
    }

    public function sumMonthlyRevenue()
    {
        $connection = $this->db();
        $firstDay = date('Y-m-01');
        $statement = mysqli_prepare(
            $connection,
            'SELECT COALESCE(SUM(b.Total_harga), 0) AS total FROM booking b JOIN jadwal j ON b.ID_Jadwal = j.ID_Jadwal WHERE j.Tanggal >= ? AND j.Tanggal <= LAST_DAY(?)'
        );

        if (!$statement) {
            return 0;
        }

        mysqli_stmt_bind_param($statement, 'ss', $firstDay, $firstDay);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $row = mysqli_fetch_assoc($result);

        return isset($row['total']) ? (int) $row['total'] : 0;
    }

    protected function mapBookingRows($result, $mode = 'upcoming')
    {
        $rows = array();

        if (!$result) {
            return $rows;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $this->mapBookingRow($row, $mode);
        }

        return $rows;
    }

    protected function mapBookingRow($row, $mode = 'upcoming')
    {
        $type = isset($row['Jenis_olahraga']) ? $row['Jenis_olahraga'] : 'Futsal';
        $time = isset($row['Jam_mulai']) && isset($row['Jam_selesai']) ? $row['Jam_mulai'] . ' - ' . $row['Jam_selesai'] : '-';
        $duration = $this->calculateDuration($row['Jam_mulai'], $row['Jam_selesai']);
        $isPast = false;

        if ($mode === 'past') {
            $isPast = true;
        } elseif ($mode === 'upcoming') {
            $isPast = false;
        } else {
            $timestamp = strtotime($row['Tanggal'] . ' ' . $row['Jam_mulai']);
            $isPast = $timestamp !== false && $timestamp < time();
        }

        $status = $isPast ? 'Selesai' : 'Akan Datang';
        $statusClass = $isPast ? 'completed' : 'upcoming';

        return array(
            'type' => $type,
            'venue' => isset($row['Nama_lapangan']) ? $row['Nama_lapangan'] : 'Lapangan Favorit',
            'field' => isset($row['Nama_lapangan']) ? $row['Nama_lapangan'] : 'Lapangan Favorit',
            'location' => isset($row['Lokasi']) ? $row['Lokasi'] : '',
            'date' => $this->formatDateIndo($row['Tanggal']),
            'time' => $time,
            'duration' => $duration,
            'code' => isset($row['ID_Booking']) ? $row['ID_Booking'] : '',
            'price' => 'Rp' . number_format((int) $row['Total_harga'], 0, ',', '.'),
            'total' => 'Rp' . number_format((int) $row['Total_harga'], 0, ',', '.'),
            'user' => isset($row['Nama_User']) ? $row['Nama_User'] : (isset($row['ID_User']) ? $row['ID_User'] : 'Pelanggan'),
            'status' => $status,
            'statusClass' => $statusClass,
            'button' => $isPast ? 'Lihat Detail' : 'Ubah Booking',
            'image' => $this->imageForType($type),
        );
    }

    protected function accountTable($connection)
    {
        if (!$connection) {
            return 'user';
        }

        $result = mysqli_query($connection, "SHOW TABLES LIKE 'users'");

        return $result && mysqli_num_rows($result) > 0 ? 'users' : 'user';
    }

    protected function formatDateIndo($date)
    {
        if (empty($date)) {
            return '';
        }

        $timestamp = strtotime($date);
        $months = array(
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        );

        $day = date('j', $timestamp);
        $month = $months[(int) date('n', $timestamp)];
        $year = date('Y', $timestamp);

        return $day . ' ' . $month . ' ' . $year;
    }

    protected function calculateDuration($start, $end)
    {
        if (empty($start) || empty($end)) {
            return '-';
        }

        $seconds = strtotime($end) - strtotime($start);

        if ($seconds <= 0) {
            return '-';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0 && $minutes > 0) {
            return $hours . ' Jam ' . $minutes . ' Menit';
        }

        if ($hours > 0) {
            return $hours . ' Jam';
        }

        return $minutes . ' Menit';
    }

    protected function imageForType($type)
    {
        $images = array(
            'Futsal' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=700&auto=format&fit=crop',
            'Badminton' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=700&auto=format&fit=crop',
            'Mini Soccer' => 'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?q=80&w=700&auto=format&fit=crop',
            'Basketball' => 'https://images.unsplash.com/photo-1546519638-68711109d298?q=80&w=700&auto=format&fit=crop',
        );

        return isset($images[$type]) ? $images[$type] : current($images);
    }
}
