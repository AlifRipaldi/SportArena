<?php
include '../config/connection.php';
session_start();

$id_jadwal = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_jadwal'])
    ? trim((string) $_POST['id_jadwal'])
    : null;

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$bookingToken = isset($_POST['booking_token']) ? (string) $_POST['booking_token'] : '';
if (!$id_jadwal || empty($_SESSION['booking_csrf']) || !hash_equals((string) $_SESSION['booking_csrf'], $bookingToken)) {
    header('Location: ../dashboard/lapangan');
    exit;
}

if ($id_jadwal) {
    $id_booking = 'BKG' . date('ymdHis') . random_int(10, 99);
    $id_user = $_SESSION['id_user'];
    $waktu = date("Y-m-d H:i:s");
    mysqli_begin_transaction($conn);

    try {
        $scheduleStatement = mysqli_prepare(
            $conn,
            "SELECT j.ID_Jadwal, j.Status, j.Tanggal,
                    COALESCE(NULLIF(j.Harga, 0), l.Harga) AS Harga,
                    l.Nama_lapangan, p.ID_User AS ID_Pemilik_User
             FROM jadwal j
             INNER JOIN lapangan l ON l.ID_Lapangan = j.ID_Lapangan
             INNER JOIN pemilik_lapangan p ON p.ID_Pemilik = l.ID_Pemilik
             WHERE j.ID_Jadwal = ? AND l.deleted_at IS NULL AND LOWER(l.Status) = 'aktif'
               AND NOT EXISTS (
                   SELECT 1 FROM booking b
                   WHERE b.ID_Jadwal = j.ID_Jadwal
                     AND LOWER(TRIM(b.Status)) NOT IN ('dibatalkan','cancelled','batal')
               )
             LIMIT 1 FOR UPDATE"
        );

        if (!$scheduleStatement) {
            throw new RuntimeException('Jadwal tidak dapat dibaca.');
        }

        mysqli_stmt_bind_param($scheduleStatement, 's', $id_jadwal);
        mysqli_stmt_execute($scheduleStatement);
        $scheduleResult = mysqli_stmt_get_result($scheduleStatement);
        $schedule = $scheduleResult ? mysqli_fetch_assoc($scheduleResult) : null;
        mysqli_stmt_close($scheduleStatement);

        $availableStatuses = array('available', 'tersedia', 'aktif');
        if (!$schedule || !in_array(strtolower(trim((string) $schedule['Status'])), $availableStatuses, true)) {
            throw new RuntimeException('Jadwal sudah tidak tersedia.');
        }

        if ($schedule['Tanggal'] < date('Y-m-d')) {
            throw new RuntimeException('Jadwal sudah lewat.');
        }

        $harga = max(0, (int) $schedule['Harga']);
        $stmt = mysqli_prepare($conn, "INSERT INTO booking (ID_Booking, ID_Jadwal, ID_User, Waktu_transaksi, Total_harga, Status) VALUES (?, ?, ?, ?, ?, 'Menunggu Pembayaran')");

        if (!$stmt) {
            throw new RuntimeException('Booking tidak dapat dibuat.');
        }

        mysqli_stmt_bind_param($stmt, 'ssssi', $id_booking, $id_jadwal, $id_user, $waktu, $harga);

        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Booking gagal disimpan.');
        }

        mysqli_stmt_close($stmt);
        $update = mysqli_prepare($conn, "UPDATE jadwal SET Status = 'Booked' WHERE ID_Jadwal = ?");

        if (!$update) {
            throw new RuntimeException('Status jadwal gagal diperbarui.');
        }

        mysqli_stmt_bind_param($update, 's', $id_jadwal);
        mysqli_stmt_execute($update);
        mysqli_stmt_close($update);

        $notification = mysqli_prepare($conn, "INSERT INTO notifikasi (ID_User, Judul, Pesan, Tipe, Link) VALUES (?, 'Booking berhasil dibuat', ?, 'booking', ?)");
        if ($notification) {
            $message = 'Booking ' . $schedule['Nama_lapangan'] . ' menunggu pembayaran.';
            $link = 'dashboard/booking';
            mysqli_stmt_bind_param($notification, 'sss', $id_user, $message, $link);
            mysqli_stmt_execute($notification);
            mysqli_stmt_close($notification);
        }

        mysqli_commit($conn);
        unset($_SESSION['booking_csrf']);
        echo "<script>alert('Booking tersimpan dan menunggu pembayaran.'); window.location='../dashboard/booking';</script>";
        exit;
    } catch (Throwable $exception) {
        mysqli_rollback($conn);
        $message = htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "<script>alert('{$message}'); window.location='../dashboard/lapangan';</script>";
        exit;
    }
}
?>
