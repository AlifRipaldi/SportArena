<?php

namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller
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

        return $this->view('dashboard/index', array(
            'title' => 'Dashboard | Arena Sport',
            'userName' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna Arena',
            'stats' => $this->stats(),
            'venues' => $this->venues(),
            'nextBooking' => $this->nextBooking(),
        ), 'layouts/dashboard');
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
}
