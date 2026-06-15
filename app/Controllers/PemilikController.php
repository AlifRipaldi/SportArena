<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class PemilikController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new Database();
    }

    public function index()
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

        $userName = isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : (isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pemilik Arena');

        return $this->view('Owner/index', array(
            'title' => 'Dashboard Pemilik | Arena Sport',
            'activeMenu' => 'dashboard',
            'userName' => $userName,
            'userRole' => $role,
            'summaryCards' => $this->summaryCards(),
            'weeklyRevenue' => $this->weeklyRevenue(),
            'fieldStatus' => $this->fieldStatus(),
            'recentBookings' => $this->recentBookings(),
            'fieldPerformance' => $this->fieldPerformance(),
        ), 'layouts/admin');
    }

    public function lapangan()
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

        $userName = isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pemilik Arena';

        return $this->view('Owner/lapangan', array(
            'title' => 'Kelola Lapangan | Arena Sport',
            'activeMenu' => 'lapangan',
            'userName' => $userName,
            'lapangan' => $this->getAllLapangan(),
        ), 'layouts/admin');
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

        $role = isset($_SESSION['role_user']) ? $_SESSION['role_user'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');

        if (!$this->isOwnerRole($role)) {
            header('Location: ' . app_url('dashboard'));
            exit;
        }

        $userName = isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pemilik Arena';

        return $this->view('Owner/booking', array(
            'title' => 'Manajemen Booking | Arena Sport',
            'activeMenu' => 'booking',
            'userName' => $userName,
            'bookings' => $this->getOwnerBookings(),
        ), 'layouts/admin');
    }

    public function jadwal()
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

        $userName = isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pemilik Arena';

        return $this->view('Owner/jadwal', array(
            'title' => 'Manajemen Jadwal | Arena Sport',
            'activeMenu' => 'jadwal',
            'userName' => $userName,
            'schedule' => $this->getSchedule(),
        ), 'layouts/admin');
    }

    protected function isOwnerRole($role)
    {
        return in_array(strtolower(trim((string) $role)), array('owner', 'pemilik', 'pemilik lapangan'), true);
    }

    protected function summaryCards()
    {
        return array(
            array(
                'label' => 'Total Lapangan',
                'value' => '5',
                'trend' => '1',
                'note' => 'dari bulan lalu',
                'icon' => 'fa-volleyball',
                'accent' => 'lime',
            ),
            array(
                'label' => 'Booking Bulan Ini',
                'value' => '156',
                'trend' => '8.3%',
                'note' => 'dari bulan lalu',
                'icon' => 'fa-calendar-days',
                'accent' => 'blue',
            ),
            array(
                'label' => 'Pendapatan Bulan Ini',
                'value' => 'Rp12.450.000',
                'trend' => '15.7%',
                'note' => 'dari bulan lalu',
                'icon' => 'fa-rupiah-sign',
                'accent' => 'green',
            ),
            array(
                'label' => 'Rating Rata-rata',
                'value' => '4.7 / 5',
                'trend' => '0.1',
                'note' => 'dari bulan lalu',
                'icon' => 'fa-star',
                'accent' => 'gold',
            ),
        );
    }

    protected function weeklyRevenue()
    {
        return array(
            array('day' => 'Senin', 'revenue' => 'Rp2.4jt', 'bookings' => 24),
            array('day' => 'Selasa', 'revenue' => 'Rp2.1jt', 'bookings' => 21),
            array('day' => 'Rabu', 'revenue' => 'Rp2.8jt', 'bookings' => 28),
            array('day' => 'Kamis', 'revenue' => 'Rp2.6jt', 'bookings' => 26),
            array('day' => 'Jumat', 'revenue' => 'Rp3.2jt', 'bookings' => 32),
            array('day' => 'Sabtu', 'revenue' => 'Rp3.5jt', 'bookings' => 35),
            array('day' => 'Minggu', 'revenue' => 'Rp3.1jt', 'bookings' => 31),
        );
    }

    protected function fieldStatus()
    {
        return array(
            array('name' => 'Futsal A', 'status' => 'Aktif', 'bookingToday' => 8, 'rating' => 4.8),
            array('name' => 'Badminton B', 'status' => 'Aktif', 'bookingToday' => 6, 'rating' => 4.6),
            array('name' => 'Mini Soccer', 'status' => 'Aktif', 'bookingToday' => 5, 'rating' => 4.7),
            array('name' => 'Futsal B', 'status' => 'Maintenance', 'bookingToday' => 0, 'rating' => 4.5),
            array('name' => 'Basket A', 'status' => 'Aktif', 'bookingToday' => 4, 'rating' => 4.4),
        );
    }

    protected function recentBookings()
    {
        return array(
            array('code' => 'AS-20240531-001', 'field' => 'Futsal A', 'user' => 'Ahmad Fauzi', 'date' => '31 Mei 2024', 'time' => '10:00 - 11:00', 'status' => 'Selesai', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('code' => 'AS-20240531-002', 'field' => 'Badminton B', 'user' => 'Rizal Maulana', 'date' => '31 Mei 2024', 'time' => '14:00 - 15:00', 'status' => 'Aktif', 'statusClass' => 'active', 'total' => 'Rp60.000'),
            array('code' => 'AS-20240531-003', 'field' => 'Mini Soccer', 'user' => 'Dinda Putri', 'date' => '31 Mei 2024', 'time' => '17:00 - 18:00', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp100.000'),
        );
    }

    protected function fieldPerformance()
    {
        return array(
            array('name' => 'Futsal A', 'bookings' => '210', 'percent' => 35),
            array('name' => 'Badminton B', 'bookings' => '140', 'percent' => 23),
            array('name' => 'Mini Soccer', 'bookings' => '155', 'percent' => 26),
            array('name' => 'Basket A', 'bookings' => '95', 'percent' => 16),
        );
    }

    protected function getAllLapangan()
    {
        return array(
            array('id' => '1', 'name' => 'Futsal A', 'type' => 'Futsal', 'location' => 'Area 1', 'price' => 'Rp80.000', 'status' => 'Aktif'),
            array('id' => '2', 'name' => 'Badminton B', 'type' => 'Badminton', 'location' => 'Area 2', 'price' => 'Rp60.000', 'status' => 'Aktif'),
            array('id' => '3', 'name' => 'Mini Soccer', 'type' => 'Mini Soccer', 'location' => 'Area 3', 'price' => 'Rp100.000', 'status' => 'Aktif'),
            array('id' => '4', 'name' => 'Basket A', 'type' => 'Basketball', 'location' => 'Area 4', 'price' => 'Rp70.000', 'status' => 'Maintenance'),
        );
    }

    protected function getOwnerBookings()
    {
        return array(
            array('code' => 'AS-20240531-001', 'field' => 'Futsal A', 'user' => 'Ahmad Fauzi', 'date' => '31 Mei 2024', 'time' => '10:00 - 11:00', 'status' => 'Selesai', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('code' => 'AS-20240531-002', 'field' => 'Badminton B', 'user' => 'Rizal Maulana', 'date' => '31 Mei 2024', 'time' => '14:00 - 15:00', 'status' => 'Aktif', 'statusClass' => 'active', 'total' => 'Rp60.000'),
            array('code' => 'AS-20240531-003', 'field' => 'Mini Soccer', 'user' => 'Dinda Putri', 'date' => '31 Mei 2024', 'time' => '17:00 - 18:00', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp100.000'),
            array('code' => 'AS-20240531-004', 'field' => 'Futsal B', 'user' => 'Budi Santoso', 'date' => '31 Mei 2024', 'time' => '19:00 - 20:00', 'status' => 'Dibatalkan', 'statusClass' => 'danger', 'total' => 'Rp80.000'),
        );
    }

    protected function getSchedule()
    {
        return array(
            array('lapangan' => 'Futsal A', 'date' => '01 Juni 2024', 'jam_mulai' => '10:00', 'jam_selesai' => '22:00', 'status' => 'Available'),
            array('lapangan' => 'Badminton B', 'date' => '01 Juni 2024', 'jam_mulai' => '08:00', 'jam_selesai' => '20:00', 'status' => 'Available'),
            array('lapangan' => 'Mini Soccer', 'date' => '01 Juni 2024', 'jam_mulai' => '10:00', 'jam_selesai' => '23:00', 'status' => 'Available'),
        );
    }
}
