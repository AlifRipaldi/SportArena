<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\ArenaData;

class DashboardController extends Controller
{
    protected function dashboardThemeMode()
    {
        return isset($_SESSION['theme_mode']) && $_SESSION['theme_mode'] === 'light' ? 'light' : 'dark';
    }

    public function dashboard()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        return $this->renderDashboard('dashboard/index', 'dashboard', 'Dashboard | Arena Sport', 'Dashboard', 'Ringkasan aktivitas dan pesanan Anda saat ini.');
    }

    public function search()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        return $this->renderDashboard('dashboard/search', 'lapangan', 'Cari Lapangan | Arena Sport', 'Cari Lapangan', 'Temukan lapangan terbaik di sekitar kamu.');
    }

    public function ulasan()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        return $this->view('dashboard/ulasan', array(
            'title' => 'Ulasan Saya | Arena Sport',
            'themeMode' => $this->dashboardThemeMode(),
            'activeMenu' => 'ulasan',
            'pageHeading' => 'Ulasan Saya',
            'pageSubheading' => 'Lihat dan kelola semua ulasan yang pernah kamu berikan',
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'reviewSummary' => $this->reviewSummary(),
            'reviews' => $this->reviews(),
            'reviewableBookings' => $this->customerReviewableBookings(),
        ), 'layouts/dashboard');
    }

    public function storeReview()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_user'])) { header('Location: ' . app_url('public/login.php')); exit; }
        $bookingId = isset($_POST['id_booking']) ? trim((string) $_POST['id_booking']) : '';
        $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
        $comment = isset($_POST['komentar']) ? trim((string) $_POST['komentar']) : '';

        if ($bookingId !== '' && $rating >= 1 && $rating <= 5 && $comment !== '') {
            $row = $this->dashboardData()->row(
                "SELECT b.ID_Booking,l.ID_Lapangan FROM booking b INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan LEFT JOIN review r ON r.ID_Booking=b.ID_Booking WHERE b.ID_Booking=? AND b.ID_User=? AND r.ID_Review IS NULL AND (LOWER(b.Status) IN ('selesai','completed') OR j.Tanggal<CURDATE()) LIMIT 1",
                'ss', array($bookingId, $this->dashboardUserId())
            );
            if ($row) {
                $reviewId = 'RVW' . date('ymdHis') . random_int(10, 99);
                $this->dashboardData()->execute("INSERT INTO review (ID_Review,ID_User,ID_Lapangan,ID_Booking,Rating,Komentar,Status) VALUES (?,?,?,?,?,?,'Tampil')", 'ssssis', array($reviewId, $this->dashboardUserId(), $row['ID_Lapangan'], $bookingId, $rating, $comment));
            }
        }

        header('Location: ' . app_url('dashboard/ulasan'));
        exit;
    }

    public function profil()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        $profile = $this->customerAccountSettings();
        $profileMetrics = $this->customerProfileMetrics();

        return $this->view('dashboard/profil', array(
            'title' => 'Profil Saya | Arena Sport',
            'themeMode' => $this->dashboardThemeMode(),
            'activeMenu' => 'profil',
            'pageHeading' => 'Profil Saya',
            'pageSubheading' => 'Kelola informasi profil dan aktivitas Anda.',
            'userName' => $profile['name'],
            'userEmail' => $profile['email'],
            'userPhone' => $profile['phone'],
            'userCity' => $profile['city'],
            'userAddress' => $profile['address'],
            'userAvatar' => $profile['avatar'],
            'userJoined' => $profile['joined'],
            'userVerified' => $profile['verified'],
            'profileMetrics' => $profileMetrics,
            'profileRecentBookings' => array_slice($this->customerBookingsFromDatabase(false), 0, 3),
            'favoriteSport' => $profile['favoriteSport'],
            'favoriteCity' => $profile['favoriteCity'],
            'searchRadius' => $profile['searchRadius'],
        ), 'layouts/dashboard');
    }

    protected function renderDashboard($view, $activeMenu, $title, $heading, $subheading)
    {
        return $this->view($view, array(
            'title' => $title,
            'themeMode' => $this->dashboardThemeMode(),
            'activeMenu' => $activeMenu,
            'pageHeading' => $heading,
            'pageSubheading' => $subheading,
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'stats' => $this->stats(),
            'venues' => $this->venues(),
            'favorites' => $this->favorites(),
            'nextBooking' => $this->nextBooking(),
            'bookings' => $this->bookings(),
            'paymentMethods' => $this->customerPaymentMethods(),
            'bookingCsrfToken' => $this->bookingCsrfToken(),
        ), 'layouts/dashboard');
    }

    protected function bookings()
    {
        return $this->customerBookingsFromDatabase(false);

        return array(
            array(
                'type' => 'Futsal',
                'venue' => 'Arena Futsal Parepare',
                'location' => 'Jl. Mattirotasi No. 12, Parepare',
                'date' => '22 Juni 2026',
                'dateValue' => '2026-06-22',
                'time' => '10:00 - 11:00',
                'duration' => '1 Jam',
                'code' => 'AS-220626-00123',
                'price' => 'Rp82.000',
                'status' => 'Mendatang',
                'statusClass' => 'upcoming',
                'category' => 'upcoming',
                'action' => 'edit',
                'button' => 'Ubah Booking',
                'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=500&auto=format&fit=crop',
            ),
            array(
                'type' => 'Badminton',
                'venue' => 'Lapangan Badminton Center',
                'location' => 'Jl. Bau Massepe No. 45, Parepare',
                'date' => '24 Juni 2026',
                'dateValue' => '2026-06-24',
                'time' => '08:00 - 09:00',
                'duration' => '1 Jam',
                'code' => 'AS-240626-00098',
                'price' => 'Rp60.000',
                'status' => 'Mendatang',
                'statusClass' => 'upcoming',
                'category' => 'upcoming',
                'action' => 'edit',
                'button' => 'Ubah Booking',
                'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=500&auto=format&fit=crop',
            ),
            array(
                'type' => 'Mini Soccer',
                'venue' => 'Mini Soccer Victory',
                'location' => 'Jl. Jend. Sudirman, Parepare',
                'date' => '25 Juni 2026',
                'dateValue' => '2026-06-25',
                'time' => '17:00 - 18:00',
                'duration' => '1 Jam',
                'code' => 'AS-250626-00045',
                'price' => 'Rp100.000',
                'status' => 'Menunggu Pembayaran',
                'statusClass' => 'pending',
                'category' => 'upcoming',
                'action' => 'pay',
                'button' => 'Bayar Sekarang',
                'image' => 'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?q=80&w=500&auto=format&fit=crop',
            ),
            array(
                'type' => 'Basketball',
                'venue' => 'Arena Basketball Court',
                'location' => 'Jl. Andi Makkasau No. 7, Parepare',
                'date' => '18 Juni 2026',
                'dateValue' => '2026-06-18',
                'time' => '14:00 - 15:00',
                'duration' => '1 Jam',
                'code' => 'AS-180626-00032',
                'price' => 'Rp70.000',
                'status' => 'Selesai',
                'statusClass' => 'completed',
                'category' => 'completed',
                'action' => 'review',
                'button' => 'Beri Ulasan',
                'image' => 'https://images.unsplash.com/photo-1546519638-68711109d298?q=80&w=500&auto=format&fit=crop',
            ),
            array(
                'type' => 'Futsal',
                'venue' => 'Arena Futsal Soreang',
                'location' => 'Jl. Reformasi No. 8, Parepare',
                'date' => '17 Juni 2026',
                'dateValue' => '2026-06-17',
                'time' => '19:00 - 20:00',
                'duration' => '1 Jam',
                'code' => 'AS-170626-00017',
                'price' => 'Rp85.000',
                'status' => 'Dibatalkan',
                'statusClass' => 'cancelled',
                'category' => 'cancelled',
                'action' => 'rebook',
                'button' => 'Booking Lagi',
                'image' => 'https://images.unsplash.com/photo-1553778263-73a83bab9b0c?q=80&w=500&auto=format&fit=crop',
            ),
        );
    }

    public function booking()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        return $this->renderDashboard('dashboard/booking', 'booking', 'Booking Saya | Arena Sport', 'Booking Saya', 'Kelola semua booking lapangan kamu');
    }

    public function updateBooking()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_user'])) { header('Location: ' . app_url('public/login.php')); exit; }

        $bookingId = isset($_POST['id_booking']) ? trim((string) $_POST['id_booking']) : '';
        $action = isset($_POST['booking_action']) ? trim((string) $_POST['booking_action']) : 'reschedule';
        $connection = Database::connection();
        mysqli_begin_transaction($connection);

        try {
            $statement = mysqli_prepare($connection, "SELECT b.ID_Jadwal, b.Status, j.ID_Lapangan FROM booking b INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal WHERE b.ID_Booking=? AND b.ID_User=? LIMIT 1 FOR UPDATE");
            if (!$statement) { throw new \RuntimeException('Booking tidak dapat dibaca.'); }
            $userId = $this->dashboardUserId();
            mysqli_stmt_bind_param($statement, 'ss', $bookingId, $userId);
            mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement);
            $booking = $result ? mysqli_fetch_assoc($result) : null;
            mysqli_stmt_close($statement);
            if (!$booking) { throw new \RuntimeException('Booking tidak ditemukan.'); }

            if ($action === 'cancel') {
                $update = mysqli_prepare($connection, "UPDATE booking SET Status='Dibatalkan', Dibatalkan_pada=NOW() WHERE ID_Booking=?");
                mysqli_stmt_bind_param($update, 's', $bookingId);
                mysqli_stmt_execute($update);
                mysqli_stmt_close($update);
                $schedule = mysqli_prepare($connection, "UPDATE jadwal SET Status='Available' WHERE ID_Jadwal=?");
                mysqli_stmt_bind_param($schedule, 's', $booking['ID_Jadwal']);
                mysqli_stmt_execute($schedule);
                mysqli_stmt_close($schedule);
                $this->dashboardCancelPendingPayments($connection, $bookingId);
            } else {
                $date = isset($_POST['booking_date']) ? trim((string) $_POST['booking_date']) : '';
                $time = isset($_POST['booking_time']) ? trim((string) $_POST['booking_time']) : '';
                $parts = preg_split('/\s*-\s*/', $time);
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || count($parts) !== 2) { throw new \RuntimeException('Jadwal baru tidak valid.'); }
                $start = $parts[0] . ':00'; $end = $parts[1] . ':00';
                $target = mysqli_prepare($connection, "SELECT ID_Jadwal FROM jadwal WHERE ID_Lapangan=? AND Tanggal=? AND Jam_Mulai=? AND Jam_Selesai=? AND LOWER(Status) IN ('available','tersedia','aktif') LIMIT 1 FOR UPDATE");
                mysqli_stmt_bind_param($target, 'ssss', $booking['ID_Lapangan'], $date, $start, $end);
                mysqli_stmt_execute($target);
                $targetResult = mysqli_stmt_get_result($target);
                $newSchedule = $targetResult ? mysqli_fetch_assoc($targetResult) : null;
                mysqli_stmt_close($target);
                if (!$newSchedule) { throw new \RuntimeException('Slot jadwal baru tidak tersedia.'); }
                $oldScheduleId = $booking['ID_Jadwal']; $newScheduleId = $newSchedule['ID_Jadwal'];
                $release = mysqli_prepare($connection, "UPDATE jadwal SET Status='Available' WHERE ID_Jadwal=?");
                mysqli_stmt_bind_param($release, 's', $oldScheduleId); mysqli_stmt_execute($release); mysqli_stmt_close($release);
                $reserve = mysqli_prepare($connection, "UPDATE jadwal SET Status='Booked' WHERE ID_Jadwal=?");
                mysqli_stmt_bind_param($reserve, 's', $newScheduleId); mysqli_stmt_execute($reserve); mysqli_stmt_close($reserve);
                $update = mysqli_prepare($connection, 'UPDATE booking SET ID_Jadwal=? WHERE ID_Booking=?');
                mysqli_stmt_bind_param($update, 'ss', $newScheduleId, $bookingId); mysqli_stmt_execute($update); mysqli_stmt_close($update);
            }

            mysqli_commit($connection);
        } catch (\Throwable $exception) {
            mysqli_rollback($connection);
            $_SESSION['booking_error'] = $exception->getMessage();
        }

        header('Location: ' . app_url('dashboard/booking'));
        exit;
    }

    public function payBooking()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_user'])) { header('Location: ' . app_url('public/login.php')); exit; }
        $bookingId = isset($_POST['id_booking']) ? trim((string) $_POST['id_booking']) : '';
        $methodInput = isset($_POST['payment_method']) ? trim((string) $_POST['payment_method']) : '';
        $connection = Database::connection();
        mysqli_begin_transaction($connection);

        try {
            $methodStatement = mysqli_prepare($connection, 'SELECT Nama FROM metode_pembayaran WHERE Aktif=1 AND (ID_Metode=? OR Nama=?) LIMIT 1');
            mysqli_stmt_bind_param($methodStatement, 'ss', $methodInput, $methodInput); mysqli_stmt_execute($methodStatement);
            $methodResult = mysqli_stmt_get_result($methodStatement); $method = $methodResult ? mysqli_fetch_assoc($methodResult) : null; mysqli_stmt_close($methodStatement);
            if (!$method) { throw new \RuntimeException('Metode pembayaran tidak tersedia.'); }

            $statement = mysqli_prepare($connection, 'SELECT Total_harga, Status FROM booking WHERE ID_Booking=? AND ID_User=? LIMIT 1 FOR UPDATE');
            $userId = $this->dashboardUserId(); mysqli_stmt_bind_param($statement, 'ss', $bookingId, $userId); mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement); $booking = $result ? mysqli_fetch_assoc($result) : null; mysqli_stmt_close($statement);
            if (!$booking || stripos((string) $booking['Status'], 'batal') !== false) { throw new \RuntimeException('Booking tidak dapat dibayar.'); }

            $existing = mysqli_prepare($connection, "SELECT ID_Pembayaran FROM pembayaran WHERE ID_Booking=? AND LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid') LIMIT 1");
            mysqli_stmt_bind_param($existing, 's', $bookingId); mysqli_stmt_execute($existing); $existingResult=mysqli_stmt_get_result($existing); $paid=$existingResult?mysqli_fetch_assoc($existingResult):null; mysqli_stmt_close($existing);
            if ($paid) { throw new \RuntimeException('Booking sudah dibayar.'); }

            $paymentId='PAY'.date('ymdHis').random_int(10,99); $reference='REF'.strtoupper(bin2hex(random_bytes(5))); $amount=(int)$booking['Total_harga']; $methodName=$method['Nama'];
            $insert=mysqli_prepare($connection,"INSERT INTO pembayaran (ID_Pembayaran,ID_Booking,Jumlah,Keterangan,Metode,Status,Referensi,Waktu_pembayaran) VALUES (?,?,?,'Pembayaran booking',?,'Berhasil',?,NOW())");
            mysqli_stmt_bind_param($insert,'ssiss',$paymentId,$bookingId,$amount,$methodName,$reference); if(!mysqli_stmt_execute($insert)){throw new \RuntimeException('Pembayaran gagal disimpan.');} mysqli_stmt_close($insert);
            $update=mysqli_prepare($connection,"UPDATE booking SET Status='Aktif' WHERE ID_Booking=?"); mysqli_stmt_bind_param($update,'s',$bookingId); mysqli_stmt_execute($update); mysqli_stmt_close($update);
            mysqli_commit($connection);
        } catch (\Throwable $exception) {
            mysqli_rollback($connection); $_SESSION['booking_error']=$exception->getMessage();
        }

        header('Location: ' . app_url('dashboard/booking'));
        exit;
    }

    protected function dashboardCancelPendingPayments($connection, $bookingId)
    {
        $statement = mysqli_prepare($connection, "UPDATE pembayaran SET Status='Dibatalkan' WHERE ID_Booking=? AND LOWER(Status) IN ('pending','menunggu')");
        if ($statement) { mysqli_stmt_bind_param($statement, 's', $bookingId); mysqli_stmt_execute($statement); mysqli_stmt_close($statement); }
    }

    public function riwayat()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        return $this->view('dashboard/riwayat', array(
            'title' => 'Riwayat Booking | Arena Sport',
            'themeMode' => $this->dashboardThemeMode(),
            'activeMenu' => 'riwayat',
            'pageHeading' => 'Riwayat',
            'pageSubheading' => 'Lihat semua riwayat booking lapangan kamu',
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'bookings' => $this->historyBookings(),
        ), 'layouts/dashboard');
    }

    protected function historyBookings()
    {
        return $this->customerBookingsFromDatabase(true);

        return array(
            array(
                'type' => 'Futsal',
                'venue' => 'Arena Futsal Parepare',
                'location' => 'Jl. Mattirotasi No. 12, Parepare',
                'date' => '22 Mei 2024',
                'time' => '10:00 - 11:00',
                'duration' => '1 Jam',
                'code' => 'AS-220524-00123',
                'price' => 'Rp82.000',
                'status' => 'Selesai',
                'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=700&auto=format&fit=crop',
            ),
            array(
                'type' => 'Badminton',
                'venue' => 'Lapangan Badminton Center',
                'location' => 'Jl. Bau Massepe No. 45, Parepare',
                'date' => '18 Mei 2024',
                'time' => '08:00 - 09:00',
                'duration' => '1 Jam',
                'code' => 'AS-180524-00098',
                'price' => 'Rp60.000',
                'status' => 'Selesai',
                'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=700&auto=format&fit=crop',
            ),
            array(
                'type' => 'Mini Soccer',
                'venue' => 'Mini Soccer Victory',
                'location' => 'Jl. Jend. Sudirman, Parepare',
                'date' => '10 Mei 2024',
                'time' => '17:00 - 18:00',
                'duration' => '1 Jam',
                'code' => 'AS-100524-00056',
                'price' => 'Rp100.000',
                'status' => 'Selesai',
                'image' => 'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?q=80&w=700&auto=format&fit=crop',
            ),
            array(
                'type' => 'Basketball',
                'venue' => 'Arena Basketball Court',
                'location' => 'Jl. Andi Makkasau No. 7, Parepare',
                'date' => '5 Mei 2024',
                'time' => '14:00 - 15:00',
                'duration' => '1 Jam',
                'code' => 'AS-050524-00032',
                'price' => 'Rp70.000',
                'status' => 'Dibatalkan',
                'image' => 'https://images.unsplash.com/photo-1546519638-68711109d298?q=80&w=700&auto=format&fit=crop',
            ),
        );
    }

    public function favorit()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        return $this->renderDashboard('dashboard/favorit', 'favorit', 'Favorit | Arena Sport', 'Favorit', 'Lapangan favorit yang ingin kamu mainkan');
    }

    public function toggleFavorite()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_user'])) { header('Location: ' . app_url('public/login.php')); exit; }

        $fieldId = isset($_POST['id_lapangan']) ? trim((string) $_POST['id_lapangan']) : '';
        if ($fieldId !== '') {
            $data = $this->dashboardData();
            $exists = (int) $data->value('SELECT COUNT(*) AS value FROM favorit WHERE ID_User = ? AND ID_Lapangan = ?', 'ss', array($this->dashboardUserId(), $fieldId));

            if ($exists > 0) {
                $data->execute('DELETE FROM favorit WHERE ID_User = ? AND ID_Lapangan = ?', 'ss', array($this->dashboardUserId(), $fieldId));
            } else {
                $data->execute('INSERT IGNORE INTO favorit (ID_User, ID_Lapangan) SELECT ?, ID_Lapangan FROM lapangan WHERE ID_Lapangan = ? AND deleted_at IS NULL', 'ss', array($this->dashboardUserId(), $fieldId));
            }
        }

        header('Location: ' . app_url(isset($_POST['return_to']) && $_POST['return_to'] === 'favorit' ? 'dashboard/favorit' : 'dashboard/lapangan'));
        exit;
    }

    public function clearFavorites()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_user'])) { header('Location: ' . app_url('public/login.php')); exit; }
        $this->dashboardData()->execute('DELETE FROM favorit WHERE ID_User = ?', 's', array($this->dashboardUserId()));
        header('Location: ' . app_url('dashboard/favorit'));
        exit;
    }

    protected function stats()
    {
        $bookings = $this->customerBookingsFromDatabase(false);
        $active = 0;
        $completed = 0;

        foreach ($bookings as $booking) {
            if (isset($booking['category']) && in_array($booking['category'], array('upcoming', 'pending'), true)) {
                $active++;
            }

            if (isset($booking['category']) && $booking['category'] === 'completed') {
                $completed++;
            }
        }

        $data = $this->dashboardData();
        $userId = $this->dashboardUserId();
        $favoriteCount = $data->value('SELECT COUNT(*) AS value FROM favorit WHERE ID_User = ?', 's', array($userId));
        $rating = $data->value('SELECT COALESCE(AVG(Rating), 0) AS value FROM review WHERE ID_User = ?', 's', array($userId));

        return array(
            array('label' => 'Booking Aktif', 'value' => (string) $active, 'icon' => '&#128197;', 'accent' => 'green'),
            array('label' => 'Selesai', 'value' => (string) $completed, 'icon' => '&#10003;', 'accent' => 'blue'),
            array('label' => 'Favorit', 'value' => (string) ((int) $favoriteCount), 'icon' => '&#9825;', 'accent' => 'purple'),
            array('label' => 'Rating Anda', 'value' => number_format((float) $rating, 1), 'icon' => '&#9734;', 'accent' => 'orange'),
        );
    }

    protected function venues()
    {
        $databaseVenues = $this->databaseVenues();

        return $databaseVenues;

        return array(
            array(
                'name' => 'Arena Futsal Parepare',
                'type' => 'Futsal',
                'location' => 'Jl. Mattirotasi No. 12, Parepare',
                'features' => array('Futsal', 'Parkir', 'Musholla', 'Toilet', 'Ruang Ganti'),
                'distance' => '1.2 km',
                'availableDays' => array(1, 2, 3, 4, 5, 6),
                'availableTimes' => array('10:00 - 11:00', '18:00 - 19:00'),
                'rating' => '4.8',
                'reviews' => '120 ulasan',
                'price' => 'Rp80.000',
                'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=900&auto=format&fit=crop',
            ),
            array(
                'name' => 'Lapangan Badminton Center',
                'type' => 'Badminton',
                'location' => 'Jl. Bau Massepe No. 45, Parepare',
                'features' => array('Badminton', 'Parkir', 'Musholla', 'Kantin'),
                'distance' => '2.4 km',
                'availableDays' => array(0, 1, 2, 3, 4, 5, 6),
                'availableTimes' => array('08:00 - 09:00', '10:00 - 11:00'),
                'rating' => '4.6',
                'reviews' => '85 ulasan',
                'price' => 'Rp60.000',
                'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=900&auto=format&fit=crop',
            ),
            array(
                'name' => 'Mini Soccer Victory',
                'type' => 'Mini Soccer',
                'location' => 'Jl. Jend. Sudirman, Parepare',
                'features' => array('Mini Soccer', 'Parkir', 'Toilet', 'Kantin'),
                'distance' => '2.8 km',
                'availableDays' => array(0, 2, 4, 6),
                'availableTimes' => array('10:00 - 11:00', '18:00 - 19:00'),
                'rating' => '4.7',
                'reviews' => '98 ulasan',
                'price' => 'Rp100.000',
                'image' => 'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?q=80&w=900&auto=format&fit=crop',
            ),
            array(
                'name' => 'Arena Basketball Court',
                'type' => 'Basketball',
                'location' => 'Jl. Andi Makkasau No. 7, Parepare',
                'features' => array('Basketball', 'Parkir', 'Musholla', 'Toilet'),
                'distance' => '3.1 km',
                'availableDays' => array(0, 1, 3, 5),
                'availableTimes' => array('08:00 - 09:00', '18:00 - 19:00'),
                'rating' => '4.5',
                'reviews' => '60 ulasan',
                'price' => 'Rp70.000',
                'image' => 'https://images.unsplash.com/photo-1546519638-68711109d298?q=80&w=900&auto=format&fit=crop',
            ),
        );
    }

    protected function databaseVenues()
    {
        try {
            $connection = Database::connection();
        } catch (\Throwable $exception) {
            return array();
        }

        $result = mysqli_query(
            $connection,
            "SELECT l.ID_Lapangan, l.Nama_lapangan, l.Lokasi, l.Jenis_olahraga, l.Fasilitas, l.Harga, l.Foto,
                    COALESCE(AVG(r.Rating), 0) AS Rating,
                    COUNT(r.ID_Review) AS Review_count
             FROM lapangan l
             LEFT JOIN review r ON r.ID_Lapangan = l.ID_Lapangan AND LOWER(r.Status) = 'tampil'
             WHERE LOWER(TRIM(l.Status)) = 'aktif' AND l.deleted_at IS NULL
             GROUP BY l.ID_Lapangan, l.Nama_lapangan, l.Lokasi, l.Jenis_olahraga, l.Fasilitas, l.Harga, l.Foto
             ORDER BY Nama_lapangan ASC"
        );

        if (!$result) {
            return array();
        }

        $venues = array();
        $scheduleStatement = mysqli_prepare(
            $connection,
            "SELECT ID_Jadwal, Tanggal, Jam_mulai, Jam_selesai, Harga
             FROM jadwal
             WHERE ID_Lapangan = ? AND Tanggal >= CURDATE()
               AND LOWER(TRIM(Status)) IN ('available', 'tersedia', 'aktif')
             ORDER BY Tanggal ASC, Jam_mulai ASC"
        );
        $index = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $fieldId = isset($row['ID_Lapangan']) ? $row['ID_Lapangan'] : '';
            $type = isset($row['Jenis_olahraga']) ? trim((string) $row['Jenis_olahraga']) : '';
            $facilities = $this->decodeDashboardList(isset($row['Fasilitas']) ? $row['Fasilitas'] : '');
            $availableDates = array();
            $availableTimes = array();
            $availableSlots = array();
            $availableScheduleIds = array();
            $availableSchedules = array();

            if ($scheduleStatement && $fieldId !== '') {
                mysqli_stmt_bind_param($scheduleStatement, 's', $fieldId);
                mysqli_stmt_execute($scheduleStatement);
                $scheduleResult = mysqli_stmt_get_result($scheduleStatement);

                while ($scheduleResult && $schedule = mysqli_fetch_assoc($scheduleResult)) {
                    $date = isset($schedule['Tanggal']) ? trim((string) $schedule['Tanggal']) : '';
                    $start = isset($schedule['Jam_mulai']) ? substr((string) $schedule['Jam_mulai'], 0, 5) : '';
                    $end = isset($schedule['Jam_selesai']) ? substr((string) $schedule['Jam_selesai'], 0, 5) : '';
                    $time = $start !== '' && $end !== '' ? $start . ' - ' . $end : '';
                    $slot = $date !== '' && $time !== '' ? $date . '@' . $time : '';

                    if ($date !== '' && !in_array($date, $availableDates, true)) {
                        $availableDates[] = $date;
                    }

                    if ($time !== '' && !in_array($time, $availableTimes, true)) {
                        $availableTimes[] = $time;
                    }

                    if ($slot !== '' && !in_array($slot, $availableSlots, true)) {
                        $availableSlots[] = $slot;
                    }

                    if (!empty($schedule['ID_Jadwal'])) {
                        $availableScheduleIds[] = $schedule['ID_Jadwal'];
                        $slotPrice = !empty($schedule['Harga']) ? (int) $schedule['Harga'] : (isset($row['Harga']) ? (int) $row['Harga'] : 0);
                        $availableSchedules[] = array(
                            'id' => $schedule['ID_Jadwal'],
                            'date' => $date,
                            'dateLabel' => $this->dashboardFormatDate($date),
                            'time' => $time,
                            'price' => $this->dashboardRupiah($slotPrice),
                        );
                    }
                }

                if ($scheduleResult) {
                    mysqli_free_result($scheduleResult);
                }
            }

            $features = $facilities;
            if ($type !== '' && !in_array($type, $features, true)) {
                array_unshift($features, $type);
            }

            $price = isset($row['Harga']) ? max(0, (int) $row['Harga']) : 0;
            $venues[] = array(
                'id' => $fieldId,
                'name' => isset($row['Nama_lapangan']) ? $row['Nama_lapangan'] : 'Lapangan Olahraga',
                'type' => $type,
                'location' => isset($row['Lokasi']) ? $row['Lokasi'] : '',
                'features' => $features,
                'distance' => number_format(1 + ($index * 0.8), 1) . ' km',
                'availableDays' => array(),
                'availableDates' => $availableDates,
                'availableTimes' => $availableTimes,
                'availableSlots' => $availableSlots,
                'availableSchedules' => $availableSchedules,
                'bookingUrl' => !empty($availableScheduleIds) ? app_url('booking/' . rawurlencode($availableScheduleIds[0])) : '',
                'rating' => number_format(isset($row['Rating']) ? (float) $row['Rating'] : 0, 1),
                'reviews' => (isset($row['Review_count']) ? (int) $row['Review_count'] : 0) . ' ulasan',
                'price' => 'Rp' . number_format($price, 0, ',', '.'),
                'image' => $this->dashboardVenueImage(isset($row['Foto']) ? $row['Foto'] : '', $type),
            );
            $index++;
        }

        if ($scheduleStatement) {
            mysqli_stmt_close($scheduleStatement);
        }

        return $venues;
    }

    protected function decodeDashboardList($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return array();
        }

        $decoded = json_decode($value, true);
        $items = is_array($decoded) ? $decoded : explode(',', $value);
        $clean = array();

        foreach ($items as $item) {
            $item = trim((string) $item);

            if ($item !== '' && !in_array($item, $clean, true)) {
                $clean[] = $item;
            }
        }

        return $clean;
    }

    protected function dashboardVenueImage($value, $type)
    {
        $photos = $this->decodeDashboardList($value);

        foreach ($photos as $photo) {
            $photo = str_replace('\\', '/', $photo);

            if (strpos($photo, '..') === false && strpos($photo, 'storage/uploads/lapangan/') === 0) {
                return app_url($photo);
            }
        }

        $fallbacks = array(
            'badminton' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=900&auto=format&fit=crop',
            'mini soccer' => 'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?q=80&w=900&auto=format&fit=crop',
            'basketball' => 'https://images.unsplash.com/photo-1546519638-68711109d298?q=80&w=900&auto=format&fit=crop',
        );
        $typeKey = strtolower(trim((string) $type));

        return isset($fallbacks[$typeKey])
            ? $fallbacks[$typeKey]
            : 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=900&auto=format&fit=crop';
    }

    protected function favorites()
    {
        return $this->customerFavoritesFromDatabase();

        return array(
            array(
                'type' => 'Futsal',
                'venue' => 'Arena Futsal Parepare',
                'location' => 'Jl. Mattirotasi No. 12, Parepare',
                'features' => array('Futsal', 'Parkir', 'Musholla', 'Toilet', '+2'),
                'rating' => '4.8',
                'reviews' => '120 ulasan',
                'distance' => '1.2 km',
                'price' => 'Rp80.000',
                'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=900&auto=format&fit=crop',
            ),
            array(
                'type' => 'Badminton',
                'venue' => 'Lapangan Badminton Center',
                'location' => 'Jl. Bau Massepe No. 45, Parepare',
                'features' => array('Badminton', 'Parkir', 'Musholla', 'Kantin'),
                'rating' => '4.6',
                'reviews' => '85 ulasan',
                'distance' => '2.4 km',
                'price' => 'Rp60.000',
                'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=900&auto=format&fit=crop',
            ),
            array(
                'type' => 'Mini Soccer',
                'venue' => 'Mini Soccer Victory',
                'location' => 'Jl. Jend. Sudirman, Parepare',
                'features' => array('Mini Soccer', 'Parkir', 'Toilet', 'Kantin', '+1'),
                'rating' => '4.7',
                'reviews' => '98 ulasan',
                'distance' => '2.8 km',
                'price' => 'Rp100.000',
                'image' => 'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?q=80&w=900&auto=format&fit=crop',
            ),
            array(
                'type' => 'Basketball',
                'venue' => 'Arena Basketball Court',
                'location' => 'Jl. Andi Makkasau No. 7, Parepare',
                'features' => array('Basketball', 'Parkir', 'Musholla', 'Toilet'),
                'rating' => '4.5',
                'reviews' => '60 ulasan',
                'distance' => '3.1 km',
                'price' => 'Rp70.000',
                'image' => 'https://images.unsplash.com/photo-1521093721353-fcc2b798fbd5?q=80&w=900&auto=format&fit=crop',
            ),
        );
    }

    protected function nextBooking()
    {
        foreach ($this->customerBookingsFromDatabase(false) as $booking) {
            if (isset($booking['category']) && in_array($booking['category'], array('upcoming', 'pending'), true)) {
                return array(
                    'venue' => $booking['venue'],
                    'date' => $booking['date'],
                    'time' => $booking['time'],
                    'duration' => $booking['duration'],
                    'status' => $booking['status'],
                    'image' => $booking['image'],
                );
            }
        }

        return array();

        return array(
            'venue' => 'Arena Futsal Parepare',
            'date' => '10 Juni 2026',
            'time' => '10:00 - 11:00',
            'duration' => '1 Jam',
            'status' => 'Akan Datang',
            'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=500&auto=format&fit=crop',
        );
    }

    protected function reviewSummary()
    {
        $reviews = $this->customerReviewsFromDatabase();
        $total = count($reviews);
        $sum = 0;
        $positive = 0;

        foreach ($reviews as $review) {
            $rating = isset($review['rating']) ? (float) $review['rating'] : 0;
            $sum += $rating;
            $positive += $rating >= 4 ? 1 : 0;
        }

        $negative = $total - $positive;

        return array(
            'average' => $total > 0 ? number_format($sum / $total, 1) : '0.0',
            'total' => (string) $total,
            'positive' => (string) $positive,
            'negative' => (string) $negative,
            'positivePercent' => $total > 0 ? number_format(($positive / $total) * 100, 1) . '%' : '0%',
            'negativePercent' => $total > 0 ? number_format(($negative / $total) * 100, 1) . '%' : '0%',
        );
    }

    protected function reviews()
    {
        return $this->customerReviewsFromDatabase();

        return array(
            array(
                'venue' => 'Arena Futsal Parepare',
                'type' => 'Futsal',
                'location' => 'Jl. Mattirotasi No. 12, Parepare',
                'rating' => 5.0,
                'reviews' => 120,
                'comment' => 'Lapangan bersih, pencahayaan bagus, dan pelayanan ramah. Recommended!',
                'date' => '22 Mei 2024',
                'code' => 'AS-220524-00123',
                'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=900&auto=format&fit=crop',
            ),
            array(
                'venue' => 'Lapangan Badminton Center',
                'type' => 'Badminton',
                'location' => 'Jl. Bau Massepe No. 45, Parepare',
                'rating' => 4.0,
                'reviews' => 85,
                'comment' => 'Lapangan cukup baik, tapi shuttlecock perlu diganti lebih sering.',
                'date' => '18 Mei 2024',
                'code' => 'AS-180524-00098',
                'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=900&auto=format&fit=crop',
            ),
            array(
                'venue' => 'Mini Soccer Victory',
                'type' => 'Mini Soccer',
                'location' => 'Jl. Jend. Sudirman, Parepare',
                'rating' => 5.0,
                'reviews' => 98,
                'comment' => 'Rumput sintetis berkualitas, cocok untuk pertandingan malam hari!',
                'date' => '10 Mei 2024',
                'code' => 'AS-100524-00056',
                'image' => 'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?q=80&w=900&auto=format&fit=crop',
            ),
            array(
                'venue' => 'Arena Basketball Court',
                'type' => 'Basketball',
                'location' => 'Jl. Andi Makkasau No. 7, Parepare',
                'rating' => 3.0,
                'reviews' => 76,
                'comment' => 'Fasilitas oke, hanya saja area parkir kurang luas.',
                'date' => '05 Mei 2024',
                'code' => 'AS-050524-00032',
                'image' => 'https://images.unsplash.com/photo-1546519638-68711109d298?q=80&w=900&auto=format&fit=crop',
            ),
        );
    }

    protected function dashboardData()
    {
        return new ArenaData();
    }

    protected function dashboardUserId()
    {
        return isset($_SESSION['id_user']) ? trim((string) $_SESSION['id_user']) : '';
    }

    protected function customerBookingsFromDatabase($historyOnly)
    {
        $userId = $this->dashboardUserId();

        if ($userId === '') {
            return array();
        }

        $rows = $this->dashboardData()->rows(
            "SELECT b.ID_Booking, b.Waktu_transaksi, b.Total_harga, b.Status AS booking_status,
                    j.Tanggal, j.Jam_Mulai, j.Jam_Selesai,
                    l.ID_Lapangan, l.Nama_lapangan, l.Lokasi, l.Jenis_olahraga, l.Foto,
                    p.Status AS payment_status,
                    (SELECT COUNT(*) FROM review r WHERE r.ID_Booking = b.ID_Booking) AS has_review
             FROM booking b
             INNER JOIN jadwal j ON j.ID_Jadwal = b.ID_Jadwal
             INNER JOIN lapangan l ON l.ID_Lapangan = j.ID_Lapangan
             LEFT JOIN pembayaran p ON p.ID_Pembayaran = (
                 SELECT p2.ID_Pembayaran
                 FROM pembayaran p2
                 WHERE p2.ID_Booking = b.ID_Booking
                 ORDER BY p2.created_at DESC, p2.ID_Pembayaran DESC
                 LIMIT 1
             )
             WHERE b.ID_User = ?
             ORDER BY j.Tanggal DESC, j.Jam_Mulai DESC",
            's',
            array($userId)
        );

        $bookings = array();

        foreach ($rows as $row) {
            $status = $this->customerBookingStatus($row);
            $dateValue = isset($row['Tanggal']) ? $row['Tanggal'] : '';

            if ($dateValue < date('Y-m-d') && $status['category'] === 'upcoming') {
                $status = $this->customerBookingStatus(array('booking_status' => 'Selesai', 'payment_status' => $row['payment_status']));
            }

            if ($historyOnly && !in_array($status['category'], array('completed', 'cancelled'), true) && $dateValue >= date('Y-m-d')) {
                continue;
            }

            $start = isset($row['Jam_Mulai']) ? substr((string) $row['Jam_Mulai'], 0, 5) : '';
            $end = isset($row['Jam_Selesai']) ? substr((string) $row['Jam_Selesai'], 0, 5) : '';
            $durationMinutes = 0;

            if ($start !== '' && $end !== '') {
                $durationMinutes = max(0, (int) round((strtotime($end) - strtotime($start)) / 60));
            }

            $booking = array(
                'type' => isset($row['Jenis_olahraga']) ? $row['Jenis_olahraga'] : '',
                'venue' => isset($row['Nama_lapangan']) ? $row['Nama_lapangan'] : 'Lapangan Olahraga',
                'location' => isset($row['Lokasi']) ? $row['Lokasi'] : '',
                'date' => $this->dashboardFormatDate($dateValue),
                'dateValue' => $dateValue,
                'time' => $start . ' - ' . $end,
                'duration' => $durationMinutes >= 60 && $durationMinutes % 60 === 0
                    ? ($durationMinutes / 60) . ' Jam'
                    : $durationMinutes . ' Menit',
                'code' => isset($row['ID_Booking']) ? $row['ID_Booking'] : '',
                'price' => $this->dashboardRupiah(isset($row['Total_harga']) ? $row['Total_harga'] : 0),
                'status' => $status['status'],
                'statusClass' => $status['class'],
                'category' => $status['category'],
                'action' => $status['action'],
                'button' => $status['button'],
                'image' => $this->dashboardVenueImage(isset($row['Foto']) ? $row['Foto'] : '', isset($row['Jenis_olahraga']) ? $row['Jenis_olahraga'] : ''),
            );

            $bookings[] = $booking;
        }

        return $bookings;
    }

    protected function customerBookingStatus(array $row)
    {
        $raw = strtolower(trim((string) (isset($row['booking_status']) ? $row['booking_status'] : '')));
        $payment = strtolower(trim((string) (isset($row['payment_status']) ? $row['payment_status'] : '')));

        if (strpos($raw, 'batal') !== false || strpos($payment, 'refund') !== false) {
            return array('status' => 'Dibatalkan', 'class' => 'cancelled', 'category' => 'cancelled', 'action' => 'rebook', 'button' => 'Booking Lagi');
        }

        if (strpos($raw, 'selesai') !== false || strpos($raw, 'complete') !== false) {
            return array('status' => 'Selesai', 'class' => 'completed', 'category' => 'completed', 'action' => 'review', 'button' => 'Beri Ulasan');
        }

        if (strpos($raw, 'menunggu') !== false || strpos($raw, 'pending') !== false || strpos($payment, 'pending') !== false) {
            return array('status' => 'Menunggu Pembayaran', 'class' => 'pending', 'category' => 'pending', 'action' => 'pay', 'button' => 'Bayar Sekarang');
        }

        return array('status' => 'Mendatang', 'class' => 'upcoming', 'category' => 'upcoming', 'action' => 'edit', 'button' => 'Ubah Booking');
    }

    protected function customerFavoritesFromDatabase()
    {
        $userId = $this->dashboardUserId();

        if ($userId === '') {
            return array();
        }

        $rows = $this->dashboardData()->rows(
            "SELECT l.ID_Lapangan, l.Nama_lapangan, l.Lokasi, l.Jenis_olahraga,
                    l.Fasilitas, l.Harga, l.Foto,
                    COALESCE(AVG(r.Rating), 0) AS rating,
                    COUNT(r.ID_Review) AS review_count
             FROM favorit f
             INNER JOIN lapangan l ON l.ID_Lapangan = f.ID_Lapangan
             LEFT JOIN review r ON r.ID_Lapangan = l.ID_Lapangan AND LOWER(r.Status) = 'tampil'
             WHERE f.ID_User = ? AND l.deleted_at IS NULL
             GROUP BY l.ID_Lapangan, l.Nama_lapangan, l.Lokasi, l.Jenis_olahraga,
                      l.Fasilitas, l.Harga, l.Foto, f.created_at
             ORDER BY f.created_at DESC",
            's',
            array($userId)
        );
        $favorites = array();

        foreach ($rows as $row) {
            $features = $this->decodeDashboardList(isset($row['Fasilitas']) ? $row['Fasilitas'] : '');
            $favorites[] = array(
                'id' => $row['ID_Lapangan'],
                'type' => $row['Jenis_olahraga'],
                'venue' => $row['Nama_lapangan'],
                'location' => $row['Lokasi'],
                'features' => $features,
                'rating' => number_format((float) $row['rating'], 1),
                'reviews' => (int) $row['review_count'] . ' ulasan',
                'distance' => '-',
                'price' => $this->dashboardRupiah($row['Harga']),
                'image' => $this->dashboardVenueImage($row['Foto'], $row['Jenis_olahraga']),
            );
        }

        return $favorites;
    }

    protected function customerReviewsFromDatabase()
    {
        $userId = $this->dashboardUserId();

        if ($userId === '') {
            return array();
        }

        $rows = $this->dashboardData()->rows(
            "SELECT r.Rating, r.Komentar, r.created_at, r.ID_Booking,
                    l.Nama_lapangan, l.Jenis_olahraga, l.Lokasi, l.Foto,
                    (SELECT COUNT(*) FROM review all_reviews WHERE all_reviews.ID_Lapangan = l.ID_Lapangan) AS review_count
             FROM review r
             INNER JOIN lapangan l ON l.ID_Lapangan = r.ID_Lapangan
             WHERE r.ID_User = ?
             ORDER BY r.created_at DESC",
            's',
            array($userId)
        );
        $reviews = array();

        foreach ($rows as $row) {
            $reviews[] = array(
                'venue' => $row['Nama_lapangan'],
                'type' => $row['Jenis_olahraga'],
                'location' => $row['Lokasi'],
                'rating' => (float) $row['Rating'],
                'reviews' => (int) $row['review_count'],
                'comment' => $row['Komentar'],
                'date' => $this->dashboardFormatDate(substr((string) $row['created_at'], 0, 10)),
                'code' => isset($row['ID_Booking']) ? $row['ID_Booking'] : '-',
                'image' => $this->dashboardVenueImage($row['Foto'], $row['Jenis_olahraga']),
            );
        }

        return $reviews;
    }

    protected function customerReviewableBookings()
    {
        return $this->dashboardData()->rows(
            "SELECT b.ID_Booking,l.Nama_lapangan,j.Tanggal FROM booking b INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan LEFT JOIN review r ON r.ID_Booking=b.ID_Booking WHERE b.ID_User=? AND r.ID_Review IS NULL AND (LOWER(b.Status) IN ('selesai','completed') OR j.Tanggal<CURDATE()) ORDER BY j.Tanggal DESC",
            's', array($this->dashboardUserId())
        );
    }

    protected function dashboardFormatDate($date)
    {
        $timestamp = strtotime((string) $date);

        if (!$timestamp) {
            return '-';
        }

        $months = array(1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember');

        return date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    }

    protected function dashboardRupiah($amount)
    {
        return 'Rp' . number_format(max(0, (int) $amount), 0, ',', '.');
    }

    protected function customerAccountSettings()
    {
        $row = $this->dashboardData()->row(
            "SELECT u.Nama, u.Email, u.Nomor_telepon, u.Role, u.Kota, u.Alamat, u.Avatar,
                    u.Email_verified_at, u.created_at,
                    s.Theme_mode, s.Bahasa, s.Notifikasi_booking,
                    s.Notifikasi_jadwal, s.Notifikasi_promo,
                    s.Kota_favorit, s.Olahraga_favorit, s.Radius_pencarian
             FROM users u
             LEFT JOIN user_settings s ON s.ID_User = u.ID_User
             WHERE u.ID_User = ? LIMIT 1",
            's',
            array($this->dashboardUserId())
        );

        $row = $row ?: array();

        return array(
            'name' => isset($row['Nama']) ? $row['Nama'] : 'Pengguna Arena',
            'email' => isset($row['Email']) ? $row['Email'] : '',
            'phone' => isset($row['Nomor_telepon']) ? $row['Nomor_telepon'] : '',
            'role' => isset($row['Role']) ? $row['Role'] : 'customer',
            'city' => isset($row['Kota']) && trim((string) $row['Kota']) !== '' ? $row['Kota'] : 'Parepare',
            'address' => isset($row['Alamat']) && trim((string) $row['Alamat']) !== '' ? $row['Alamat'] : 'Belum diisi',
            'avatar' => isset($row['Avatar']) && trim((string) $row['Avatar']) !== '' ? $this->dashboardProfileImage($row['Avatar']) : 'https://ui-avatars.com/api/?name=' . rawurlencode(isset($row['Nama']) ? $row['Nama'] : 'Arena Sport') . '&background=20314a&color=ffffff',
            'joined' => isset($row['created_at']) ? $this->dashboardFormatDate(substr((string) $row['created_at'], 0, 10)) : '-',
            'verified' => !empty($row['Email_verified_at']),
            'theme' => isset($row['Theme_mode']) ? $row['Theme_mode'] : 'dark',
            'language' => isset($row['Bahasa']) ? $row['Bahasa'] : 'id',
            'notifyBooking' => !isset($row['Notifikasi_booking']) || (bool) $row['Notifikasi_booking'],
            'notifySchedule' => !isset($row['Notifikasi_jadwal']) || (bool) $row['Notifikasi_jadwal'],
            'notifyOffer' => isset($row['Notifikasi_promo']) && (bool) $row['Notifikasi_promo'],
            'notifyNew' => true,
            'favoriteCity' => isset($row['Kota_favorit']) && $row['Kota_favorit'] !== null ? $row['Kota_favorit'] : 'Parepare',
            'favoriteSport' => isset($row['Olahraga_favorit']) && $row['Olahraga_favorit'] !== null ? $row['Olahraga_favorit'] : 'Futsal',
            'searchRadius' => isset($row['Radius_pencarian']) ? (string) $row['Radius_pencarian'] : '10',
        );
    }

    protected function customerProfileMetrics()
    {
        $userId = $this->dashboardUserId();
        $data = $this->dashboardData();
        $totalBookings = (int) $data->value('SELECT COUNT(*) AS value FROM booking WHERE ID_User = ?', 's', array($userId));
        $completed = (int) $data->value("SELECT COUNT(*) AS value FROM booking WHERE ID_User = ? AND LOWER(Status) IN ('selesai','completed')", 's', array($userId));
        $paid = (int) $data->value("SELECT COALESCE(SUM(p.Jumlah),0) AS value FROM pembayaran p INNER JOIN booking b ON b.ID_Booking=p.ID_Booking WHERE b.ID_User=? AND LOWER(p.Status) IN ('berhasil','dibayar','lunas','success','paid')", 's', array($userId));
        $favorites = (int) $data->value('SELECT COUNT(*) AS value FROM favorit WHERE ID_User = ?', 's', array($userId));
        $reviews = (int) $data->value('SELECT COUNT(*) AS value FROM review WHERE ID_User = ?', 's', array($userId));
        $rating = (float) $data->value('SELECT COALESCE(AVG(Rating),0) AS value FROM review WHERE ID_User = ?', 's', array($userId));
        $notifications = (int) $data->value('SELECT COUNT(*) AS value FROM notifikasi WHERE ID_User = ? AND Dibaca_pada IS NULL', 's', array($userId));

        return array('bookings' => $totalBookings, 'completed' => $completed, 'paid' => $this->dashboardRupiah($paid), 'favorites' => $favorites, 'reviews' => $reviews, 'rating' => number_format($rating, 1), 'notifications' => $notifications);
    }

    protected function customerPaymentMethods()
    {
        return $this->dashboardData()->rows('SELECT ID_Metode, Nama FROM metode_pembayaran WHERE Aktif=1 ORDER BY Nama');
    }

    protected function dashboardProfileImage($avatar)
    {
        $avatar = trim((string) $avatar);

        if (preg_match('#^https?://#', $avatar)) {
            return $avatar;
        }

        return strpos($avatar, '..') === false ? app_url($avatar) : '';
    }

    protected function bookingCsrfToken()
    {
        if (empty($_SESSION['booking_csrf'])) {
            $_SESSION['booking_csrf'] = bin2hex(random_bytes(24));
        }

        return $_SESSION['booking_csrf'];
    }

    protected function accountTable($connection)
    {
        $result = mysqli_query($connection, "SHOW TABLES LIKE 'users'");

        return $result && mysqli_num_rows($result) > 0 ? 'users' : 'user';
    }

    public function settings()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        $settings = $this->customerAccountSettings();
        $_SESSION['theme_mode'] = $settings['theme'];

        return $this->view('dashboard/settings', array(
            'title' => 'Pengaturan Akun | Arena Sport',
            'themeMode' => $settings['theme'],
            'userName' => $settings['name'],
            'userEmail' => $settings['email'],
            'userPhone' => $settings['phone'],
            'userCity' => $settings['city'],
            'userRole' => $settings['role'],
            'language' => $settings['language'],
            'notifyBooking' => $settings['notifyBooking'],
            'notifySchedule' => $settings['notifySchedule'],
            'notifyOffer' => $settings['notifyOffer'],
            'notifyNew' => $settings['notifyNew'],
            'favoriteCity' => $settings['favoriteCity'],
            'favoriteSport' => $settings['favoriteSport'],
            'searchRadius' => $settings['searchRadius'],
        ), 'layouts/dashboard');
    }

    public function changePassword()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        return $this->renderPasswordView();
    }

    public function updatePassword()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        $currentPassword = isset($_POST['current_password']) ? (string) $_POST['current_password'] : '';
        $newPassword = isset($_POST['new_password']) ? (string) $_POST['new_password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? (string) $_POST['confirm_password'] : '';
        $message = '';
        $errorMessage = '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $errorMessage = 'Semua kolom password wajib diisi.';
        } elseif (strlen($newPassword) < 8) {
            $errorMessage = 'Password baru minimal 8 karakter.';
        } elseif (!preg_match('/[A-Z]/', $newPassword) || !preg_match('/[a-z]/', $newPassword) || !preg_match('/\d/', $newPassword) || !preg_match('/[^A-Za-z0-9]/', $newPassword)) {
            $errorMessage = 'Password baru harus berisi huruf besar, huruf kecil, angka, dan simbol.';
        } elseif ($newPassword !== $confirmPassword) {
            $errorMessage = 'Konfirmasi password baru belum sama.';
        } else {
            $account = $this->findUserAccount((string) $_SESSION['id_user']);

            if (!$account || empty($account['password'])) {
                $errorMessage = 'Data akun belum bisa dibaca dari database.';
            } elseif (!password_verify($currentPassword, (string) $account['password']) && !hash_equals((string) $account['password'], $currentPassword)) {
                $errorMessage = 'Password saat ini tidak sesuai.';
            } elseif ($this->updateUserPassword((string) $_SESSION['id_user'], password_hash($newPassword, PASSWORD_DEFAULT))) {
                $message = 'Password berhasil diperbarui.';
            } else {
                $errorMessage = 'Password belum bisa diperbarui. Silakan coba lagi.';
            }
        }

        return $this->renderPasswordView($message, $errorMessage);
    }

    protected function renderPasswordView($message = '', $errorMessage = '')
    {
        return $this->view('dashboard/change_password', array(
            'title' => 'Ubah Password | Arena Sport',
            'themeMode' => $this->dashboardThemeMode(),
            'displayDate' => $this->indonesianDateTime(),
            'message' => $message,
            'errorMessage' => $errorMessage,
        ), 'layouts/dashboard');
    }

    protected function indonesianDateTime()
    {
        $months = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        );

        $month = date('m');

        return date('j') . ' ' . $months[$month] . ' ' . date('Y, H:i');
    }

    protected function findUserAccount($userId)
    {
        $connection = Database::connection();
        $accountTable = $this->accountTable($connection);
        $statement = mysqli_prepare($connection, 'SELECT Password FROM `' . $accountTable . '` WHERE ID_User = ? LIMIT 1');

        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 's', $userId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $row = $result ? mysqli_fetch_assoc($result) : null;

        if (!$row) {
            return null;
        }

        return array(
            'password' => isset($row['Password']) ? $row['Password'] : '',
        );
    }

    protected function updateUserPassword($userId, $passwordHash)
    {
        $connection = Database::connection();
        $accountTable = $this->accountTable($connection);
        $statement = mysqli_prepare($connection, 'UPDATE `' . $accountTable . '` SET Password = ?, Must_Reset_Password = 0 WHERE ID_User = ?');

        if (!$statement) {
            return false;
        }

        mysqli_stmt_bind_param($statement, 'ss', $passwordHash, $userId);

        return mysqli_stmt_execute($statement);
    }

    public function updateSettings()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        $message = '';
        $errorMessage = '';
        $userId = $_SESSION['id_user'];
        $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $telepon = isset($_POST['telepon']) ? trim($_POST['telepon']) : '';
        $kota = isset($_POST['kota']) ? trim($_POST['kota']) : 'Parepare';
        $themeMode = isset($_POST['theme_mode']) && in_array($_POST['theme_mode'], array('dark', 'light')) ? $_POST['theme_mode'] : 'dark';
        $language = isset($_POST['language']) && in_array($_POST['language'], array('id', 'en')) ? $_POST['language'] : 'id';
        $notifyBooking = isset($_POST['notify_booking']);
        $notifySchedule = isset($_POST['notify_schedule']);
        $notifyOffer = isset($_POST['notify_offer']);
        $notifyNew = isset($_POST['notify_new']);
        $favoriteCity = trim($_POST['favorite_city'] ?? 'Parepare');
        $favoriteSport = trim($_POST['favorite_sport'] ?? 'Futsal');
        $searchRadius = trim($_POST['search_radius'] ?? '10');
        $searchRadius = preg_replace('/[^0-9]/', '', $searchRadius);
        if ($searchRadius === '') {
            $searchRadius = '10';
        }

        if ($nama === '' || $email === '') {
            $errorMessage = 'Nama dan email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = 'Email tidak valid.';
        } else {
            $connection = Database::connection();
            $accountTable = $this->accountTable($connection);
            $statement = mysqli_prepare($connection, 'UPDATE `' . $accountTable . '` SET Nama = ?, Email = ?, Nomor_telepon = ?, Kota = ? WHERE ID_User = ?');

            if ($statement) {
                mysqli_stmt_bind_param($statement, 'sssss', $nama, $email, $telepon, $kota, $userId);

                if (mysqli_stmt_execute($statement)) {
                    $this->dashboardData()->execute(
                        "INSERT INTO user_settings
                            (ID_User, Theme_mode, Bahasa, Notifikasi_booking, Notifikasi_jadwal,
                             Notifikasi_promo, Kota_favorit, Olahraga_favorit, Radius_pencarian)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                         ON DUPLICATE KEY UPDATE
                            Theme_mode = VALUES(Theme_mode), Bahasa = VALUES(Bahasa),
                            Notifikasi_booking = VALUES(Notifikasi_booking),
                            Notifikasi_jadwal = VALUES(Notifikasi_jadwal),
                            Notifikasi_promo = VALUES(Notifikasi_promo),
                            Kota_favorit = VALUES(Kota_favorit),
                            Olahraga_favorit = VALUES(Olahraga_favorit),
                            Radius_pencarian = VALUES(Radius_pencarian)",
                        'sssiiissi',
                        array($userId, $themeMode, $language, (int) $notifyBooking, (int) $notifySchedule, (int) $notifyOffer, $favoriteCity, $favoriteSport, (int) $searchRadius)
                    );
                    $_SESSION['nama_user'] = $nama;
                    $_SESSION['email_user'] = $email;
                    $_SESSION['telepon_user'] = $telepon;
                    $_SESSION['kota_user'] = $kota;
                    $message = 'Perubahan pengaturan berhasil disimpan.';
                } else {
                    $errorMessage = 'Tidak dapat menyimpan perubahan. Silakan coba lagi.';
                }
            } else {
                $errorMessage = 'Terjadi kesalahan koneksi database.';
            }
        }

        $_SESSION['theme_mode'] = $themeMode;
        $_SESSION['language'] = $language;
        $_SESSION['notify_booking'] = $notifyBooking;
        $_SESSION['notify_schedule'] = $notifySchedule;
        $_SESSION['notify_offer'] = $notifyOffer;
        $_SESSION['notify_new'] = $notifyNew;
        $_SESSION['favorite_city'] = $favoriteCity;
        $_SESSION['favorite_sport'] = $favoriteSport;
        $_SESSION['search_radius'] = $searchRadius;

        return $this->view('dashboard/settings', array(
            'title' => 'Pengaturan Akun | Arena Sport',
            'message' => $message,
            'errorMessage' => $errorMessage,
            'themeMode' => $themeMode,
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'userEmail' => isset($_SESSION['email_user']) ? $_SESSION['email_user'] : 'user@arenasport.id',
            'userPhone' => isset($_SESSION['telepon_user']) ? $_SESSION['telepon_user'] : '081234567890',
            'userCity' => isset($_SESSION['kota_user']) ? $_SESSION['kota_user'] : 'Parepare',
            'userRole' => isset($_SESSION['role_user']) ? $_SESSION['role_user'] : 'User',
            'language' => $language,
            'notifyBooking' => $notifyBooking,
            'notifySchedule' => $notifySchedule,
            'notifyOffer' => $notifyOffer,
            'notifyNew' => $notifyNew,
            'favoriteCity' => $favoriteCity,
            'favoriteSport' => $favoriteSport,
            'searchRadius' => $searchRadius,
        ), 'layouts/dashboard');
    }

    public function updateTheme()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(array('ok' => false, 'message' => 'Sesi login sudah berakhir.'));
            return;
        }

        $themeMode = isset($_POST['theme_mode']) && in_array($_POST['theme_mode'], array('dark', 'light'), true) ? $_POST['theme_mode'] : 'dark';
        $_SESSION['theme_mode'] = $themeMode;
        $this->dashboardData()->execute(
            "INSERT INTO user_settings (ID_User, Theme_mode)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE Theme_mode = VALUES(Theme_mode)",
            'ss',
            array($this->dashboardUserId(), $themeMode)
        );

        header('Content-Type: application/json');
        echo json_encode(array('ok' => true, 'themeMode' => $themeMode));
    }
}
