<?php

namespace App\Controllers;

use App\Core\Controller;

class AdminController extends Controller
{
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

        if (!$this->isAdminRole($role)) {
            header('Location: ' . app_url('dashboard'));
            exit;
        }

        $userName = isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : (isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin Arena');

        return $this->view('Admin/index', array(
            'title' => 'Dashboard Admin | Arena Sport',
            'activeMenu' => 'dashboard',
            'userName' => $userName,
            'userRole' => $role,
            'summaryCards' => $this->summaryCards(),
            'monthlyRevenue' => $this->monthlyRevenue(),
            'bookingStatus' => $this->bookingStatus(),
            'recentBookings' => $this->recentBookings(),
            'popularFields' => $this->popularFields(),
            'bottomMetrics' => $this->bottomMetrics(),
        ), 'layouts/admin');
    }

    protected function isAdminRole($role)
    {
        return in_array(strtolower(trim((string) $role)), array('admin', 'administrator', 'superadmin'), true);
    }

    protected function summaryCards()
    {
        return array(
            array(
                'label' => 'Total User',
                'value' => '1.245',
                'trend' => '12.5%',
                'note' => 'dari bulan lalu',
                'icon' => 'fa-users',
                'accent' => 'lime',
            ),
            array(
                'label' => 'Booking Hari Ini',
                'value' => '156',
                'trend' => '8.3%',
                'note' => 'dari kemarin',
                'icon' => 'fa-calendar-days',
                'accent' => 'blue',
            ),
            array(
                'label' => 'Total Pendapatan',
                'value' => 'Rp12.450.000',
                'trend' => '15.7%',
                'note' => 'dari kemarin',
                'icon' => 'fa-rupiah-sign',
                'accent' => 'green',
            ),
            array(
                'label' => 'Lapangan Aktif',
                'value' => '24',
                'trend' => '2',
                'note' => 'dari bulan lalu',
                'icon' => 'fa-volleyball',
                'accent' => 'gold',
            ),
        );
    }

    protected function monthlyRevenue()
    {
        return array(
            array('month' => 'Jan', 'amount' => 'Rp3.5jt', 'x' => 0, 'y' => 81),
            array('month' => 'Feb', 'amount' => 'Rp5.9jt', 'x' => 9.1, 'y' => 69),
            array('month' => 'Mar', 'amount' => 'Rp8.8jt', 'x' => 18.2, 'y' => 55),
            array('month' => 'Apr', 'amount' => 'Rp10.7jt', 'x' => 27.3, 'y' => 46),
            array('month' => 'Mei', 'amount' => 'Rp13.7jt', 'x' => 36.4, 'y' => 34),
            array('month' => 'Jun', 'amount' => 'Rp10.2jt', 'x' => 45.5, 'y' => 48),
            array('month' => 'Jul', 'amount' => 'Rp13.2jt', 'x' => 54.6, 'y' => 36),
            array('month' => 'Agu', 'amount' => 'Rp17.8jt', 'x' => 63.7, 'y' => 18),
            array('month' => 'Sep', 'amount' => 'Rp15.1jt', 'x' => 72.8, 'y' => 29),
            array('month' => 'Okt', 'amount' => 'Rp19.6jt', 'x' => 81.9, 'y' => 11),
            array('month' => 'Nov', 'amount' => 'Rp21.1jt', 'x' => 91, 'y' => 6),
            array('month' => 'Des', 'amount' => 'Rp14.2jt', 'x' => 100, 'y' => 33),
        );
    }

    protected function bookingStatus()
    {
        return array(
            array('label' => 'Selesai', 'value' => '45%', 'count' => '234', 'color' => 'lime'),
            array('label' => 'Aktif', 'value' => '30%', 'count' => '156', 'color' => 'blue'),
            array('label' => 'Pending', 'value' => '15%', 'count' => '78', 'color' => 'gold'),
            array('label' => 'Dibatalkan', 'value' => '10%', 'count' => '52', 'color' => 'red'),
        );
    }

