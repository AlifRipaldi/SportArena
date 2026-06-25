<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Booking;
use App\Models\Lapangan;

class DashboardController extends Controller
{
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

    public function ulasan()
    {
        $this->requireUser();

        return $this->view('dashboard/ulasan', array(
            'title' => 'Ulasan Saya | Arena Sport',
            'activeMenu' => 'ulasan',
            'pageHeading' => 'Ulasan Saya',
            'pageSubheading' => 'Lihat dan kelola semua ulasan yang pernah kamu berikan',
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'reviewSummary' => $this->reviewSummary(),
            'reviews' => $this->reviews(),
        ), 'layouts/dashboard');
    }

    public function profil()
    {
        $this->requireUser();

        return $this->view('dashboard/profil', array(
            'title' => 'Profil Saya | Arena Sport',
            'activeMenu' => 'profil',
            'pageHeading' => 'Profil Saya',
            'pageSubheading' => 'Kelola informasi profil dan aktivitas Anda.',
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'userEmail' => isset($_SESSION['email_user']) ? $_SESSION['email_user'] : 'user@arenasport.id',
            'userPhone' => isset($_SESSION['telepon_user']) ? $_SESSION['telepon_user'] : '081234567890',
            'userCity' => isset($_SESSION['kota_user']) ? $_SESSION['kota_user'] : 'Parepare',
        ), 'layouts/dashboard');
    }

