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
            'title' => 'Manajemen Jadwal | Arena Sport',
            'activeMenu' => 'jadwal',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'schedule' => $this->getSchedule(),
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
            array('lapangan' => 'Arena Futsal A', 'date' => '17 Juni 2026', 'jam_mulai' => '10:00', 'jam_selesai' => '22:00', 'status' => 'Available'),
            array('lapangan' => 'Arena Badminton 1', 'date' => '17 Juni 2026', 'jam_mulai' => '08:00', 'jam_selesai' => '20:00', 'status' => 'Available'),
            array('lapangan' => 'Arena Futsal B', 'date' => '18 Juni 2026', 'jam_mulai' => '10:00', 'jam_selesai' => '23:00', 'status' => 'Available'),
        );
    }
}
