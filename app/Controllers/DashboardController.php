<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller
{
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

    protected function renderDashboard($view, $activeMenu, $title, $heading, $subheading)
    {
        return $this->view($view, array(
            'title' => $title,
            'activeMenu' => $activeMenu,
            'pageHeading' => $heading,
            'pageSubheading' => $subheading,
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'stats' => $this->stats(),
            'venues' => $this->venues(),
            'favorites' => $this->favorites(),
            'nextBooking' => $this->nextBooking(),
            'bookings' => $this->bookings(),
        ), 'layouts/dashboard');
    }

    protected function bookings()
    {
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
                'status' => 'Mendatang',
                'statusClass' => 'upcoming',
                'button' => 'Ubah Booking',
                'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=500&auto=format&fit=crop',
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
                'status' => 'Mendatang',
                'statusClass' => 'upcoming',
                'button' => 'Ubah Booking',
                'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=500&auto=format&fit=crop',
            ),
            array(
                'type' => 'Mini Soccer',
                'venue' => 'Mini Soccer Victory',
                'location' => 'Jl. Jend. Sudirman, Parepare',
                'date' => '25 Mei 2024',
                'time' => '17:00 - 18:00',
                'duration' => '1 Jam',
                'code' => 'AS-250524-00045',
                'price' => 'Rp100.000',
                'status' => 'Menunggu Pembayaran',
                'statusClass' => 'pending',
                'button' => 'Bayar Sekarang',
                'image' => 'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?q=80&w=500&auto=format&fit=crop',
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

    protected function stats()
    {
        return array(
            array('label' => 'Booking Aktif', 'value' => '12', 'icon' => '&#128197;', 'accent' => 'green'),
            array('label' => 'Selesai', 'value' => '28', 'icon' => '&#10003;', 'accent' => 'blue'),
            array('label' => 'Favorit', 'value' => '5', 'icon' => '&#9825;', 'accent' => 'purple'),
            array('label' => 'Rating Anda', 'value' => '4.8', 'icon' => '&#9734;', 'accent' => 'orange'),
        );
    }

    protected function venues()
    {
        return array(
            array(
                'name' => 'Arena Futsal Parepare',
                'location' => 'Jl. Mattirotasi No. 12, Parepare',
                'rating' => '4.8',
                'reviews' => '120 ulasan',
                'price' => 'Rp80.000',
                'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=900&auto=format&fit=crop',
            ),
            array(
                'name' => 'Lapangan Badminton Center',
                'location' => 'Jl. Bau Massepe No. 45, Parepare',
                'rating' => '4.6',
                'reviews' => '85 ulasan',
                'price' => 'Rp60.000',
                'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=900&auto=format&fit=crop',
            ),
            array(
                'name' => 'Mini Soccer Victory',
                'location' => 'Jl. Jend. Sudirman, Parepare',
                'rating' => '4.7',
                'reviews' => '98 ulasan',
                'price' => 'Rp100.000',
                'image' => 'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?q=80&w=900&auto=format&fit=crop',
            ),
        );
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

    protected function nextBooking()
    {
        return array(
            'venue' => 'Arena Futsal Parepare',
            'date' => '10 Juni 2026',
            'time' => '10:00 - 11:00',
            'duration' => '1 Jam',
            'status' => 'Akan Datang',
            'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=500&auto=format&fit=crop',
        );
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

        return $this->view('dashboard/settings', array(
            'title' => 'Pengaturan Akun | Arena Sport',
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'userEmail' => isset($_SESSION['email_user']) ? $_SESSION['email_user'] : 'user@arenasport.id',
            'userPhone' => isset($_SESSION['telepon_user']) ? $_SESSION['telepon_user'] : '081234567890',
            'userCity' => 'Parepare',
            'userRole' => isset($_SESSION['role_user']) ? $_SESSION['role_user'] : 'User',
        ), 'layouts/dashboard');
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

        if ($nama === '' || $email === '') {
            $errorMessage = 'Nama dan email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = 'Email tidak valid.';
        } else {
            $connection = Database::connection();
            $statement = mysqli_prepare($connection, 'UPDATE user SET Nama = ?, Email = ?, Nomor_telepon = ? WHERE ID_User = ?');

            if ($statement) {
                mysqli_stmt_bind_param($statement, 'ssss', $nama, $email, $telepon, $userId);

                if (mysqli_stmt_execute($statement)) {
                    $_SESSION['nama_user'] = $nama;
                    $_SESSION['email_user'] = $email;
                    $_SESSION['telepon_user'] = $telepon;
                    $message = 'Perubahan pengaturan berhasil disimpan.';
                } else {
                    $errorMessage = 'Tidak dapat menyimpan perubahan. Silakan coba lagi.';
                }
            } else {
                $errorMessage = 'Terjadi kesalahan koneksi database.';
            }
        }

        return $this->view('dashboard/settings', array(
            'title' => 'Pengaturan Akun | Arena Sport',
            'message' => $message,
            'errorMessage' => $errorMessage,
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'userEmail' => isset($_SESSION['email_user']) ? $_SESSION['email_user'] : 'user@arenasport.id',
            'userPhone' => isset($_SESSION['telepon_user']) ? $_SESSION['telepon_user'] : '081234567890',
            'userCity' => $kota,
            'userRole' => isset($_SESSION['role_user']) ? $_SESSION['role_user'] : 'User',
        ), 'layouts/dashboard');
    }
}
