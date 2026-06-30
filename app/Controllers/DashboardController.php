<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Booking;
use App\Models\Lapangan;
use App\Models\ArenaData;

class DashboardController extends Controller
{
    protected function dashboardThemeMode()
    {
        return isset($_SESSION['theme_mode']) && $_SESSION['theme_mode'] === 'light' ? 'light' : 'dark';
    }

    public function dashboard()
    {
        $this->requireUser();

        return $this->renderDashboard('dashboard/index', 'dashboard', 'Dashboard | Arena Sport', 'Dashboard', 'Ringkasan aktivitas dan pesanan Anda saat ini.');
    }

    public function search()
    {
        $this->requireUser();

        return $this->renderDashboard('dashboard/search', 'lapangan', 'Cari Lapangan | Arena Sport', 'Cari Lapangan', 'Temukan lapangan terbaik di sekitar kamu.');
    }

    public function fieldDetail($id)
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_user'])) { header('Location: ' . app_url('public/login.php')); exit; }
        if (!headers_sent()) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        }

        $fieldId = rawurldecode(trim((string) $id));
        $venue = null;
        foreach ($this->databaseVenues() as $item) {
            if (isset($item['id']) && hash_equals((string) $item['id'], $fieldId)) {
                $venue = $item;
                break;
            }
        }

        if (!$venue) {
            $_SESSION['booking_error'] = 'Lapangan tidak ditemukan atau sedang tidak aktif.';
            header('Location: ' . app_url('dashboard/lapangan'));
            exit;
        }

        $reviews = $this->dashboardData()->rows(
            "SELECT r.Rating, r.Komentar, r.created_at, u.Nama
             FROM review r
             INNER JOIN users u ON u.ID_User=r.ID_User
             WHERE r.ID_Lapangan=? AND LOWER(r.Status)='tampil'
             ORDER BY r.created_at DESC",
            's', array($fieldId)
        );
        $operatingHours = $this->dashboardData()->rows(
            'SELECT Hari, Jam_buka, Jam_tutup, Tutup FROM jam_operasional WHERE ID_Lapangan=? ORDER BY Hari',
            's', array($fieldId)
        );

        return $this->view('dashboard/field_detail', array(
            'title' => $venue['name'] . ' | Arena Sport',
            'themeMode' => $this->dashboardThemeMode(),
            'venue' => $venue,
            'reviews' => $reviews,
            'operatingHours' => $operatingHours,
            'bookingCsrfToken' => $this->bookingCsrfToken(),
        ), 'layouts/dashboard');
    }

    public function ulasan()
    {
        $this->requireUser();

        $reviewMessage = isset($_SESSION['review_success']) ? (string) $_SESSION['review_success'] : '';
        $reviewError = isset($_SESSION['review_error']) ? (string) $_SESSION['review_error'] : '';
        unset($_SESSION['review_success'], $_SESSION['review_error']);
        $profile = $this->customerAccountSettings();

        return $this->view('dashboard/ulasan', array(
            'title' => 'Ulasan Saya | Arena Sport',
            'themeMode' => $this->dashboardThemeMode(),
            'activeMenu' => 'ulasan',
            'pageHeading' => 'Ulasan Saya',
            'pageSubheading' => 'Lihat dan kelola semua ulasan yang pernah kamu berikan',
            'userName' => $profile['name'],
            'userAvatar' => $profile['avatar'],
            'reviewSummary' => $this->reviewSummary(),
            'reviews' => $this->reviews(),
            'reviewableBookings' => $this->customerReviewableBookings(),
            'bookingCsrfToken' => $this->bookingCsrfToken(),
            'reviewMessage' => $reviewMessage,
            'reviewError' => $reviewError,
        ), 'layouts/dashboard');
    }

    public function storeReview()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_user'])) { header('Location: ' . app_url('public/login.php')); exit; }
        if (!$this->hasValidBookingToken()) {
            $_SESSION['review_error'] = 'Permintaan ulasan tidak valid.';
            header('Location: ' . app_url('dashboard/ulasan'));
            exit;
        }

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
                $saved = $this->dashboardData()->execute("INSERT INTO review (ID_Review,ID_User,ID_Lapangan,ID_Booking,Rating,Komentar,Status) VALUES (?,?,?,?,?,?,'Tampil')", 'ssssis', array($reviewId, $this->dashboardUserId(), $row['ID_Lapangan'], $bookingId, $rating, $comment));
                $_SESSION[$saved ? 'review_success' : 'review_error'] = $saved ? 'Ulasan berhasil dikirim.' : 'Ulasan belum dapat disimpan.';
            } else {
                $_SESSION['review_error'] = 'Booking tidak dapat diulas atau sudah pernah diulas.';
            }
        } else {
            $_SESSION['review_error'] = 'Lengkapi booking, rating, dan komentar ulasan.';
        }

        header('Location: ' . app_url('dashboard/ulasan'));
        exit;
    }

    public function profil()
    {
        $this->requireUser();

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
        $this->requireUser();

        if (!headers_sent()) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        }

        $bookingMessage = isset($_SESSION['booking_success']) ? (string) $_SESSION['booking_success'] : '';
        $bookingError = isset($_SESSION['booking_error']) ? (string) $_SESSION['booking_error'] : '';
        unset($_SESSION['booking_success'], $_SESSION['booking_error']);
        $profile = $this->customerAccountSettings();

        return $this->view($view, array(
            'title' => $title,
            'themeMode' => $this->dashboardThemeMode(),
            'activeMenu' => $activeMenu,
            'pageHeading' => $heading,
            'pageSubheading' => $subheading,
            'userName' => $profile['name'],
            'userAvatar' => $profile['avatar'],
            'stats' => $this->stats(),
            'venues' => $this->venues(),
            'nextBooking' => $this->nextBooking(),
            'bookings' => $this->bookings(),
            'paymentMethods' => $this->customerPaymentMethods(),
            'bookingCsrfToken' => $this->bookingCsrfToken(),
            'bookingMessage' => $bookingMessage,
            'bookingError' => $bookingError,
        ), 'layouts/dashboard');
    }

    protected function currentUserId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;
    }

    protected function requireUser()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        $role = isset($_SESSION['role_user']) ? $_SESSION['role_user'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');

        if (!$this->isUserRole($role)) {
            $redirectRole = $this->normalizeRole($role);

            if (in_array($redirectRole, array('admin', 'administrator', 'superadmin'), true)) {
                header('Location: ' . app_url('admin/dashboard'));
                exit;
            }

            if (in_array($redirectRole, array('owner', 'pemilik', 'pemilik lapangan', 'mitra'), true)) {
                header('Location: ' . app_url('pemilik/dashboard'));
                exit;
            }

            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        return true;
    }

    protected function isUserRole($role)
    {
        return in_array($this->normalizeRole($role), array('user', 'customer', 'pelanggan'), true);
    }

    protected function normalizeRole($role)
    {
        return strtolower(str_replace(array('_', '-'), ' ', trim((string) $role)));
    }

    protected function bookings()
    {
        $userId = $this->currentUserId();
        return (new Booking())->upcomingByUser($userId, 6);
    }

    protected function historyBookings()
    {
        $userId = $this->currentUserId();
        return (new Booking())->pastByUser($userId, 8);
    }

    protected function nextBooking()
    {
        $userId = $this->currentUserId();
        return (new Booking())->nextUpcomingByUser($userId);
    }

    public function booking()
    {
        $this->requireUser();

        return $this->renderDashboard('dashboard/booking', 'booking', 'Booking Saya | Arena Sport', 'Booking Saya', 'Kelola semua booking lapangan kamu');
    }

    public function storeBooking()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_user'])) { header('Location: ' . app_url('public/login.php')); exit; }

        $scheduleId = isset($_POST['id_jadwal']) ? trim((string) $_POST['id_jadwal']) : '';
        if (!$this->hasValidBookingToken()) {
            $_SESSION['booking_error'] = 'Permintaan booking tidak valid. Silakan pilih jadwal kembali.';
            header('Location: ' . app_url('dashboard/lapangan'));
            exit;
        }

        $connection = Database::connection();
        mysqli_begin_transaction($connection);

        try {
            $booking = $this->reserveBooking($connection, $scheduleId, $this->dashboardUserId());
            $this->createBookingNotifications($connection, $booking);
            mysqli_commit($connection);
            $_SESSION['booking_success'] = 'Booking ' . $booking['venue'] . ' berhasil dibuat. Silakan selesaikan pembayaran.';
            header('Location: ' . app_url('dashboard/booking'));
            exit;
        } catch (\Throwable $exception) {
            mysqli_rollback($connection);
            $_SESSION['booking_error'] = $exception->getMessage();
            header('Location: ' . app_url('dashboard/lapangan'));
            exit;
        }
    }

    protected function reserveBooking($connection, $scheduleId, $userId)
    {
        if ($scheduleId === '' || $userId === '') {
            throw new \RuntimeException('Jadwal booking tidak valid.');
        }

        $statement = mysqli_prepare(
            $connection,
            "SELECT j.ID_Jadwal, j.Tanggal, j.Jam_Mulai,
                    COALESCE(NULLIF(j.Harga, 0), l.Harga) AS Harga,
                    l.Nama_lapangan, p.ID_User AS ID_Pemilik_User
             FROM jadwal j
             INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan
             INNER JOIN pemilik_lapangan p ON p.ID_Pemilik=l.ID_Pemilik
             WHERE j.ID_Jadwal=? AND LOWER(TRIM(j.Status)) IN ('available','tersedia','aktif')
               AND LOWER(TRIM(l.Status))='aktif' AND l.deleted_at IS NULL
             LIMIT 1 FOR UPDATE"
        );
        if (!$statement) { throw new \RuntimeException('Jadwal tidak dapat dibaca.'); }
        mysqli_stmt_bind_param($statement, 's', $scheduleId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $schedule = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);

        if (!$schedule) { throw new \RuntimeException('Jadwal sudah tidak tersedia.'); }
        $startAt = strtotime($schedule['Tanggal'] . ' ' . $schedule['Jam_Mulai']);
        if ($startAt === false || $startAt <= time()) { throw new \RuntimeException('Jadwal sudah lewat.'); }

        $bookingId = 'BKG' . date('ymdHis') . random_int(10, 99);
        $price = max(0, (int) $schedule['Harga']);
        $createdAt = date('Y-m-d H:i:s');
        $insert = mysqli_prepare($connection, "INSERT INTO booking (ID_Booking,ID_Jadwal,ID_User,Waktu_transaksi,Total_harga,Status) VALUES (?,?,?,?,?,'Menunggu Pembayaran')");
        if (!$insert) { throw new \RuntimeException('Booking tidak dapat dibuat.'); }
        mysqli_stmt_bind_param($insert, 'ssssi', $bookingId, $scheduleId, $userId, $createdAt, $price);
        if (!mysqli_stmt_execute($insert)) {
            mysqli_stmt_close($insert);
            throw new \RuntimeException('Booking gagal disimpan.');
        }
        mysqli_stmt_close($insert);

        $update = mysqli_prepare($connection, "UPDATE jadwal SET Status='Booked' WHERE ID_Jadwal=? AND LOWER(TRIM(Status)) IN ('available','tersedia','aktif')");
        if (!$update) { throw new \RuntimeException('Jadwal gagal diamankan.'); }
        mysqli_stmt_bind_param($update, 's', $scheduleId);
        mysqli_stmt_execute($update);
        $updated = mysqli_stmt_affected_rows($update);
        mysqli_stmt_close($update);
        if ($updated !== 1) { throw new \RuntimeException('Jadwal baru saja dipesan pengguna lain.'); }

        return array(
            'id' => $bookingId,
            'userId' => $userId,
            'ownerUserId' => isset($schedule['ID_Pemilik_User']) ? $schedule['ID_Pemilik_User'] : '',
            'venue' => isset($schedule['Nama_lapangan']) ? $schedule['Nama_lapangan'] : 'lapangan',
        );
    }

    protected function createBookingNotifications($connection, array $booking)
    {
        $statement = mysqli_prepare($connection, "INSERT INTO notifikasi (ID_User,Judul,Pesan,Tipe,Link) VALUES (?,?,?,?,?)");
        if (!$statement) { return; }

        $type = 'booking';
        $link = 'dashboard/booking';
        $title = 'Booking berhasil dibuat';
        $message = 'Booking ' . $booking['venue'] . ' menunggu pembayaran.';
        mysqli_stmt_bind_param($statement, 'sssss', $booking['userId'], $title, $message, $type, $link);
        mysqli_stmt_execute($statement);

        if (!empty($booking['ownerUserId'])) {
            $ownerTitle = 'Booking baru';
            $ownerMessage = 'Ada booking baru untuk ' . $booking['venue'] . '.';
            $ownerLink = 'pemilik/booking';
            mysqli_stmt_bind_param($statement, 'sssss', $booking['ownerUserId'], $ownerTitle, $ownerMessage, $type, $ownerLink);
            mysqli_stmt_execute($statement);
        }
        mysqli_stmt_close($statement);
    }

    public function updateBooking()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_user'])) { header('Location: ' . app_url('public/login.php')); exit; }

        if (!$this->hasValidBookingToken()) {
            $_SESSION['booking_error'] = 'Permintaan perubahan booking tidak valid.';
            header('Location: ' . app_url('dashboard/booking'));
            exit;
        }

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

            $currentStatus = strtolower(trim((string) $booking['Status']));
            if (strpos($currentStatus, 'batal') !== false || strpos($currentStatus, 'selesai') !== false || strpos($currentStatus, 'complete') !== false) {
                throw new \RuntimeException('Booking ini tidak dapat diubah lagi.');
            }

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
                $slot = isset($_POST['booking_slot']) ? trim((string) $_POST['booking_slot']) : '';
                $slotParts = explode('@', $slot, 2);
                $date = isset($slotParts[0]) ? trim($slotParts[0]) : '';
                $time = isset($slotParts[1]) ? trim($slotParts[1]) : '';
                $parts = preg_split('/\s*-\s*/', $time);
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || count($parts) !== 2) { throw new \RuntimeException('Jadwal baru tidak valid.'); }
                $start = $parts[0] . ':00'; $end = $parts[1] . ':00';
                if (strtotime($date . ' ' . $start) <= time()) { throw new \RuntimeException('Jadwal baru harus berada di waktu mendatang.'); }
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
            $_SESSION['booking_success'] = $action === 'cancel' ? 'Booking berhasil dibatalkan.' : 'Jadwal booking berhasil diubah.';
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

        if (!$this->hasValidBookingToken()) {
            $_SESSION['booking_error'] = 'Permintaan pembayaran tidak valid.';
            header('Location: ' . app_url('dashboard/booking'));
            exit;
        }

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
            if (stripos((string) $booking['Status'], 'menunggu') === false && stripos((string) $booking['Status'], 'pending') === false) { throw new \RuntimeException('Booking ini tidak sedang menunggu pembayaran.'); }

            $existing = mysqli_prepare($connection, "SELECT ID_Pembayaran FROM pembayaran WHERE ID_Booking=? AND LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid') LIMIT 1");
            mysqli_stmt_bind_param($existing, 's', $bookingId); mysqli_stmt_execute($existing); $existingResult=mysqli_stmt_get_result($existing); $paid=$existingResult?mysqli_fetch_assoc($existingResult):null; mysqli_stmt_close($existing);
            if ($paid) { throw new \RuntimeException('Booking sudah dibayar.'); }

            $paymentId='PAY'.date('ymdHis').random_int(10,99); $reference='REF'.strtoupper(bin2hex(random_bytes(5))); $amount=(int)$booking['Total_harga']; $methodName=$method['Nama'];
            $insert=mysqli_prepare($connection,"INSERT INTO pembayaran (ID_Pembayaran,ID_Booking,Jumlah,Keterangan,Metode,Status,Referensi,Waktu_pembayaran) VALUES (?,?,?,'Pembayaran booking',?,'Berhasil',?,NOW())");
            mysqli_stmt_bind_param($insert,'ssiss',$paymentId,$bookingId,$amount,$methodName,$reference); if(!mysqli_stmt_execute($insert)){throw new \RuntimeException('Pembayaran gagal disimpan.');} mysqli_stmt_close($insert);
            $update=mysqli_prepare($connection,"UPDATE booking SET Status='Aktif' WHERE ID_Booking=?"); mysqli_stmt_bind_param($update,'s',$bookingId); mysqli_stmt_execute($update); mysqli_stmt_close($update);
            mysqli_commit($connection);
            $_SESSION['booking_success']='Pembayaran booking berhasil dikonfirmasi.';
        } catch (\Throwable $exception) {
            mysqli_rollback($connection); $_SESSION['booking_error']=$exception->getMessage();
        }

        header('Location: ' . app_url('dashboard/booking'));
        exit;
    }

    protected function dashboardCancelPendingPayments($connection, $bookingId)
    {
        $statement = mysqli_prepare(
            $connection,
            "UPDATE pembayaran
             SET Status=CASE
                 WHEN LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid') THEN 'Refund Pending'
                 ELSE 'Dibatalkan'
             END
             WHERE ID_Booking=? AND LOWER(Status) IN ('pending','menunggu','berhasil','dibayar','lunas','success','paid')"
        );
        if ($statement) { mysqli_stmt_bind_param($statement, 's', $bookingId); mysqli_stmt_execute($statement); mysqli_stmt_close($statement); }
    }

    public function riwayat()
    {
        $this->requireUser();

        $profile = $this->customerAccountSettings();

        return $this->view('dashboard/riwayat', array(
            'title' => 'Riwayat Booking | Arena Sport',
            'themeMode' => $this->dashboardThemeMode(),
            'activeMenu' => 'riwayat',
            'pageHeading' => 'Riwayat',
            'pageSubheading' => 'Lihat semua riwayat booking lapangan kamu',
            'userName' => $profile['name'],
            'userAvatar' => $profile['avatar'],
            'bookings' => $this->historyBookings(),
        ), 'layouts/dashboard');
    }
}