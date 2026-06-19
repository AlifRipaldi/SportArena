<?php

use App\Controllers\HomeController;
use App\Controllers\DashboardController;
use App\Controllers\AdminController;
use App\Controllers\PemilikController;

$router = $app->router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/index.php', [HomeController::class, 'index']);
$router->get('/lapangan', [HomeController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'dashboard']);
$router->get('/dashboard/lapangan', [DashboardController::class, 'search']);
$router->get('/dashboard/booking', [DashboardController::class, 'booking']);
$router->get('/dashboard/riwayat', [DashboardController::class, 'riwayat']);
$router->get('/dashboard/favorit', [DashboardController::class, 'favorit']);
$router->get('/dashboard/ulasan', [DashboardController::class, 'ulasan']);
$router->get('/dashboard/profil', [DashboardController::class, 'profil']);
$router->get('/settings', [DashboardController::class, 'settings']);
$router->post('/settings', [DashboardController::class, 'updateSettings']);
$router->post('/settings/theme', [DashboardController::class, 'updateTheme']);
$router->get('/settings/password', [DashboardController::class, 'changePassword']);
$router->post('/settings/password', [DashboardController::class, 'updatePassword']);
$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/dashboard', [AdminController::class, 'index']);
$router->get('/pemilik', [PemilikController::class, 'index']);
$router->get('/pemilik/dashboard', [PemilikController::class, 'index']);
$router->get('/pemilik/lapangan', [PemilikController::class, 'lapangan']);
$router->post('/pemilik/lapangan/tambah', [PemilikController::class, 'storeLapangan']);
$router->post('/pemilik/lapangan/update', [PemilikController::class, 'updateLapangan']);
$router->post('/pemilik/lapangan/hapus', [PemilikController::class, 'deleteLapangan']);
$router->get('/pemilik/booking', [PemilikController::class, 'booking']);
$router->get('/pemilik/jadwal', [PemilikController::class, 'jadwal']);
$router->get('/pemilik/pendapatan', [PemilikController::class, 'pendapatan']);
$router->get('/pemilik/pendapatan/download', [PemilikController::class, 'downloadPendapatan']);
$router->get('/pemilik/transaksi', [PemilikController::class, 'transaksi']);
$router->get('/pemilik/transaksi/export', [PemilikController::class, 'downloadTransaksi']);
$router->get('/pemilik/ulasan', [PemilikController::class, 'ulasan']);
$router->get('/pemilik/profil', [PemilikController::class, 'profil']);
$router->post('/pemilik/profil', [PemilikController::class, 'updateProfil']);
$router->get('/pemilik/pengaturan', [PemilikController::class, 'pengaturan']);

$router->get('/login', function () {
    header('Location: ' . app_url('public/login.php'));
    exit;
});

$router->get('/register', function () {
    header('Location: ' . app_url('public/register.php'));
    exit;
});

$router->get('/booking/{id}', function ($id) {
    header('Location: ' . app_url('public/booking.php?id=' . rawurlencode($id)));
    exit;
});

$router->get('/admin/booking', [AdminController::class, 'booking']);
$router->get('/admin/lapangan', [AdminController::class, 'lapangan']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->get('/admin/pemilik-lapangan', [AdminController::class, 'pemilikLapangan']);
$router->get('/admin/ulasan', [AdminController::class, 'ulasan']);
$router->get('/admin/transaksi', [AdminController::class, 'transaksi']);
$router->get('/admin/laporan', [AdminController::class, 'laporan']);
$router->get('/admin/pengaturan', [AdminController::class, 'pengaturan']);
