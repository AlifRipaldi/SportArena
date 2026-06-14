<?php
// Lokasi: app/Controllers/AuthController.php
require_once '../app/Core/Controller.php';

class AuthController extends Controller {
    public function login() {
        // Menampilkan halaman form login 1 pintu
        $this->view('auth/login'); 
    }

    public function proses_login() {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // 1. Cek Admin
        $adminModel = $this->model('Admin');
        $admin = $adminModel->findByEmail($email);
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['role'] = 'admin';
            header('Location: /admin/dashboard');
            exit;
        }

        // 2. Cek Pemilik Lapangan
        $pemilikModel = $this->model('Pemilik');
        $pemilik = $pemilikModel->findByEmail($email);
        if ($pemilik && password_verify($password, $pemilik['password'])) {
            $_SESSION['user_id'] = $pemilik['id'];
            $_SESSION['role'] = 'pemilik';
            header('Location: /pemilik/dashboard');
            exit;
        }

        // 3. Cek User Biasa
        $userModel = $this->model('User');
        $user = $userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = 'user';
            header('Location: /dashboard');
            exit;
        }

        // Jika gagal semua
        $data['error'] = 'Email atau password salah!';
        $this->view('auth/login', $data); 
    }
}