<?php

use App\Controllers\HomeController;
use App\Controllers\DashboardController;

$router = $app->router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/index.php', [HomeController::class, 'index']);
$router->get('/lapangan', [HomeController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'dashboard']);
$router->get('/dashboard/lapangan', [DashboardController::class, 'search']);
$router->get('/dashboard/booking', [DashboardController::class, 'booking']);
$router->get('/dashboard/favorit', [DashboardController::class, 'favorit']);
$router->get('/dashboard/ulasan', [DashboardController::class, 'ulasan']);
$router->get('/settings', [DashboardController::class, 'settings']);
$router->post('/settings', [DashboardController::class, 'updateSettings']);

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
