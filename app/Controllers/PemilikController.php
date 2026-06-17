<?php

namespace App\Controllers;

use App\Core\Controller;

class PemilikController extends Controller
{
    public function index()
    {
        $owner = $this->requireOwner();

        return $this->view('Owner/index', array(
            'title' => 'Dashboard Pemilik | Arena Sport',
            'activeMenu' => 'dashboard',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'summaryCards' => $this->summaryCards(),
            'monthlyRevenue' => $this->monthlyRevenue(),
            'bookingStatus' => $this->bookingStatus(),
            'recentBookings' => $this->recentBookings(),
            'ownerFields' => $this->ownerFields(),
            'latestReviews' => $this->latestReviews(),
        ), 'layouts/owner');
    }

    public function lapangan()
    {
        $owner = $this->requireOwner();

        return $this->view('Owner/lapangan', array(
            'title' => 'Kelola Lapangan | Arena Sport',
            'activeMenu' => 'lapangan',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'lapangan' => $this->getAllLapangan(),
        ), 'layouts/owner');
    }

    public function booking()
    {
        $owner = $this->requireOwner();

        return $this->view('Owner/booking', array(
            'title' => 'Manajemen Booking | Arena Sport',
            'activeMenu' => 'booking',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'bookings' => $this->getOwnerBookings(),
        ), 'layouts/owner');
    }

    public function jadwal()
    {
        $owner = $this->requireOwner();

        return $this->view('Owner/jadwal', array(
            'title' => 'Jadwal Booking | Arena Sport',
            'activeMenu' => 'jadwal',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'schedule' => $this->getSchedule(),
            'selectedDate' => '16 Juni 2025',
        ), 'layouts/owner');
    }

    public function pendapatan()
    {
        $owner = $this->requireOwner();

        return $this->view('Owner/pendapatan', array(
            'title' => 'Pendapatan | Arena Sport',
            'activeMenu' => 'pendapatan',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'revenueStats' => $this->revenueStats(),
            'revenueChart' => $this->dailyRevenueChart(),
            'revenueSummary' => $this->revenueSummary(),
            'revenueTransactions' => $this->revenueTransactions(),
            'selectedPeriod' => 'Juni 2025',
        ), 'layouts/owner');
    }

    public function ulasan()
    {
        $owner = $this->requireOwner();

        return $this->view('Owner/ulasan', array(
            'title' => 'Ulasan & Rating | Arena Sport',
            'activeMenu' => 'ulasan',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'reviewStats' => $this->ownerReviewStats(),
            'ratingDistribution' => $this->ownerRatingDistribution(),
            'fieldRatings' => $this->ownerFieldRatings(),
            'reviews' => $this->ownerReviewRows(),
        ), 'layouts/owner');
    }

    public function profil()
    {
        $owner = $this->requireOwner();
        $profile = $this->ownerProfile($owner['name']);

        return $this->view('Owner/profil', array(
            'title' => 'Profil | Arena Sport',
            'activeMenu' => 'profil',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'profile' => $profile,
            'managedFields' => $this->profileManagedFields(),
        ), 'layouts/owner');
    }