    protected function recentBookings()
    {
        return array(
            array('code' => 'AS-20240531-001', 'user' => 'Ahmad Fauzi', 'field' => 'Futsal A', 'date' => '31 Mei 2024', 'time' => '10:00 - 11:00', 'status' => 'Selesai', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('code' => 'AS-20240531-002', 'user' => 'Rizal Maulana', 'field' => 'Badminton B', 'date' => '31 Mei 2024', 'time' => '14:00 - 15:00', 'status' => 'Aktif', 'statusClass' => 'active', 'total' => 'Rp60.000'),
            array('code' => 'AS-20240531-003', 'user' => 'Dinda Putri', 'field' => 'Mini Soccer', 'date' => '31 Mei 2024', 'time' => '17:00 - 18:00', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp100.000'),
            array('code' => 'AS-20240531-004', 'user' => 'Budi Santoso', 'field' => 'Basket A', 'date' => '31 Mei 2024', 'time' => '19:00 - 20:00', 'status' => 'Dibatalkan', 'statusClass' => 'danger', 'total' => 'Rp70.000'),
            array('code' => 'AS-20240531-005', 'user' => 'Siti Aminah', 'field' => 'Futsal B', 'date' => '31 Mei 2024', 'time' => '20:00 - 21:00', 'status' => 'Selesai', 'statusClass' => 'success', 'total' => 'Rp80.000'),
        );
    }

    protected function popularFields()
    {
        return array(
            array('name' => 'Futsal A', 'booking' => '210', 'percent' => 28),
            array('name' => 'Badminton B', 'booking' => '180', 'percent' => 24),
            array('name' => 'Mini Soccer', 'booking' => '155', 'percent' => 21),
            array('name' => 'Basket A', 'booking' => '120', 'percent' => 16),
            array('name' => 'Futsal B', 'booking' => '85', 'percent' => 11),
        );
    }

    protected function bottomMetrics()
    {
        return array(
            array('label' => 'Pendapatan Hari Ini', 'value' => 'Rp2.450.000', 'trend' => '18.2%', 'note' => 'dari kemarin', 'icon' => 'fa-rupiah-sign', 'accent' => 'green'),
            array('label' => 'Pendapatan Bulan Ini', 'value' => 'Rp124.500.000', 'trend' => '22.5%', 'note' => 'dari bulan lalu', 'icon' => 'fa-volleyball', 'accent' => 'indigo'),
            array('label' => 'Total Booking Bulan Ini', 'value' => '2.450', 'trend' => '16.3%', 'note' => 'dari bulan lalu', 'icon' => 'fa-calendar-days', 'accent' => 'gold'),
            array('label' => 'Rata-rata Rating', 'value' => '4.8 / 5', 'trend' => '0.2', 'note' => 'dari bulan lalu', 'icon' => 'fa-calendar-check', 'accent' => 'purple'),
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

        $role = isset($_SESSION['role_user']) ? $_SESSION['role_user'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');

        if (!$this->isAdminRole($role)) {
            header('Location: ' . app_url('dashboard'));
            exit;
        }

        $userName = isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : (isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin Arena');

        return $this->view('Admin/booking', array(
            'title' => 'Manajemen Booking | Arena Sport',
            'activeMenu' => 'booking',
            'userName' => $userName,
            'recentBookings' => $this->recentBookings(),
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

        if (!$this->isAdminRole($role)) {
            header('Location: ' . app_url('dashboard'));
            exit;
        }

        $userName = isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : (isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin Arena');

        return $this->view('Admin/lapangan', array(
            'title' => 'Manajemen Lapangan | Arena Sport',
            'activeMenu' => 'lapangan',
            'userName' => $userName,
        ), 'layouts/admin');
    }

    public function users()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        $role = isset($_SESSION['role_user']) ? $_SESSION['role_user'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');

        if (!$this->isAdminRole($role)) {
            header('Location: ' . app_url('dashboard'));
            exit;
        }

        $userName = isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : (isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin Arena');

        return $this->view('Admin/users', array(
            'title' => 'Manajemen User | Arena Sport',
            'activeMenu' => 'users',
            'userName' => $userName,
        ), 'layouts/admin');
    }
}
