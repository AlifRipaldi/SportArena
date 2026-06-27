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
$router->get('/dashboard/lapangan/{id}', [DashboardController::class, 'fieldDetail']);
$router->post('/dashboard/booking/tambah', [DashboardController::class, 'storeBooking']);
$router->get('/dashboard/booking', [DashboardController::class, 'booking']);
$router->post('/dashboard/booking/update', [DashboardController::class, 'updateBooking']);
$router->post('/dashboard/booking/bayar', [DashboardController::class, 'payBooking']);
$router->get('/dashboard/riwayat', [DashboardController::class, 'riwayat']);
$router->get('/dashboard/favorit', [DashboardController::class, 'favorit']);
$router->post('/dashboard/favorit/toggle', [DashboardController::class, 'toggleFavorite']);
$router->post('/dashboard/favorit/hapus-semua', [DashboardController::class, 'clearFavorites']);
$router->get('/dashboard/ulasan', [DashboardController::class, 'ulasan']);
$router->post('/dashboard/ulasan/tambah', [DashboardController::class, 'storeReview']);
$router->get('/dashboard/profil', [DashboardController::class, 'profil']);
$router->get('/settings', [DashboardController::class, 'settings']);
$router->post('/settings', [DashboardController::class, 'updateSettings']);
$router->post('/settings/theme', [DashboardController::class, 'updateTheme']);
$router->get('/settings/password', [DashboardController::class, 'changePassword']);
$router->post('/settings/password', [DashboardController::class, 'updatePassword']);
$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/dashboard', [AdminController::class, 'index']);
$router->get('/admin/search', [AdminController::class, 'search']);
$router->get('/pemilik', [PemilikController::class, 'index']);
$router->get('/pemilik/dashboard', [PemilikController::class, 'index']);
$router->get('/pemilik/lapangan', [PemilikController::class, 'lapangan']);
$router->post('/pemilik/lapangan/tambah', [PemilikController::class, 'storeLapangan']);
$router->post('/pemilik/lapangan/update', [PemilikController::class, 'updateLapangan']);
$router->post('/pemilik/lapangan/hapus', [PemilikController::class, 'deleteLapangan']);
$router->get('/pemilik/booking', [PemilikController::class, 'booking']);
$router->get('/pemilik/jadwal', [PemilikController::class, 'jadwal']);
$router->post('/pemilik/jadwal/tambah', [PemilikController::class, 'storeJadwal']);
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
$router->post('/admin/booking/tambah', [AdminController::class, 'storeBooking']);
$router->post('/admin/booking/update', [AdminController::class, 'updateBooking']);
$router->post('/admin/booking/hapus', [AdminController::class, 'deleteBooking']);
$router->get('/admin/lapangan', [AdminController::class, 'lapangan']);
$router->post('/admin/lapangan/tambah', [AdminController::class, 'storeLapangan']);
$router->post('/admin/lapangan/update', [AdminController::class, 'updateLapangan']);
$router->post('/admin/lapangan/hapus', [AdminController::class, 'deleteLapangan']);
$router->get('/admin/jadwal', [AdminController::class, 'jadwal']);
$router->post('/admin/jadwal/tambah', [AdminController::class, 'storeJadwal']);
$router->post('/admin/jadwal/update', [AdminController::class, 'updateJadwal']);
$router->post('/admin/jadwal/hapus', [AdminController::class, 'deleteJadwal']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->post('/admin/users/tambah', [AdminController::class, 'storeUser']);
$router->post('/admin/users/update', [AdminController::class, 'updateUser']);
$router->post('/admin/users/hapus', [AdminController::class, 'deleteUser']);
$router->get('/admin/pemilik-lapangan', [AdminController::class, 'pemilikLapangan']);
$router->get('/admin/ulasan', [AdminController::class, 'ulasan']);
$router->post('/admin/ulasan/tanggapi', [AdminController::class, 'replyReview']);
$router->post('/admin/ulasan/hapus', [AdminController::class, 'deleteReview']);
$router->get('/admin/transaksi', [AdminController::class, 'transaksi']);
$router->post('/admin/transaksi/update', [AdminController::class, 'updateTransaction']);
$router->get('/admin/export/{type}', [AdminController::class, 'export']);
$router->get('/admin/laporan', [AdminController::class, 'laporan']);
$router->get('/admin/pengaturan', [AdminController::class, 'pengaturan']);
$router->post('/admin/pengaturan/profil', [AdminController::class, 'updateProfile']);
$router->post('/admin/pengaturan/password', [AdminController::class, 'updatePassword']);
$router->post('/admin/pengaturan/metode', [AdminController::class, 'updatePaymentMethods']);
$router->post('/admin/pengaturan/preferensi', [AdminController::class, 'updatePreferences']);
$router->post('/admin/pengaturan/rekening/tambah', [AdminController::class, 'storeBankAccount']);
$router->post('/admin/pengaturan/rekening/update', [AdminController::class, 'updateBankAccount']);
$router->post('/admin/pengaturan/rekening/hapus', [AdminController::class, 'deleteBankAccount']);