    protected function requireOwner()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        $role = isset($_SESSION['role_user']) ? $_SESSION['role_user'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');

        if (!$this->isOwnerRole($role)) {
            header('Location: ' . app_url('dashboard'));
            exit;
        }

        return array(
            'name' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : (isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pemilik Arena'),
            'role' => $role,
        );
    }

    protected function isOwnerRole($role)
    {
        return in_array($this->normalizeRole($role), array('owner', 'pemilik', 'pemilik lapangan', 'mitra'), true);
    }

    protected function normalizeRole($role)
    {
        return strtolower(str_replace(array('_', '-'), ' ', trim((string) $role)));
    }

    protected function summaryCards()
    {
        return array(
            array(
                'label' => 'Total Lapangan',
                'value' => '3',
                'trend' => 'Lapangan Aktif',
                'note' => '',
                'icon' => 'fa-map-location-dot',
                'accent' => 'lime',
            ),
            array(
                'label' => 'Booking Hari Ini',
                'value' => '18',
                'trend' => '12%',
                'note' => 'dari kemarin',
                'icon' => 'fa-calendar-days',
                'accent' => 'blue',
            ),
            array(
                'label' => 'Pendapatan Bulan Ini',
                'value' => 'Rp4.250.000',
                'trend' => '18.6%',
                'note' => 'dari bulan lalu',
                'icon' => 'fa-rupiah-sign',
                'accent' => 'green',
            ),
            array(
                'label' => 'Rating Rata-rata',
                'value' => '4.8',
                'trend' => '0.3',
                'note' => 'dari bulan lalu',
                'icon' => 'fa-star',
                'accent' => 'purple',
            ),
        );
    }

    protected function monthlyRevenue()
    {
        return array(
            array('month' => 'Jan', 'amount' => 'Rp2jt', 'x' => 0, 'y' => 80),
            array('month' => 'Feb', 'amount' => 'Rp3.4jt', 'x' => 9.1, 'y' => 66),
            array('month' => 'Mar', 'amount' => 'Rp4jt', 'x' => 18.2, 'y' => 60),
            array('month' => 'Apr', 'amount' => 'Rp5.1jt', 'x' => 27.3, 'y' => 49),
            array('month' => 'Mei', 'amount' => 'Rp7jt', 'x' => 36.4, 'y' => 30),
            array('month' => 'Jun', 'amount' => 'Rp4.8jt', 'x' => 45.5, 'y' => 52),
            array('month' => 'Jul', 'amount' => 'Rp6.5jt', 'x' => 54.6, 'y' => 35),
            array('month' => 'Agu', 'amount' => 'Rp8.3jt', 'x' => 63.7, 'y' => 17),
            array('month' => 'Sep', 'amount' => 'Rp7.1jt', 'x' => 72.8, 'y' => 29),
            array('month' => 'Okt', 'amount' => 'Rp9.1jt', 'x' => 81.9, 'y' => 9),
            array('month' => 'Nov', 'amount' => 'Rp9.8jt', 'x' => 91, 'y' => 2),
            array('month' => 'Des', 'amount' => 'Rp7jt', 'x' => 100, 'y' => 30),
        );
    }

    protected function bookingStatus()
    {
        return array(
            array('label' => 'Selesai', 'value' => '55%', 'count' => '66', 'color' => 'lime'),
            array('label' => 'Aktif', 'value' => '25%', 'count' => '30', 'color' => 'blue'),
            array('label' => 'Pending', 'value' => '15%', 'count' => '18', 'color' => 'gold'),
            array('label' => 'Dibatalkan', 'value' => '5%', 'count' => '6', 'color' => 'red'),
        );
    }

    protected function recentBookings()
    {
        return array(
            array('code' => 'AS-20260617-001', 'field' => 'Arena Futsal A', 'user' => 'Ahmad', 'date' => '17 Juni 2026', 'time' => '19:00 - 20:00', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('code' => 'AS-20260617-002', 'field' => 'Arena Badminton 1', 'user' => 'Rizal', 'date' => '17 Juni 2026', 'time' => '20:00 - 21:00', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp60.000'),
            array('code' => 'AS-20260618-003', 'field' => 'Arena Futsal B', 'user' => 'Akbar', 'date' => '18 Juni 2026', 'time' => '16:00 - 17:00', 'status' => 'Selesai', 'statusClass' => 'active', 'total' => 'Rp80.000'),
            array('code' => 'AS-20260618-004', 'field' => 'Arena Badminton 2', 'user' => 'Dewi', 'date' => '18 Juni 2026', 'time' => '18:00 - 19:00', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp60.000'),
            array('code' => 'AS-20260619-005', 'field' => 'Arena Futsal A', 'user' => 'Fajar', 'date' => '19 Juni 2026', 'time' => '17:00 - 18:00', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp80.000'),
        );
    }

    protected function ownerFields()
    {
        return array(
            array('name' => 'Arena Futsal A', 'location' => 'Parepare', 'rating' => '4.8', 'reviews' => '120', 'price' => 'Rp80.000', 'status' => 'Aktif', 'visual' => 'futsal'),
            array('name' => 'Arena Badminton 1', 'location' => 'Parepare', 'rating' => '4.7', 'reviews' => '85', 'price' => 'Rp60.000', 'status' => 'Aktif', 'visual' => 'badminton'),
        );
    }

    protected function latestReviews()
    {
        return array(
            array('name' => 'Rahman', 'time' => '2 hari lalu', 'rating' => 5, 'text' => 'Lapangan bersih dan nyaman, pelayanan ramah, rekomendasi!'),
            array('name' => 'Akbar', 'time' => '3 hari lalu', 'rating' => 4, 'text' => 'Parkiran luas dan lokasi strategis, mantap!'),
            array('name' => 'Dewi', 'time' => '5 hari lalu', 'rating' => 5, 'text' => 'Fasilitas lengkap dan terawat dengan baik.'),
        );
    }

    protected function getAllLapangan()
    {
        return array(
            array('id' => '1', 'name' => 'Arena Futsal A', 'type' => 'Futsal', 'location' => 'Parepare', 'price' => 'Rp80.000', 'status' => 'Aktif', 'cardStatus' => 'Aktif', 'rating' => '4.8', 'reviews' => '120', 'visual' => 'futsal'),
            array('id' => '2', 'name' => 'Arena Badminton 1', 'type' => 'Badminton', 'location' => 'Parepare', 'price' => 'Rp60.000', 'status' => 'Aktif', 'cardStatus' => 'Aktif', 'rating' => '4.7', 'reviews' => '85', 'visual' => 'badminton'),
            array('id' => '3', 'name' => 'Arena Futsal B', 'type' => 'Futsal', 'location' => 'Parepare', 'price' => 'Rp75.000', 'status' => 'Nonaktif', 'cardStatus' => 'Aktif', 'rating' => '4.5', 'reviews' => '60', 'visual' => 'futsal-alt'),
        );
    }

    protected function getOwnerBookings()
    {
        return array(
            array('code' => 'AS-20260617-001', 'field' => 'Arena Futsal A', 'user' => 'Ahmad', 'date' => '17 Juni 2026', 'time' => '19:00 - 20:00', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('code' => 'AS-20260617-002', 'field' => 'Arena Badminton 1', 'user' => 'Rizal', 'date' => '17 Juni 2026', 'time' => '20:00 - 21:00', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp60.000'),
            array('code' => 'AS-20260618-003', 'field' => 'Arena Futsal B', 'user' => 'Akbar', 'date' => '18 Juni 2026', 'time' => '16:00 - 17:00', 'status' => 'Selesai', 'statusClass' => 'active', 'total' => 'Rp80.000'),
            array('code' => 'AS-20260618-004', 'field' => 'Arena Badminton 2', 'user' => 'Dewi', 'date' => '18 Juni 2026', 'time' => '18:00 - 19:00', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp60.000'),
        );
    }

    protected function getSchedule()
    {
        return array(
            array('tenant' => 'Ahmad', 'field' => 'Arena Futsal A', 'date' => '16 Juni 2025', 'time' => '19:00 - 20:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('tenant' => 'Rizal', 'field' => 'Arena Badminton 1', 'date' => '16 Juni 2025', 'time' => '20:00 - 21:00', 'duration' => '1 Jam', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp60.000'),
            array('tenant' => 'Akbar', 'field' => 'Arena Futsal B', 'date' => '17 Juni 2025', 'time' => '16:00 - 17:00', 'duration' => '1 Jam', 'status' => 'Selesai', 'statusClass' => 'active', 'total' => 'Rp75.000'),
            array('tenant' => 'Dewi', 'field' => 'Arena Badminton 1', 'date' => '17 Juni 2025', 'time' => '18:00 - 19:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp60.000'),
            array('tenant' => 'Fajar', 'field' => 'Arena Futsal A', 'date' => '17 Juni 2025', 'time' => '17:00 - 18:00', 'duration' => '1 Jam', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp80.000'),
            array('tenant' => 'Rudi', 'field' => 'Arena Futsal A', 'date' => '18 Juni 2025', 'time' => '20:00 - 21:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('tenant' => 'Sandi', 'field' => 'Arena Badminton 1', 'date' => '18 Juni 2025', 'time' => '19:00 - 20:00', 'duration' => '1 Jam', 'status' => 'Dibatalkan', 'statusClass' => 'danger', 'total' => 'Rp60.000'),
        );
    }

    protected function revenueStats()
    {
        return array(
            array('label' => 'Total Pendapatan', 'value' => 'Rp4.250.000', 'trend' => '18.6%', 'note' => 'dari bulan lalu', 'icon' => 'fa-rupiah-sign', 'accent' => 'green'),
            array('label' => 'Total Transaksi', 'value' => '120', 'trend' => '15.2%', 'note' => 'dari bulan lalu', 'icon' => 'fa-calendar-check', 'accent' => 'cyan'),
            array('label' => 'Rata-rata per Hari', 'value' => 'Rp141.667', 'trend' => '16.6%', 'note' => 'dari bulan lalu', 'icon' => 'fa-chart-column', 'accent' => 'purple'),
            array('label' => 'Potongan Platform (2%)', 'value' => 'Rp85.000', 'trend' => '18.6%', 'note' => 'dari bulan lalu', 'icon' => 'fa-percent', 'accent' => 'gold'),
        );
    }

    protected function dailyRevenueChart()
    {
        return array(
            array('label' => '1 Jun', 'amount' => 'Rp280.000', 'x' => 0, 'y' => 72),
            array('label' => '3 Jun', 'amount' => 'Rp370.000', 'x' => 5, 'y' => 64),
            array('label' => '4 Jun', 'amount' => 'Rp410.000', 'x' => 9, 'y' => 60),
            array('label' => '5 Jun', 'amount' => 'Rp450.000', 'x' => 13, 'y' => 57),
            array('label' => '7 Jun', 'amount' => 'Rp600.000', 'x' => 19, 'y' => 45),
            array('label' => '8 Jun', 'amount' => 'Rp550.000', 'x' => 24, 'y' => 50),
            array('label' => '9 Jun', 'amount' => 'Rp690.000', 'x' => 28, 'y' => 36),
            array('label' => '10 Jun', 'amount' => 'Rp810.000', 'x' => 32, 'y' => 24),
            array('label' => '11 Jun', 'amount' => 'Rp680.000', 'x' => 36, 'y' => 37),
            array('label' => '13 Jun', 'amount' => 'Rp520.000', 'x' => 41, 'y' => 52),
            array('label' => '14 Jun', 'amount' => 'Rp540.000', 'x' => 45, 'y' => 50),
            array('label' => '15 Jun', 'amount' => 'Rp641.200', 'x' => 50, 'y' => 59, 'highlight' => true),
            array('label' => '17 Jun', 'amount' => 'Rp260.000', 'x' => 56, 'y' => 76),
            array('label' => '18 Jun', 'amount' => 'Rp390.000', 'x' => 60, 'y' => 66),
            array('label' => '19 Jun', 'amount' => 'Rp460.000', 'x' => 64, 'y' => 59),
            array('label' => '21 Jun', 'amount' => 'Rp720.000', 'x' => 69, 'y' => 34),
            array('label' => '22 Jun', 'amount' => 'Rp660.000', 'x' => 73, 'y' => 39),
            array('label' => '24 Jun', 'amount' => 'Rp630.000', 'x' => 79, 'y' => 42),
            array('label' => '26 Jun', 'amount' => 'Rp790.000', 'x' => 85, 'y' => 26),
            array('label' => '27 Jun', 'amount' => 'Rp960.000', 'x' => 90, 'y' => 9),
            array('label' => '29 Jun', 'amount' => 'Rp820.000', 'x' => 95, 'y' => 24),
            array('label' => '30 Jun', 'amount' => 'Rp780.000', 'x' => 100, 'y' => 29),
        );
    }

    protected function revenueSummary()
    {
        return array(
            array('label' => 'Pendapatan Kotor', 'value' => 'Rp4.250.000', 'icon' => 'fa-money-bill-trend-up', 'accent' => 'green', 'tone' => 'positive'),
            array('label' => 'Potongan Platform (2%)', 'value' => '-Rp85.000', 'icon' => 'fa-percent', 'accent' => 'red', 'tone' => 'negative'),
            array('label' => 'Pendapatan Bersih', 'value' => 'Rp4.165.000', 'icon' => 'fa-wallet', 'accent' => 'blue', 'tone' => 'positive'),
            array('label' => 'Dibayarkan ke Rekening', 'value' => 'Rp4.165.000', 'icon' => 'fa-building-columns', 'accent' => 'purple', 'tone' => 'positive'),
            array('label' => 'Saldo Tersedia', 'value' => 'Rp0', 'icon' => 'fa-coins', 'accent' => 'gold', 'tone' => 'neutral'),
        );
    }

    protected function revenueTransactions()
    {
        return array(
            array('date' => '16 Jun 2025', 'field' => 'Arena Futsal A', 'tenant' => 'Ahmad', 'method' => 'Transfer Bank', 'methodIcon' => 'fa-building-columns', 'methodClass' => 'bank', 'total' => 'Rp80.000', 'fee' => 'Rp1.600', 'net' => 'Rp78.400', 'status' => 'Dibayar'),
            array('date' => '16 Jun 2025', 'field' => 'Arena Badminton 1', 'tenant' => 'Rizal', 'method' => 'E-Wallet (OVO)', 'methodIcon' => 'fa-wallet', 'methodClass' => 'ovo', 'total' => 'Rp60.000', 'fee' => 'Rp1.200', 'net' => 'Rp58.800', 'status' => 'Dibayar'),
            array('date' => '17 Jun 2025', 'field' => 'Arena Futsal B', 'tenant' => 'Akbar', 'method' => 'Transfer Bank', 'methodIcon' => 'fa-building-columns', 'methodClass' => 'bank', 'total' => 'Rp75.000', 'fee' => 'Rp1.500', 'net' => 'Rp73.500', 'status' => 'Dibayar'),
            array('date' => '17 Jun 2025', 'field' => 'Arena Badminton 1', 'tenant' => 'Dewi', 'method' => 'E-Wallet (Dana)', 'methodIcon' => 'fa-wallet', 'methodClass' => 'dana', 'total' => 'Rp60.000', 'fee' => 'Rp1.200', 'net' => 'Rp58.800', 'status' => 'Dibayar'),
            array('date' => '17 Jun 2025', 'field' => 'Arena Futsal A', 'tenant' => 'Fajar', 'method' => 'Transfer Bank', 'methodIcon' => 'fa-building-columns', 'methodClass' => 'bank', 'total' => 'Rp80.000', 'fee' => 'Rp1.600', 'net' => 'Rp78.400', 'status' => 'Dibayar'),
        );
    }

    protected function ownerReviewStats()
    {
        return array(
            array('label' => 'Rating Rata-rata', 'value' => '4.8 / 5', 'note' => '(156 ulasan)', 'icon' => 'fa-star', 'accent' => 'lime', 'rating' => 4.8),
            array('label' => 'Total Ulasan', 'value' => '156', 'trend' => '12.3%', 'note' => 'dari bulan lalu', 'icon' => 'fa-comment-dots', 'accent' => 'blue'),
            array('label' => 'Ulasan Positif', 'value' => '142', 'trend' => '91.0%', 'note' => 'dari total ulasan', 'icon' => 'fa-thumbs-up', 'accent' => 'purple'),
            array('label' => 'Ulasan Negatif', 'value' => '14', 'trend' => '9.0%', 'note' => 'dari total ulasan', 'icon' => 'fa-thumbs-down', 'accent' => 'orange'),
        );
    }

    protected function ownerRatingDistribution()
    {
        return array(
            array('stars' => 5, 'count' => 98, 'percent' => 62.8),
            array('stars' => 4, 'count' => 36, 'percent' => 23.1),
            array('stars' => 3, 'count' => 14, 'percent' => 9.0),
            array('stars' => 2, 'count' => 5, 'percent' => 3.2),
            array('stars' => 1, 'count' => 3, 'percent' => 1.9),
        );
    }

    protected function ownerFieldRatings()
    {
        return array(
            array('name' => 'Arena Futsal A', 'rating' => '4.8', 'reviews' => '85', 'percent' => 94, 'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=360&auto=format&fit=crop'),
            array('name' => 'Arena Badminton 1', 'rating' => '4.7', 'reviews' => '45', 'percent' => 88, 'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=360&auto=format&fit=crop'),
            array('name' => 'Arena Futsal B', 'rating' => '4.6', 'reviews' => '26', 'percent' => 86, 'image' => 'https://images.unsplash.com/photo-1551958219-acbc608c6377?q=80&w=360&auto=format&fit=crop'),
        );
    }

    protected function ownerReviewRows()
    {
        return array(
            array('name' => 'Ahmad Rizki', 'username' => '@ahmadrzki', 'field' => 'Arena Futsal A', 'rating' => 5.0, 'review' => 'Lapangan bersih dan nyaman, pencahayaan bagus.', 'date' => '16 Juni 2025', 'time' => '19:45', 'avatar' => 'https://ui-avatars.com/api/?name=Ahmad+Rizki&background=245b84&color=ffffff'),
            array('name' => 'Dewi Sartika', 'username' => '@dewii.srt', 'field' => 'Arena Badminton 1', 'rating' => 4.5, 'review' => 'Net dan lantai bagus, hanya saja AC kurang dingin.', 'date' => '16 Juni 2025', 'time' => '17:20', 'avatar' => 'https://ui-avatars.com/api/?name=Dewi+Sartika&background=245b84&color=ffffff'),
            array('name' => 'Fajar Maulana', 'username' => '@fajarmaulana', 'field' => 'Arena Futsal B', 'rating' => 5.0, 'review' => 'Mantap! lapangan luas dan parkir nyaman.', 'date' => '15 Juni 2025', 'time' => '21:10', 'avatar' => 'https://ui-avatars.com/api/?name=Fajar+Maulana&background=245b84&color=ffffff'),
            array('name' => 'Rizal Aditya', 'username' => '@rizaladtya', 'field' => 'Arena Futsal A', 'rating' => 3.5, 'review' => 'Secara keseluruhan bagus, mungkin kamar mandi perlu ditingkatkan.', 'date' => '15 Juni 2025', 'time' => '16:05', 'avatar' => 'https://ui-avatars.com/api/?name=Rizal+Aditya&background=245b84&color=ffffff'),
            array('name' => 'Nurfadilah', 'username' => '@nurfadilah_', 'field' => 'Arena Badminton 1', 'rating' => 5.0, 'review' => 'Pelayanan ramah, lapangan top!', 'date' => '15 Juni 2025', 'time' => '15:30', 'avatar' => 'https://ui-avatars.com/api/?name=Nurfadilah&background=d7d3cc&color=394150'),
        );
    }

    protected function ownerProfile($name)
    {
        $displayName = trim((string) $name) !== '' ? $name : 'Rahmat';

        return array(
            'name' => $displayName,
            'email' => 'rahmat@email.com',
            'phone' => '0812-3456-7890',
            'location' => 'Parepare, Sulawesi Selatan',
            'bio' => 'Pemilik beberapa lapangan olahraga di Parepare. Berkomitmen memberikan fasilitas terbaik untuk pelanggan.',
            'joined' => '12 Maret 2024',
            'totalFields' => '3 Lapangan',
            'lastLogin' => '16 Juni 2025, 21:15',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=320&auto=format&fit=crop',
        );
    }

    protected function profileManagedFields()
    {
        return array(
            array('name' => 'Arena Futsal A', 'type' => 'Futsal', 'location' => 'Parepare', 'price' => 'Rp80.000', 'status' => 'Aktif', 'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=360&auto=format&fit=crop'),
            array('name' => 'Arena Badminton 1', 'type' => 'Badminton', 'location' => 'Parepare', 'price' => 'Rp60.000', 'status' => 'Aktif', 'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=360&auto=format&fit=crop'),
            array('name' => 'Arena Futsal B', 'type' => 'Futsal', 'location' => 'Parepare', 'price' => 'Rp75.000', 'status' => 'Aktif', 'image' => 'https://images.unsplash.com/photo-1551958219-acbc608c6377?q=80&w=360&auto=format&fit=crop'),
        );
    }
}
