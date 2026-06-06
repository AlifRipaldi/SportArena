<?php

use App\Controllers\HomeController;
use App\Controllers\DashboardController;

$router = $app->router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/index.php', [HomeController::class, 'index']);
$router->get('/lapangan', [HomeController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);

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