    protected function renderDashboard($view, $activeMenu, $title, $heading, $subheading)
    {
        $this->requireUser();

        return $this->view($view, array(
            'title' => $title,
            'activeMenu' => $activeMenu,
            'pageHeading' => $heading,
            'pageSubheading' => $subheading,
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'stats' => $this->stats(),
            'venues' => $this->venues(),
            'nextBooking' => $this->nextBooking(),
            'bookings' => $this->bookings(),
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

    public function riwayat()
    {
        $this->requireUser();

        return $this->view('dashboard/riwayat', array(
            'title' => 'Riwayat Booking | Arena Sport',
            'activeMenu' => 'riwayat',
            'pageHeading' => 'Riwayat',
            'pageSubheading' => 'Lihat semua riwayat booking lapangan kamu',
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'bookings' => $this->historyBookings(),
        ), 'layouts/dashboard');
    }

    public function favorit()
    {
        $this->requireUser();

        return $this->renderDashboard('dashboard/favorit', 'favorit', 'Favorit | Arena Sport', 'Favorit', 'Lapangan favorit yang ingin kamu mainkan');
    }

    protected function stats()
    {
        $userId = $this->currentUserId();
        $bookingModel = new Booking();
        $lapanganModel = new Lapangan();

        return array(
            array(
                'label' => 'Booking Aktif',
                'value' => (string) $bookingModel->countUpcomingByUser($userId),
                'icon' => '&#128197;',
                'accent' => 'green',
            ),
            array(
                'label' => 'Selesai',
                'value' => (string) $bookingModel->countPastByUser($userId),
                'icon' => '&#10003;',
                'accent' => 'blue',
            ),
            array(
                'label' => 'Lapangan Tersedia',
                'value' => (string) $lapanganModel->countAll(),
                'icon' => '&#x1F3DF;',
                'accent' => 'purple',
            ),
            array(
                'label' => 'Rating Anda',
                'value' => '4.8',
                'icon' => '&#9734;',
                'accent' => 'orange',
            ),
        );
    }

    protected function venues()
    {
        $rows = (new Lapangan())->popular(4);
        $fields = array();

        foreach ($rows as $row) {
            $fields[] = array(
                'name' => $row['Nama_lapangan'],
                'location' => $row['Lokasi'],
                'rating' => $this->ratingForType($row['Jenis_olahraga']),
                'reviews' => $this->reviewCountForType($row['Jenis_olahraga']),
                'price' => 'Rp' . number_format($row['Harga'], 0, ',', '.'),
                'image' => $this->venueImageForType($row['Jenis_olahraga']),
            );
        }

        return $fields;
    }

    protected function favorites()
    {
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

    protected function ratingForType($type)
    {
        $ratings = array(
            'Futsal' => '4.8',
            'Badminton' => '4.6',
            'Mini Soccer' => '4.7',
            'Basketball' => '4.5',
        );

        return isset($ratings[$type]) ? $ratings[$type] : '4.6';
    }

    protected function reviewCountForType($type)
    {
        $counts = array(
            'Futsal' => '120',
            'Badminton' => '85',
            'Mini Soccer' => '98',
            'Basketball' => '76',
        );

        return isset($counts[$type]) ? $counts[$type] : '80';
    }

    protected function venueImageForType($type)
    {
        $images = array(
            'Futsal' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=900&auto=format&fit=crop',
            'Badminton' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=900&auto=format&fit=crop',
            'Mini Soccer' => 'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?q=80&w=900&auto=format&fit=crop',
            'Basketball' => 'https://images.unsplash.com/photo-1521093721353-fcc2b798fbd5?q=80&w=900&auto=format&fit=crop',
        );

        return isset($images[$type]) ? $images[$type] : 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=900&auto=format&fit=crop';
    }

    protected function reviewSummary()
    {
        return array(
            'average' => '4.7',
            'total' => '12',
            'positive' => '11',
            'negative' => '1',
            'positivePercent' => '91.7%',
            'negativePercent' => '8.3%',
        );
    }

    protected function reviews()
    {
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

    protected function accountTable($connection)
    {
        $result = mysqli_query($connection, "SHOW TABLES LIKE 'users'");

        return $result && mysqli_num_rows($result) > 0 ? 'users' : 'user';
    }

    public function settings()
    {
        $this->requireUser();

        return $this->view('dashboard/settings', array(
            'title' => 'Pengaturan Akun | Arena Sport',
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'userEmail' => isset($_SESSION['email_user']) ? $_SESSION['email_user'] : 'user@arenasport.id',
            'userPhone' => isset($_SESSION['telepon_user']) ? $_SESSION['telepon_user'] : '081234567890',
            'userCity' => isset($_SESSION['kota_user']) ? $_SESSION['kota_user'] : 'Parepare',
            'userRole' => isset($_SESSION['role_user']) ? $_SESSION['role_user'] : 'User',
            'themeMode' => isset($_SESSION['theme_mode']) ? $_SESSION['theme_mode'] : 'dark',
            'language' => isset($_SESSION['language']) ? $_SESSION['language'] : 'id',
            'notifyBooking' => isset($_SESSION['notify_booking']) ? $_SESSION['notify_booking'] : true,
            'notifySchedule' => isset($_SESSION['notify_schedule']) ? $_SESSION['notify_schedule'] : true,
            'notifyOffer' => isset($_SESSION['notify_offer']) ? $_SESSION['notify_offer'] : false,
            'notifyNew' => isset($_SESSION['notify_new']) ? $_SESSION['notify_new'] : true,
            'favoriteCity' => isset($_SESSION['favorite_city']) ? $_SESSION['favorite_city'] : 'Parepare',
            'favoriteSport' => isset($_SESSION['favorite_sport']) ? $_SESSION['favorite_sport'] : 'Futsal',
            'searchRadius' => isset($_SESSION['search_radius']) ? $_SESSION['search_radius'] : '10',
        ), 'layouts/dashboard');
    }

    public function updateSettings()
    {
        $this->requireUser();

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
            $statement = mysqli_prepare($connection, 'UPDATE `' . $accountTable . '` SET Nama = ?, Email = ?, Nomor_telepon = ? WHERE ID_User = ?');

            if ($statement) {
                mysqli_stmt_bind_param($statement, 'ssss', $nama, $email, $telepon, $userId);

                if (mysqli_stmt_execute($statement)) {
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
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'userEmail' => isset($_SESSION['email_user']) ? $_SESSION['email_user'] : 'user@arenasport.id',
            'userPhone' => isset($_SESSION['telepon_user']) ? $_SESSION['telepon_user'] : '081234567890',
            'userCity' => isset($_SESSION['kota_user']) ? $_SESSION['kota_user'] : 'Parepare',
            'userRole' => isset($_SESSION['role_user']) ? $_SESSION['role_user'] : 'User',
            'themeMode' => $themeMode,
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
}
