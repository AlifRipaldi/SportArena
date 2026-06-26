<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\ArenaData;
use App\Models\Jadwal;

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

    protected function requireAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $role = isset($_SESSION['role_user']) ? $_SESSION['role_user'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');
        if (empty($_SESSION['id_user']) || !$this->isAdminRole($role)) {
            header('Location: ' . app_url(empty($_SESSION['id_user']) ? 'public/login.php' : 'dashboard'));
            exit;
        }

        return array(
            'id' => (string) $_SESSION['id_user'],
            'name' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : (isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin Arena'),
            'role' => $role,
        );
    }

    protected function verifyAdminPost()
    {
        $this->requireAdmin();
        $token = isset($_POST['admin_token']) ? (string) $_POST['admin_token'] : '';

        if ($token === '' || empty($_SESSION['admin_csrf']) || !hash_equals((string) $_SESSION['admin_csrf'], $token)) {
            http_response_code(419);
            exit('Permintaan kedaluwarsa. Muat ulang halaman lalu coba lagi.');
        }
    }

    protected function adminFlash($type, $message)
    {
        $_SESSION['admin_flash'] = array('type' => $type, 'message' => $message);
    }

    protected function adminActionResult($path, $success, $successMessage, $failureMessage)
    {
        $this->adminFlash($success ? 'success' : 'error', $success ? $successMessage : $failureMessage);
        $this->redirect($path);
    }

    protected function adminId($prefix)
    {
        return strtoupper($prefix) . date('ymdHis') . strtoupper(bin2hex(random_bytes(2)));
    }

    public function storeBooking()
    {
        $this->verifyAdminPost();
        $userId = trim(isset($_POST['id_user']) ? (string) $_POST['id_user'] : '');
        $scheduleId = trim(isset($_POST['id_jadwal']) ? (string) $_POST['id_jadwal'] : '');
        $note = trim(isset($_POST['catatan']) ? (string) $_POST['catatan'] : '');
        $connection = Database::connection();
        mysqli_begin_transaction($connection);

        try {
            $data = $this->adminData();
            $schedule = $data->row("SELECT j.Harga,l.Harga AS field_price FROM jadwal j INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan WHERE j.ID_Jadwal=? AND LOWER(j.Status) IN ('available','tersedia') AND NOT EXISTS (SELECT 1 FROM booking b WHERE b.ID_Jadwal=j.ID_Jadwal AND LOWER(TRIM(b.Status)) NOT IN ('dibatalkan','cancelled','batal')) AND l.deleted_at IS NULL FOR UPDATE", 's', array($scheduleId));
            $isCustomer = (int) $data->value("SELECT COUNT(*) value FROM users WHERE ID_User=? AND LOWER(Role)='customer' AND LOWER(Status)='aktif'", 's', array($userId));
            if (!$schedule || $isCustomer < 1) {
                throw new \RuntimeException('Customer atau jadwal tidak valid.');
            }

            $bookingId = $this->adminId('BK');
            $price = max(0, (int) ($schedule['Harga'] > 0 ? $schedule['Harga'] : $schedule['field_price']));
            if (!$data->execute("INSERT INTO booking (ID_Booking,ID_Jadwal,ID_User,Waktu_transaksi,Total_harga,Status,Catatan) VALUES (?,?,?,NOW(),?,'Menunggu Pembayaran',?)", 'sssis', array($bookingId, $scheduleId, $userId, $price, $note))) {
                throw new \RuntimeException('Booking gagal disimpan.');
            }
            $data->execute("UPDATE jadwal SET Status='Booked' WHERE ID_Jadwal=?", 's', array($scheduleId));
            mysqli_commit($connection);
            $this->adminActionResult('admin/booking', true, 'Booking baru berhasil dibuat.', '');
        } catch (\Throwable $exception) {
            mysqli_rollback($connection);
            $this->adminActionResult('admin/booking', false, '', $exception->getMessage());
        }
    }

    public function updateBooking()
    {
        $this->verifyAdminPost();
        $id = trim(isset($_POST['id_booking']) ? (string) $_POST['id_booking'] : '');
        $status = trim(isset($_POST['status']) ? (string) $_POST['status'] : '');
        $allowed = array('Menunggu Pembayaran', 'Aktif', 'Selesai', 'Dibatalkan');
        if ($id === '' || !in_array($status, $allowed, true)) {
            $this->adminActionResult('admin/booking', false, '', 'Data booking tidak valid.');
        }

        $data = $this->adminData();
        $success = $data->execute("UPDATE booking SET Status=?,Dibatalkan_pada=IF(?='Dibatalkan',NOW(),NULL) WHERE ID_Booking=?", 'sss', array($status, $status, $id));
        if ($success) {
            $scheduleId = $data->value('SELECT ID_Jadwal value FROM booking WHERE ID_Booking=?', 's', array($id));
            $data->execute("UPDATE jadwal SET Status=? WHERE ID_Jadwal=?", 'ss', array($status === 'Dibatalkan' ? 'Available' : 'Booked', $scheduleId));
        }
        $this->adminActionResult('admin/booking', $success, 'Status booking berhasil diperbarui.', 'Booking tidak ditemukan.');
    }

    public function deleteBooking()
    {
        $_POST['status'] = 'Dibatalkan';
        $this->updateBooking();
    }

    public function storeLapangan()
    {
        $this->verifyAdminPost();
        $ownerId = trim(isset($_POST['id_pemilik']) ? (string) $_POST['id_pemilik'] : '');
        $name = trim(isset($_POST['nama']) ? (string) $_POST['nama'] : '');
        $location = trim(isset($_POST['lokasi']) ? (string) $_POST['lokasi'] : '');
        $type = trim(isset($_POST['jenis']) ? (string) $_POST['jenis'] : '');
        $facilities = trim(isset($_POST['fasilitas']) ? (string) $_POST['fasilitas'] : '');
        $price = max(0, (int) (isset($_POST['harga']) ? $_POST['harga'] : 0));
        if ($ownerId === '' || $name === '' || $location === '' || $type === '' || $price < 1) {
            $this->adminActionResult('admin/lapangan', false, '', 'Lengkapi data wajib lapangan.');
        }
        $success = $this->adminData()->execute("INSERT INTO lapangan (ID_Lapangan,Nama_lapangan,Lokasi,Jenis_olahraga,Fasilitas,ID_Pemilik,Harga,Status,Deskripsi) VALUES (?,?,?,?,?,?,?,'Aktif',?)", 'ssssssis', array($this->adminId('LP'), $name, $location, $type, $facilities, $ownerId, $price, trim(isset($_POST['deskripsi']) ? (string) $_POST['deskripsi'] : '')));
        $this->adminActionResult('admin/lapangan', $success, 'Lapangan berhasil ditambahkan.', 'Lapangan gagal ditambahkan. Pastikan pemilik valid.');
    }

    public function updateLapangan()
    {
        $this->verifyAdminPost();
        $id = trim(isset($_POST['id_lapangan']) ? (string) $_POST['id_lapangan'] : '');
        $status = trim(isset($_POST['status']) ? (string) $_POST['status'] : 'Aktif');
        if (!in_array($status, array('Aktif', 'Maintenance', 'Nonaktif'), true)) {
            $status = 'Nonaktif';
        }
        $values = array(
            trim(isset($_POST['nama']) ? (string) $_POST['nama'] : ''),
            trim(isset($_POST['lokasi']) ? (string) $_POST['lokasi'] : ''),
            trim(isset($_POST['jenis']) ? (string) $_POST['jenis'] : ''),
            trim(isset($_POST['fasilitas']) ? (string) $_POST['fasilitas'] : ''),
            trim(isset($_POST['id_pemilik']) ? (string) $_POST['id_pemilik'] : ''),
            max(0, (int) (isset($_POST['harga']) ? $_POST['harga'] : 0)),
            $status,
            trim(isset($_POST['deskripsi']) ? (string) $_POST['deskripsi'] : ''),
            $id,
        );
        $valid = $id !== '' && $values[0] !== '' && $values[1] !== '' && $values[2] !== '' && $values[4] !== '' && $values[5] > 0;
        $success = $valid && $this->adminData()->execute('UPDATE lapangan SET Nama_lapangan=?,Lokasi=?,Jenis_olahraga=?,Fasilitas=?,ID_Pemilik=?,Harga=?,Status=?,Deskripsi=? WHERE ID_Lapangan=? AND deleted_at IS NULL', 'sssssisss', $values);
        $this->adminActionResult('admin/lapangan', $success, 'Lapangan berhasil diperbarui.', 'Data lapangan tidak valid.');
    }

    public function deleteLapangan()
    {
        $this->verifyAdminPost();
        $id = trim(isset($_POST['id_lapangan']) ? (string) $_POST['id_lapangan'] : '');
        $success = $id !== '' && $this->adminData()->execute("UPDATE lapangan SET deleted_at=NOW(),Status='Nonaktif' WHERE ID_Lapangan=? AND deleted_at IS NULL", 's', array($id));
        $this->adminActionResult('admin/lapangan', $success, 'Lapangan berhasil dinonaktifkan.', 'Lapangan tidak ditemukan.');
    }

    public function jadwal()
    {
        $admin = $this->requireAdmin();
        $this->ensureAdminSchedules();
        $fieldId = trim(isset($_GET['field']) ? (string) $_GET['field'] : '');
        $sql = "SELECT j.ID_Jadwal,j.ID_Lapangan,j.Tanggal,j.Jam_Mulai,j.Jam_Selesai,j.Status,j.Harga,l.Nama_lapangan FROM jadwal j INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan WHERE l.deleted_at IS NULL";
        $types = '';
        $params = array();
        if ($fieldId !== '') {
            $sql .= ' AND j.ID_Lapangan=?';
            $types = 's';
            $params[] = $fieldId;
        }
        $sql .= ' ORDER BY j.Tanggal DESC,j.Jam_Mulai';
        return $this->view('Admin/jadwal', array(
            'title' => 'Kelola Jadwal | Arena Sport', 'activeMenu' => 'lapangan', 'userName' => $admin['name'], 'userRole' => $admin['role'],
            'selectedField' => $fieldId, 'schedules' => $this->adminData()->rows($sql, $types, $params),
            'scheduleFields' => $this->adminData()->rows("SELECT ID_Lapangan id,Nama_lapangan name,Harga price FROM lapangan WHERE deleted_at IS NULL AND LOWER(Status)='aktif' ORDER BY Nama_lapangan"),
        ), 'layouts/admin');
    }

    public function search()
    {
        $admin = $this->requireAdmin();
        $query = trim(isset($_GET['q']) ? (string) $_GET['q'] : '');
        $term = '%' . $query . '%';
        $results = array();
        if ($query !== '') {
            foreach ($this->adminData()->rows("SELECT ID_User id,Nama title,CONCAT(Email,' · ',Role) detail FROM users WHERE Nama LIKE ? OR Email LIKE ? LIMIT 10", 'ss', array($term, $term)) as $row) {
                $results[] = array_merge($row, array('type' => 'Pengguna', 'url' => app_url('admin/users')));
            }
            foreach ($this->adminData()->rows("SELECT ID_Lapangan id,Nama_lapangan title,CONCAT(Jenis_olahraga,' · ',Lokasi) detail FROM lapangan WHERE deleted_at IS NULL AND (Nama_lapangan LIKE ? OR Lokasi LIKE ?) LIMIT 10", 'ss', array($term, $term)) as $row) {
                $results[] = array_merge($row, array('type' => 'Lapangan', 'url' => app_url('admin/lapangan')));
            }
            foreach ($this->adminData()->rows("SELECT b.ID_Booking id,b.ID_Booking title,CONCAT(u.Nama,' · ',l.Nama_lapangan) detail FROM booking b INNER JOIN users u ON u.ID_User=b.ID_User INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan WHERE b.ID_Booking LIKE ? OR u.Nama LIKE ? OR l.Nama_lapangan LIKE ? LIMIT 10", 'sss', array($term, $term, $term)) as $row) {
                $results[] = array_merge($row, array('type' => 'Booking', 'url' => app_url('admin/booking')));
            }
        }
        return $this->view('Admin/search', array('title' => 'Pencarian Admin | Arena Sport', 'activeMenu' => '', 'userName' => $admin['name'], 'userRole' => $admin['role'], 'query' => $query, 'results' => $results), 'layouts/admin');
    }

    public function storeJadwal()
    {
        $this->verifyAdminPost();
        $fieldId = trim(isset($_POST['id_lapangan']) ? (string) $_POST['id_lapangan'] : '');
        $date = trim(isset($_POST['tanggal']) ? (string) $_POST['tanggal'] : '');
        $start = trim(isset($_POST['jam_mulai']) ? (string) $_POST['jam_mulai'] : '');
        $end = trim(isset($_POST['jam_selesai']) ? (string) $_POST['jam_selesai'] : '');
        $price = max(0, (int) (isset($_POST['harga']) ? $_POST['harga'] : 0));
        $valid = $fieldId !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && $start !== '' && $start < $end;
        $success = $valid && $this->adminData()->execute("INSERT INTO jadwal (ID_Jadwal,ID_Lapangan,Tanggal,Jam_Mulai,Jam_Selesai,Status,Harga) VALUES (?,?,?,?,?,'Available',?)", 'sssssi', array($this->adminId('JWL'), $fieldId, $date, $start, $end, $price));
        $this->adminActionResult('admin/jadwal?field=' . rawurlencode($fieldId), $success, 'Jadwal berhasil ditambahkan.', 'Jadwal tidak valid atau slot sudah tersedia.');
    }

    public function updateJadwal()
    {
        $this->verifyAdminPost();
        $id = trim(isset($_POST['id_jadwal']) ? (string) $_POST['id_jadwal'] : '');
        $fieldId = trim(isset($_POST['id_lapangan']) ? (string) $_POST['id_lapangan'] : '');
        $date = trim(isset($_POST['tanggal']) ? (string) $_POST['tanggal'] : '');
        $start = trim(isset($_POST['jam_mulai']) ? (string) $_POST['jam_mulai'] : '');
        $end = trim(isset($_POST['jam_selesai']) ? (string) $_POST['jam_selesai'] : '');
        $status = trim(isset($_POST['status']) ? (string) $_POST['status'] : 'Available');
        $price = max(0, (int) (isset($_POST['harga']) ? $_POST['harga'] : 0));
        $allowed = array('Available', 'Blocked', 'Maintenance');
        $hasBooking = (int) $this->adminData()->value('SELECT COUNT(*) value FROM booking WHERE ID_Jadwal=?', 's', array($id));
        $valid = $id !== '' && $fieldId !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && $start < $end && in_array($status, $allowed, true) && $hasBooking === 0;
        $success = $valid && $this->adminData()->execute('UPDATE jadwal SET ID_Lapangan=?,Tanggal=?,Jam_Mulai=?,Jam_Selesai=?,Status=?,Harga=? WHERE ID_Jadwal=?', 'sssssis', array($fieldId, $date, $start, $end, $status, $price, $id));
        $this->adminActionResult('admin/jadwal?field=' . rawurlencode($fieldId), $success, 'Jadwal berhasil diperbarui.', $hasBooking > 0 ? 'Jadwal yang sudah memiliki booking tidak dapat diubah.' : 'Data jadwal tidak valid.');
    }

    public function deleteJadwal()
    {
        $this->verifyAdminPost();
        $id = trim(isset($_POST['id_jadwal']) ? (string) $_POST['id_jadwal'] : '');
        $fieldId = trim(isset($_POST['id_lapangan']) ? (string) $_POST['id_lapangan'] : '');
        $hasBooking = (int) $this->adminData()->value('SELECT COUNT(*) value FROM booking WHERE ID_Jadwal=?', 's', array($id));
        $success = $id !== '' && $hasBooking === 0 && $this->adminData()->execute('DELETE FROM jadwal WHERE ID_Jadwal=?', 's', array($id));
        $this->adminActionResult('admin/jadwal?field=' . rawurlencode($fieldId), $success, 'Jadwal berhasil dihapus.', $hasBooking > 0 ? 'Jadwal memiliki booking dan tidak dapat dihapus.' : 'Jadwal tidak ditemukan.');
    }

    public function storeUser()
    {
        $this->verifyAdminPost();
        $name = trim(isset($_POST['nama']) ? (string) $_POST['nama'] : '');
        $email = strtolower(trim(isset($_POST['email']) ? (string) $_POST['email'] : ''));
        $phone = trim(isset($_POST['telepon']) ? (string) $_POST['telepon'] : '');
        $role = strtolower(trim(isset($_POST['role']) ? (string) $_POST['role'] : 'customer'));
        $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || strlen($password) < 8 || !in_array($role, array('customer', 'pemilik'), true)) {
            $this->adminActionResult('admin/users', false, '', 'Nama, email, telepon, role, dan password minimal 8 karakter wajib diisi.');
        }
        $connection = Database::connection();
        mysqli_begin_transaction($connection);
        try {
            $data = $this->adminData();
            $id = $this->adminId('USR');
            if (!$data->execute("INSERT INTO users (ID_User,Nama,Email,Password,Nomor_telepon,Role,Status) VALUES (?,?,?,?,?,?,'Aktif')", 'ssssss', array($id, $name, $email, password_hash($password, PASSWORD_DEFAULT), $phone, $role))) {
                throw new \RuntimeException('Email sudah digunakan atau data tidak dapat disimpan.');
            }
            if ($role === 'pemilik') {
                $business = trim(isset($_POST['nama_usaha']) ? (string) $_POST['nama_usaha'] : $name);
                $address = trim(isset($_POST['alamat']) ? (string) $_POST['alamat'] : '-');
                if (!$data->execute("INSERT INTO pemilik_lapangan (ID_Pemilik,ID_User,nama_usaha,alamat,Status_verifikasi) VALUES (?,?,?,?,'Terverifikasi')", 'ssss', array($this->adminId('OWN'), $id, $business !== '' ? $business : $name, $address !== '' ? $address : '-'))) {
                    throw new \RuntimeException('Profil pemilik gagal dibuat.');
                }
            }
            mysqli_commit($connection);
            $this->adminActionResult('admin/users', true, 'Pengguna berhasil ditambahkan.', '');
        } catch (\Throwable $exception) {
            mysqli_rollback($connection);
            $this->adminActionResult('admin/users', false, '', $exception->getMessage());
        }
    }

    public function updateUser()
    {
        $this->verifyAdminPost();
        $id = trim(isset($_POST['id_user']) ? (string) $_POST['id_user'] : '');
        $name = trim(isset($_POST['nama']) ? (string) $_POST['nama'] : '');
        $email = strtolower(trim(isset($_POST['email']) ? (string) $_POST['email'] : ''));
        $phone = trim(isset($_POST['telepon']) ? (string) $_POST['telepon'] : '');
        $role = strtolower(trim(isset($_POST['role']) ? (string) $_POST['role'] : 'customer'));
        $status = trim(isset($_POST['status']) ? (string) $_POST['status'] : 'Aktif');
        $valid = $id !== '' && $name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && $phone !== '' && in_array($role, array('customer', 'pemilik'), true) && in_array($status, array('Aktif', 'Nonaktif'), true);
        $data = $this->adminData();
        $ownerId = $data->value('SELECT ID_Pemilik value FROM pemilik_lapangan WHERE ID_User=?', 's', array($id));
        if ($valid && $role === 'customer' && $ownerId && (int) $data->value('SELECT COUNT(*) value FROM lapangan WHERE ID_Pemilik=? AND deleted_at IS NULL', 's', array($ownerId)) > 0) {
            $valid = false;
        }
        $success = $valid && $data->execute('UPDATE users SET Nama=?,Email=?,Nomor_telepon=?,Role=?,Status=? WHERE ID_User=? AND LOWER(Role)<>\'admin\'', 'ssssss', array($name, $email, $phone, $role, $status, $id));
        if ($success && $role === 'pemilik' && !(int) $data->value('SELECT COUNT(*) value FROM pemilik_lapangan WHERE ID_User=?', 's', array($id))) {
            $success = $data->execute("INSERT INTO pemilik_lapangan (ID_Pemilik,ID_User,nama_usaha,alamat,Status_verifikasi) VALUES (?,?,?,?,'Terverifikasi')", 'ssss', array($this->adminId('OWN'), $id, $name, '-'));
        }
        if ($success && $role === 'customer' && $ownerId) {
            $success = $data->execute('DELETE FROM pemilik_lapangan WHERE ID_Pemilik=?', 's', array($ownerId));
        }
        $this->adminActionResult('admin/users', $success, 'Data pengguna berhasil diperbarui.', 'Data pengguna tidak valid atau email sudah digunakan.');
    }

    public function deleteUser()
    {
        $this->verifyAdminPost();
        $id = trim(isset($_POST['id_user']) ? (string) $_POST['id_user'] : '');
        $success = $id !== '' && $this->adminData()->execute("UPDATE users SET Status='Nonaktif' WHERE ID_User=? AND LOWER(Role)<>'admin'", 's', array($id));
        $this->adminActionResult('admin/users', $success, 'Pengguna dinonaktifkan agar riwayat transaksi tetap aman.', 'Pengguna tidak ditemukan.');
    }

    public function replyReview()
    {
        $this->verifyAdminPost();
        $id = trim(isset($_POST['id_review']) ? (string) $_POST['id_review'] : '');
        $reply = trim(isset($_POST['balasan']) ? (string) $_POST['balasan'] : '');
        $success = $id !== '' && $reply !== '' && $this->adminData()->execute('UPDATE review SET Balasan=? WHERE ID_Review=?', 'ss', array($reply, $id));
        $this->adminActionResult('admin/ulasan', $success, 'Tanggapan ulasan berhasil disimpan.', 'Tanggapan tidak boleh kosong.');
    }

    public function deleteReview()
    {
        $this->verifyAdminPost();
        $id = trim(isset($_POST['id_review']) ? (string) $_POST['id_review'] : '');
        $success = $id !== '' && $this->adminData()->execute('DELETE FROM review WHERE ID_Review=?', 's', array($id));
        $this->adminActionResult('admin/ulasan', $success, 'Ulasan berhasil dihapus.', 'Ulasan tidak ditemukan.');
    }

    public function updateTransaction()
    {
        $this->verifyAdminPost();
        $id = trim(isset($_POST['id_pembayaran']) ? (string) $_POST['id_pembayaran'] : '');
        $status = trim(isset($_POST['status']) ? (string) $_POST['status'] : '');
        $allowed = array('Pending', 'Berhasil', 'Gagal', 'Refund');
        $success = $id !== '' && in_array($status, $allowed, true) && $this->adminData()->execute('UPDATE pembayaran SET Status=?,Waktu_pembayaran=IF(?=\'Berhasil\',COALESCE(Waktu_pembayaran,NOW()),Waktu_pembayaran) WHERE ID_Pembayaran=?', 'sss', array($status, $status, $id));
        $this->adminActionResult('admin/transaksi', $success, 'Status transaksi berhasil diperbarui.', 'Transaksi tidak valid.');
    }

    public function updateProfile()
    {
        $admin = $this->requireAdmin();
        $this->verifyAdminPost();
        $name = trim(isset($_POST['nama']) ? (string) $_POST['nama'] : '');
        $email = strtolower(trim(isset($_POST['email']) ? (string) $_POST['email'] : ''));
        $phone = trim(isset($_POST['telepon']) ? (string) $_POST['telepon'] : '');
        $success = $name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && $phone !== '' && $this->adminData()->execute('UPDATE users SET Nama=?,Email=?,Nomor_telepon=? WHERE ID_User=?', 'ssss', array($name, $email, $phone, $admin['id']));
        if ($success) {
            $_SESSION['nama_user'] = $name;
            $_SESSION['nama'] = $name;
        }
        $this->adminActionResult('admin/pengaturan?tab=akun', $success, 'Profil administrator berhasil diperbarui.', 'Profil gagal diperbarui.');
    }

    public function updatePassword()
    {
        $admin = $this->requireAdmin();
        $this->verifyAdminPost();
        $current = isset($_POST['password_saat_ini']) ? (string) $_POST['password_saat_ini'] : '';
        $new = isset($_POST['password_baru']) ? (string) $_POST['password_baru'] : '';
        $confirmation = isset($_POST['konfirmasi_password']) ? (string) $_POST['konfirmasi_password'] : '';
        $row = $this->adminData()->row('SELECT Password FROM users WHERE ID_User=?', 's', array($admin['id']));
        $validCurrent = $row && (password_verify($current, (string) $row['Password']) || hash_equals((string) $row['Password'], $current));
        $success = $validCurrent && strlen($new) >= 8 && $new === $confirmation && $this->adminData()->execute('UPDATE users SET Password=? WHERE ID_User=?', 'ss', array(password_hash($new, PASSWORD_DEFAULT), $admin['id']));
        $message = !$validCurrent ? 'Password saat ini salah.' : (($new !== $confirmation || strlen($new) < 8) ? 'Password baru minimal 8 karakter dan konfirmasi harus sama.' : 'Password gagal diperbarui.');
        $this->adminActionResult('admin/pengaturan?tab=akun', $success, 'Password administrator berhasil diganti.', $message);
    }

    public function updatePaymentMethods()
    {
        $this->verifyAdminPost();
        $active = isset($_POST['methods']) && is_array($_POST['methods']) ? array_map('strval', $_POST['methods']) : array();
        $data = $this->adminData();
        $methods = $data->rows('SELECT ID_Metode FROM metode_pembayaran');
        $success = true;
        foreach ($methods as $method) {
            $enabled = in_array((string) $method['ID_Metode'], $active, true) ? 1 : 0;
            $success = $data->execute('UPDATE metode_pembayaran SET Aktif=? WHERE ID_Metode=?', 'is', array($enabled, $method['ID_Metode'])) && $success;
        }
        $this->adminActionResult('admin/pengaturan?tab=pembayaran', $success, 'Metode pembayaran berhasil diperbarui.', 'Metode pembayaran gagal diperbarui.');
    }

    public function updatePreferences()
    {
        $this->verifyAdminPost();
        $section = strtolower(trim(isset($_POST['section']) ? (string) $_POST['section'] : 'umum'));
        $allowedSections = array('umum', 'notifikasi', 'pembayaran', 'keamanan', 'akun');
        if (!in_array($section, $allowedSections, true)) {
            $section = 'umum';
        }
        $submitted = isset($_POST['settings']) && is_array($_POST['settings']) ? $_POST['settings'] : array();
        $preferences = $this->adminPreferences();
        foreach ($submitted as $key => $value) {
            $key = preg_replace('/[^a-z0-9_]/i', '', (string) $key);
            if ($key === '') {
                continue;
            }
            $preferences[$key] = is_array($value) ? '' : substr(trim((string) $value), 0, 500);
        }
        $encoded = json_encode($preferences, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $success = $encoded !== false && file_put_contents($this->adminPreferenceFile(), $encoded, LOCK_EX) !== false;
        $this->adminActionResult('admin/pengaturan?tab=' . $section, $success, 'Preferensi berhasil disimpan.', 'Preferensi gagal disimpan.');
    }

    public function storeBankAccount()
    {
        $this->verifyAdminPost();
        $ownerId = trim(isset($_POST['id_pemilik']) ? (string) $_POST['id_pemilik'] : '');
        $bank = trim(isset($_POST['bank']) ? (string) $_POST['bank'] : '');
        $number = preg_replace('/\s+/', '', trim(isset($_POST['nomor']) ? (string) $_POST['nomor'] : ''));
        $holder = trim(isset($_POST['pemilik']) ? (string) $_POST['pemilik'] : '');
        $success = $ownerId !== '' && $bank !== '' && $number !== '' && $holder !== '' && $this->adminData()->execute("INSERT INTO rekening_pemilik (ID_Pemilik,Nama_bank,Nomor_rekening,Nama_pemilik,Status) VALUES (?,?,?,?,'Aktif')", 'ssss', array($ownerId, $bank, $number, $holder));
        $this->adminActionResult('admin/pengaturan?tab=pembayaran', $success, 'Rekening berhasil ditambahkan.', 'Data rekening tidak valid.');
    }

    public function updateBankAccount()
    {
        $this->verifyAdminPost();
        $id = (int) (isset($_POST['id_rekening']) ? $_POST['id_rekening'] : 0);
        $status = trim(isset($_POST['status']) ? (string) $_POST['status'] : 'Aktif');
        if (!in_array($status, array('Aktif', 'Nonaktif'), true)) {
            $status = 'Nonaktif';
        }
        $values = array(trim(isset($_POST['bank']) ? (string) $_POST['bank'] : ''), preg_replace('/\s+/', '', trim(isset($_POST['nomor']) ? (string) $_POST['nomor'] : '')), trim(isset($_POST['pemilik']) ? (string) $_POST['pemilik'] : ''), $status, $id);
        $success = $id > 0 && $values[0] !== '' && $values[1] !== '' && $values[2] !== '' && $this->adminData()->execute('UPDATE rekening_pemilik SET Nama_bank=?,Nomor_rekening=?,Nama_pemilik=?,Status=? WHERE ID_Rekening=?', 'ssssi', $values);
        $this->adminActionResult('admin/pengaturan?tab=pembayaran', $success, 'Rekening berhasil diperbarui.', 'Rekening tidak valid.');
    }

    public function deleteBankAccount()
    {
        $this->verifyAdminPost();
        $id = (int) (isset($_POST['id_rekening']) ? $_POST['id_rekening'] : 0);
        $success = $id > 0 && $this->adminData()->execute('DELETE FROM rekening_pemilik WHERE ID_Rekening=?', 'i', array($id));
        $this->adminActionResult('admin/pengaturan?tab=pembayaran', $success, 'Rekening berhasil dihapus.', 'Rekening tidak ditemukan.');
    }

    protected function adminPreferenceFile()
    {
        return dirname(__DIR__, 2) . '/storage/admin-settings.json';
    }

    protected function adminPreferences()
    {
        $defaults = array(
            'app_name' => 'Arena Sport',
            'app_description' => 'Platform booking lapangan olahraga online.',
            'admin_email' => 'admin@arenasport.com',
            'admin_phone' => '0812-3456-7890',
            'admin_address' => 'Parepare, Sulawesi Selatan',
            'maintenance_mode' => '0', 'user_registration' => '1', 'auto_approval' => '1', 'email_notification' => '1', 'dark_theme' => '1',
            'notification_in_app' => '1', 'notification_email' => '1', 'notification_new_booking' => '1', 'notification_confirmed' => '1', 'notification_cancelled' => '1', 'notification_reminder' => '1', 'notification_review' => '1', 'notification_promo' => '0', 'notification_security' => '1',
            'payment_timeout' => '60 Menit', 'admin_fee' => '2,5 %', 'minimum_payment' => 'Rp 10.000',
            'login_notification' => '1', 'automatic_logout' => '1', 'login_history' => '1',
        );
        $path = $this->adminPreferenceFile();
        if (!is_file($path)) {
            return $defaults;
        }
        $decoded = json_decode((string) file_get_contents($path), true);
        return is_array($decoded) ? array_merge($defaults, $decoded) : $defaults;
    }

    public function export($type)
    {
        $this->requireAdmin();
        $type = strtolower(trim((string) $type));
        $allowed = array('booking', 'users', 'lapangan', 'ulasan', 'transaksi', 'laporan');
        if (!in_array($type, $allowed, true)) {
            http_response_code(404);
            exit('Jenis export tidak ditemukan.');
        }

        $filters = null;
        if ($type === 'laporan' || $this->hasAdminReportFilterInput($_GET)) {
            $filters = $this->adminNormalizeReportField($this->adminReportFilters($_GET), $this->adminReportFieldOptions());
        }

        $rows = $this->adminExportRows($type, $filters);
        $filename = 'arena-sport-' . $type . '-' . date('Ymd-His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');
        if (!empty($rows)) {
            fputcsv($output, array_keys($rows[0]), ';');
            foreach ($rows as $row) {
                fputcsv($output, array_values($row), ';');
            }
        }
        fclose($output);
        exit;
    }

    protected function adminExportRows($type, $filters = null)
    {
        if ($type === 'booking') {
            if ($filters !== null) {
                $where = $this->adminReportBookingWhere($filters, 'b', 'l');
                return $this->adminData()->rows(
                    'SELECT b.ID_Booking,u.Nama AS Customer,l.Nama_lapangan AS Lapangan,j.Tanggal,j.Jam_Mulai,j.Jam_Selesai,b.Total_harga,b.Status FROM booking b INNER JOIN users u ON u.ID_User=b.ID_User INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan ' . $where['sql'] . ' ORDER BY b.Waktu_transaksi DESC',
                    $where['types'],
                    $where['params']
                );
            }

            return $this->adminData()->rows('SELECT b.ID_Booking,u.Nama AS Customer,l.Nama_lapangan AS Lapangan,j.Tanggal,j.Jam_Mulai,j.Jam_Selesai,b.Total_harga,b.Status FROM booking b INNER JOIN users u ON u.ID_User=b.ID_User INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan ORDER BY b.Waktu_transaksi DESC');
        }
        if ($type === 'users') {
            if ($filters !== null) {
                return $this->adminData()->rows(
                    "SELECT ID_User,Nama,Email,Nomor_telepon,Role,Status,created_at FROM users WHERE LOWER(Role)<>'admin' AND DATE(created_at) BETWEEN ? AND ? ORDER BY created_at DESC",
                    'ss',
                    array($filters['start'], $filters['end'])
                );
            }

            return $this->adminData()->rows("SELECT ID_User,Nama,Email,Nomor_telepon,Role,Status,created_at FROM users WHERE LOWER(Role)<>'admin' ORDER BY created_at DESC");
        }
        if ($type === 'lapangan') {
            if ($filters !== null) {
                $where = $this->adminReportFieldWhere($filters, 'l');
                return $this->adminData()->rows(
                    'SELECT l.ID_Lapangan,l.Nama_lapangan,l.Lokasi,l.Jenis_olahraga,l.Harga,l.Status,p.nama_usaha AS Pemilik FROM lapangan l INNER JOIN pemilik_lapangan p ON p.ID_Pemilik=l.ID_Pemilik ' . $where['sql'] . ' ORDER BY l.created_at DESC',
                    $where['types'],
                    $where['params']
                );
            }

            return $this->adminData()->rows('SELECT l.ID_Lapangan,l.Nama_lapangan,l.Lokasi,l.Jenis_olahraga,l.Harga,l.Status,p.nama_usaha AS Pemilik FROM lapangan l INNER JOIN pemilik_lapangan p ON p.ID_Pemilik=l.ID_Pemilik WHERE l.deleted_at IS NULL ORDER BY l.created_at DESC');
        }
        if ($type === 'ulasan') {
            return $this->adminData()->rows('SELECT r.ID_Review,u.Nama AS Pengguna,l.Nama_lapangan AS Lapangan,r.Rating,r.Komentar,r.Balasan,r.created_at FROM review r INNER JOIN users u ON u.ID_User=r.ID_User INNER JOIN lapangan l ON l.ID_Lapangan=r.ID_Lapangan ORDER BY r.created_at DESC');
        }
        if ($type === 'transaksi') {
            if ($filters !== null) {
                $where = $this->adminReportPaymentWhere($filters, 'p', 'l');
                return $this->adminData()->rows(
                    'SELECT p.ID_Pembayaran,p.ID_Booking,u.Nama AS Pengguna,l.Nama_lapangan AS Lapangan,p.Metode,p.Jumlah,p.Status,COALESCE(p.Waktu_pembayaran,p.created_at) AS Waktu FROM pembayaran p INNER JOIN booking b ON b.ID_Booking=p.ID_Booking INNER JOIN users u ON u.ID_User=b.ID_User INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan ' . $where['sql'] . ' ORDER BY p.created_at DESC',
                    $where['types'],
                    $where['params']
                );
            }

            return $this->adminData()->rows('SELECT p.ID_Pembayaran,p.ID_Booking,u.Nama AS Pengguna,l.Nama_lapangan AS Lapangan,p.Metode,p.Jumlah,p.Status,COALESCE(p.Waktu_pembayaran,p.created_at) AS Waktu FROM pembayaran p INNER JOIN booking b ON b.ID_Booking=p.ID_Booking INNER JOIN users u ON u.ID_User=b.ID_User INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan ORDER BY p.created_at DESC');
        }

        if ($filters !== null) {
            $rows = array(
                array('Metrik' => 'Periode', 'Nilai' => $filters['startLabel'] . ' - ' . $filters['endLabel']),
                array('Metrik' => 'Lapangan', 'Nilai' => $this->adminReportFieldLabel($filters['field'])),
            );

            foreach ($this->adminReportStatsFromDatabase($filters) as $stat) {
                $rows[] = array('Metrik' => $stat['label'], 'Nilai' => $stat['value']);
            }

            return $rows;
        }

        return array(
            array('Metrik' => 'Total Booking', 'Nilai' => (string) $this->adminData()->value('SELECT COUNT(*) value FROM booking')),
            array('Metrik' => 'Total Pengguna', 'Nilai' => (string) $this->adminData()->value('SELECT COUNT(*) value FROM users')),
            array('Metrik' => 'Lapangan Aktif', 'Nilai' => (string) $this->adminData()->value("SELECT COUNT(*) value FROM lapangan WHERE LOWER(Status)='aktif' AND deleted_at IS NULL")),
            array('Metrik' => 'Pendapatan', 'Nilai' => (string) $this->adminData()->value("SELECT COALESCE(SUM(Jumlah),0) value FROM pembayaran WHERE LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid')")),
        );
    }

    protected function adminData()
    {
        return new ArenaData();
    }

    protected function ensureAdminSchedules()
    {
        $rows = $this->adminData()->rows("SELECT ID_Lapangan FROM lapangan WHERE LOWER(Status)='aktif' AND deleted_at IS NULL");
        $jadwal = new Jadwal();

        foreach ($rows as $row) {
            if (!empty($row['ID_Lapangan'])) {
                $jadwal->ensureForField($row['ID_Lapangan'], date('Y-m-d'));
            }
        }
    }

    protected function adminRupiah($amount)
    {
        return 'Rp' . number_format(max(0, (int) $amount), 0, ',', '.');
    }

    protected function adminDate($date)
    {
        return $this->adminFormatDate($date);
    }

    protected function hasAdminReportFilterInput($source)
    {
        return is_array($source) && (
            isset($source['start']) || isset($source['end']) || isset($source['field'])
        );
    }

    protected function adminReportFilters($source = null)
    {
        $source = is_array($source) ? $source : array();
        $defaultStart = date('Y-m-01');
        $defaultEnd = date('Y-m-t');
        $start = $this->adminReportDateValue(isset($source['start']) ? $source['start'] : '', $defaultStart);
        $end = $this->adminReportDateValue(isset($source['end']) ? $source['end'] : '', $defaultEnd);

        if (strtotime($start) > strtotime($end)) {
            $swap = $start;
            $start = $end;
            $end = $swap;
        }

        $field = trim(isset($source['field']) ? (string) $source['field'] : '');
        $field = preg_replace('/[^A-Za-z0-9_\-]/', '', $field);

        return array(
            'start' => $start,
            'end' => $end,
            'field' => $field,
            'startLabel' => date('d/m/Y', strtotime($start)),
            'endLabel' => date('d/m/Y', strtotime($end)),
            'periodLabel' => date('d/m/Y', strtotime($start)) . ' - ' . date('d/m/Y', strtotime($end)),
            'monthLabel' => date('m/Y', strtotime($start)) === date('m/Y', strtotime($end)) ? date('m/Y', strtotime($start)) : date('d/m/Y', strtotime($start)) . ' - ' . date('d/m/Y', strtotime($end)),
        );
    }

    protected function adminReportDateValue($value, $fallback)
    {
        $value = trim((string) $value);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $fallback;
        }

        $timestamp = strtotime($value);
        return $timestamp ? date('Y-m-d', $timestamp) : $fallback;
    }

    protected function adminReportFieldOptions()
    {
        return $this->adminData()->rows("SELECT ID_Lapangan id,Nama_lapangan name FROM lapangan WHERE deleted_at IS NULL ORDER BY Nama_lapangan");
    }

    protected function adminNormalizeReportField(array $filters, array $fields)
    {
        if ($filters['field'] === '') {
            $filters['fieldLabel'] = 'Semua Lapangan';
            return $filters;
        }

        foreach ($fields as $field) {
            if ((string) $field['id'] === (string) $filters['field']) {
                $filters['fieldLabel'] = $field['name'];
                return $filters;
            }
        }

        $filters['field'] = '';
        $filters['fieldLabel'] = 'Semua Lapangan';
        return $filters;
    }

    protected function adminReportQuery(array $filters)
    {
        $query = array('start' => $filters['start'], 'end' => $filters['end']);
        if ($filters['field'] !== '') {
            $query['field'] = $filters['field'];
        }

        return http_build_query($query);
    }

    protected function adminReportPaymentWhere(array $filters, $paymentAlias = 'p', $fieldAlias = 'l')
    {
        $conditions = array(
            "LOWER(" . $paymentAlias . ".Status) IN ('berhasil','dibayar','lunas','success','paid')",
            'DATE(COALESCE(' . $paymentAlias . '.Waktu_pembayaran,' . $paymentAlias . '.created_at)) BETWEEN ? AND ?',
        );
        $types = 'ss';
        $params = array($filters['start'], $filters['end']);

        if ($filters['field'] !== '' && $fieldAlias !== '') {
            $conditions[] = $fieldAlias . '.ID_Lapangan=?';
            $types .= 's';
            $params[] = $filters['field'];
        }

        return array('sql' => 'WHERE ' . implode(' AND ', $conditions), 'types' => $types, 'params' => $params);
    }

    protected function adminReportBookingWhere(array $filters, $bookingAlias = 'b', $fieldAlias = 'l')
    {
        $conditions = array('DATE(' . $bookingAlias . '.Waktu_transaksi) BETWEEN ? AND ?');
        $types = 'ss';
        $params = array($filters['start'], $filters['end']);

        if ($filters['field'] !== '' && $fieldAlias !== '') {
            $conditions[] = $fieldAlias . '.ID_Lapangan=?';
            $types .= 's';
            $params[] = $filters['field'];
        }

        return array('sql' => 'WHERE ' . implode(' AND ', $conditions), 'types' => $types, 'params' => $params);
    }

    protected function adminReportFieldWhere(array $filters, $fieldAlias = 'l')
    {
        $conditions = array($fieldAlias . '.deleted_at IS NULL');
        $types = '';
        $params = array();

        if ($filters['field'] !== '') {
            $conditions[] = $fieldAlias . '.ID_Lapangan=?';
            $types .= 's';
            $params[] = $filters['field'];
        }

        return array('sql' => 'WHERE ' . implode(' AND ', $conditions), 'types' => $types, 'params' => $params);
    }

    protected function adminReportFieldLabel($fieldId)
    {
        $fieldId = trim((string) $fieldId);
        if ($fieldId === '') {
            return 'Semua Lapangan';
        }

        $name = $this->adminData()->value('SELECT Nama_lapangan value FROM lapangan WHERE ID_Lapangan=? AND deleted_at IS NULL LIMIT 1', 's', array($fieldId));
        return $name ? (string) $name : 'Semua Lapangan';
    }

    protected function adminReportDateTicks(array $filters)
    {
        $start = strtotime($filters['start']);
        $end = strtotime($filters['end']);
        if (!$start || !$end || $start === $end) {
            return array($this->adminReportShortDate($filters['start']));
        }

        $ticks = array();
        for ($index = 0; $index < 5; $index++) {
            $time = (int) round($start + (($end - $start) * ($index / 4)));
            $ticks[] = $this->adminReportShortDate(date('Y-m-d', $time));
        }

        return $ticks;
    }

    protected function adminReportShortDate($date)
    {
        $timestamp = strtotime($date);
        if (!$timestamp) {
            return (string) $date;
        }

        $months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des');
        return date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp)];
    }

    protected function adminRevenueAxisLabels(array $points)
    {
        $max = 0;
        foreach ($points as $point) {
            $max = max($max, isset($point['rawAmount']) ? (int) $point['rawAmount'] : (int) preg_replace('/[^0-9]/', '', isset($point['amount']) ? $point['amount'] : '0'));
        }

        $top = $this->adminNiceMoneyTop($max);
        $labels = array();
        for ($index = 5; $index >= 0; $index--) {
            $labels[] = $this->adminRupiahShort((int) round(($top / 5) * $index));
        }

        return $labels;
    }

    protected function adminBookingAxisLabels(array $fields)
    {
        $max = 0;
        foreach ($fields as $field) {
            $max = max($max, isset($field['value']) ? (int) $field['value'] : 0);
        }

        $top = $this->adminNiceCountTop($max);
        $labels = array();
        for ($index = 6; $index >= 0; $index--) {
            $labels[] = (string) (int) round(($top / 6) * $index);
        }

        return $labels;
    }

    protected function adminNiceMoneyTop($max)
    {
        $max = max(100000, (int) $max);
        $magnitude = pow(10, floor(log10($max)));
        $normalized = $max / $magnitude;

        if ($normalized <= 1) {
            $nice = 1;
        } elseif ($normalized <= 2) {
            $nice = 2;
        } elseif ($normalized <= 5) {
            $nice = 5;
        } else {
            $nice = 10;
        }

        return (int) ($nice * $magnitude);
    }

    protected function adminNiceCountTop($max)
    {
        $max = max(1, (int) $max);
        return max(6, (int) ceil($max / 6) * 6);
    }

    protected function adminRupiahShort($amount)
    {
        $amount = max(0, (int) $amount);
        if ($amount >= 1000000) {
            $value = $amount / 1000000;
            return 'Rp' . rtrim(rtrim(number_format($value, 1, ',', '.'), '0'), ',') . 'jt';
        }

        if ($amount >= 1000) {
            $value = $amount / 1000;
            return 'Rp' . rtrim(rtrim(number_format($value, 1, ',', '.'), '0'), ',') . 'rb';
        }

        return 'Rp' . number_format($amount, 0, ',', '.');
    }

    protected function adminBookingRows()
    {
        return $this->adminData()->rows(
            "SELECT b.ID_Booking, b.ID_User, b.ID_Jadwal, b.Status AS booking_status, b.Total_harga, b.Waktu_transaksi,
                    u.Nama AS customer_name, u.Nomor_telepon,
                    j.Tanggal, j.Jam_Mulai, j.Jam_Selesai, j.Status AS schedule_status,
                    l.ID_Lapangan, l.Nama_lapangan,
                    p.ID_Pembayaran, p.Jumlah, p.Metode, p.Status AS payment_status,
                    p.Waktu_pembayaran, p.created_at AS payment_created_at
             FROM booking b
             INNER JOIN users u ON u.ID_User = b.ID_User
             INNER JOIN jadwal j ON j.ID_Jadwal = b.ID_Jadwal
             INNER JOIN lapangan l ON l.ID_Lapangan = j.ID_Lapangan
             LEFT JOIN pembayaran p ON p.ID_Pembayaran = (
                 SELECT p2.ID_Pembayaran FROM pembayaran p2
                 WHERE p2.ID_Booking = b.ID_Booking
                 ORDER BY p2.created_at DESC, p2.ID_Pembayaran DESC LIMIT 1
             )
             ORDER BY b.Waktu_transaksi DESC"
        );
    }

    protected function adminBookingPayload($bookingStatus, $paymentStatus = '')
    {
        $status = strtolower(trim((string) ($bookingStatus . ' ' . $paymentStatus)));

        if (strpos($status, 'batal') !== false || strpos($status, 'refund') !== false || strpos($status, 'gagal') !== false) {
            return array('label' => 'Dibatalkan', 'class' => 'danger', 'key' => 'cancelled');
        }

        if (strpos($status, 'selesai') !== false || strpos($status, 'berhasil') !== false || strpos($status, 'dibayar') !== false || strpos($status, 'lunas') !== false || strpos($status, 'paid') !== false) {
            return array('label' => 'Selesai', 'class' => 'active', 'key' => 'completed');
        }

        if (strpos($status, 'menunggu') !== false || strpos($status, 'pending') !== false) {
            return array('label' => 'Pending', 'class' => 'warning', 'key' => 'pending');
        }

        return array('label' => 'Aktif', 'class' => 'success', 'key' => 'active');
    }

    protected function adminSummaryCardsFromDatabase()
    {
        $data = $this->adminData();
        $customers = (int) $data->value("SELECT COUNT(*) AS value FROM users WHERE Role='customer'");
        $today = (int) $data->value("SELECT COUNT(*) AS value FROM booking b INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal WHERE j.Tanggal=CURDATE()");
        $income = (int) $data->value("SELECT COALESCE(SUM(Jumlah),0) AS value FROM pembayaran WHERE LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid')");
        $fields = (int) $data->value("SELECT COUNT(*) AS value FROM lapangan WHERE LOWER(Status)='aktif' AND deleted_at IS NULL");

        return array(
            array('label'=>'Total Customer','value'=>(string)$customers,'trend'=>'0%','note'=>'data tersimpan','icon'=>'fa-users','accent'=>'lime'),
            array('label'=>'Booking Hari Ini','value'=>(string)$today,'trend'=>'0%','note'=>'hari ini','icon'=>'fa-calendar-days','accent'=>'blue'),
            array('label'=>'Total Pendapatan','value'=>$this->adminRupiah($income),'trend'=>'0%','note'=>'seluruh periode','icon'=>'fa-rupiah-sign','accent'=>'green'),
            array('label'=>'Lapangan Aktif','value'=>(string)$fields,'trend'=>'0','note'=>'data saat ini','icon'=>'fa-volleyball','accent'=>'gold'),
        );
    }

    protected function adminMonthlyRevenueFromDatabase()
    {
        $amounts = array_fill(1, 12, 0);
        $rows = $this->adminData()->rows("SELECT MONTH(COALESCE(Waktu_pembayaran,created_at)) month_number, COALESCE(SUM(Jumlah),0) amount FROM pembayaran WHERE LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid') AND YEAR(COALESCE(Waktu_pembayaran,created_at))=YEAR(CURDATE()) GROUP BY MONTH(COALESCE(Waktu_pembayaran,created_at))");
        foreach ($rows as $row) { $amounts[(int)$row['month_number']] = (int)$row['amount']; }
        $names=array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des');
        $max=max(1,max($amounts)); $result=array();
        foreach($amounts as $month=>$amount){$result[]=array('month'=>$names[$month],'amount'=>$this->adminRupiah($amount),'x'=>round((($month-1)/11)*100,2),'y'=>round(92-(($amount/$max)*82),2));}
        return $result;
    }

    protected function adminBookingStatusFromDatabase()
    {
        $counts=array('Selesai'=>0,'Aktif'=>0,'Pending'=>0,'Dibatalkan'=>0);
        foreach($this->adminBookingRows() as $row){$payload=$this->adminBookingPayload($row['booking_status'],isset($row['payment_status'])?$row['payment_status']:'');$counts[$payload['label']]++;}
        $total=max(1,array_sum($counts));$colors=array('Selesai'=>'lime','Aktif'=>'blue','Pending'=>'gold','Dibatalkan'=>'red');$result=array();
        foreach($counts as $label=>$count){$result[]=array('label'=>$label,'value'=>number_format(($count/$total)*100,0).'%','count'=>(string)$count,'color'=>$colors[$label]);}
        return $result;
    }

    protected function adminBookingsFromDatabase()
    {
        $bookings=array();
        foreach($this->adminBookingRows() as $row){$payload=$this->adminBookingPayload($row['booking_status'],isset($row['payment_status'])?$row['payment_status']:'');$bookings[]=array('id'=>$row['ID_Booking'],'code'=>$row['ID_Booking'],'userId'=>$row['ID_User'],'scheduleId'=>$row['ID_Jadwal'],'field'=>$row['Nama_lapangan'],'user'=>$row['customer_name'],'date'=>$this->adminDate($row['Tanggal']),'time'=>substr($row['Jam_Mulai'],0,5).' - '.substr($row['Jam_Selesai'],0,5),'status'=>$payload['label'],'rawStatus'=>$row['booking_status'],'statusClass'=>$payload['class'],'total'=>$this->adminRupiah($row['Total_harga']));}
        return $bookings;
    }

    protected function adminPopularFieldsFromDatabase()
    {
        $rows=$this->adminData()->rows("SELECT l.Nama_lapangan name, COUNT(b.ID_Booking) booking FROM lapangan l LEFT JOIN jadwal j ON j.ID_Lapangan=l.ID_Lapangan LEFT JOIN booking b ON b.ID_Jadwal=j.ID_Jadwal WHERE l.deleted_at IS NULL GROUP BY l.ID_Lapangan ORDER BY booking DESC LIMIT 5");
        $max=1;foreach($rows as $row){$max=max($max,(int)$row['booking']);}$result=array();
        foreach($rows as $row){$result[]=array('name'=>$row['name'],'booking'=>(string)$row['booking'],'percent'=>round(((int)$row['booking']/$max)*100));}
        return $result;
    }

    protected function adminBottomMetricsFromDatabase()
    {
        $data=$this->adminData();
        $todayIncome=(int)$data->value("SELECT COALESCE(SUM(Jumlah),0) value FROM pembayaran WHERE LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid') AND DATE(COALESCE(Waktu_pembayaran,created_at))=CURDATE()");
        $monthIncome=(int)$data->value("SELECT COALESCE(SUM(Jumlah),0) value FROM pembayaran WHERE LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid') AND YEAR(COALESCE(Waktu_pembayaran,created_at))=YEAR(CURDATE()) AND MONTH(COALESCE(Waktu_pembayaran,created_at))=MONTH(CURDATE())");
        $monthBookings=(int)$data->value("SELECT COUNT(*) value FROM booking WHERE YEAR(Waktu_transaksi)=YEAR(CURDATE()) AND MONTH(Waktu_transaksi)=MONTH(CURDATE())");
        $rating=(float)$data->value("SELECT COALESCE(AVG(Rating),0) value FROM review");
        return array(
            array('label'=>'Pendapatan Hari Ini','value'=>$this->adminRupiah($todayIncome),'trend'=>'0%','note'=>'hari ini','icon'=>'fa-rupiah-sign','accent'=>'green'),
            array('label'=>'Pendapatan Bulan Ini','value'=>$this->adminRupiah($monthIncome),'trend'=>'0%','note'=>'bulan ini','icon'=>'fa-volleyball','accent'=>'indigo'),
            array('label'=>'Total Booking Bulan Ini','value'=>(string)$monthBookings,'trend'=>'0%','note'=>'bulan ini','icon'=>'fa-calendar-days','accent'=>'gold'),
            array('label'=>'Rata-rata Rating','value'=>number_format($rating,1).' / 5','trend'=>'0','note'=>'semua ulasan','icon'=>'fa-calendar-check','accent'=>'purple'),
        );
    }

    protected function adminFieldsFromDatabase()
    {
        $rows=$this->adminData()->rows("SELECT l.*, COUNT(DISTINCT CASE WHEN j.Tanggal=CURDATE() THEN b.ID_Booking END) bookings, COALESCE(AVG(r.Rating),0) rating, COUNT(DISTINCT r.ID_Review) reviews FROM lapangan l LEFT JOIN jadwal j ON j.ID_Lapangan=l.ID_Lapangan LEFT JOIN booking b ON b.ID_Jadwal=j.ID_Jadwal LEFT JOIN review r ON r.ID_Lapangan=l.ID_Lapangan WHERE l.deleted_at IS NULL GROUP BY l.ID_Lapangan ORDER BY l.created_at DESC");
        $fields=array();
        foreach($rows as $row){$status=$row['Status'];$type=strtolower($row['Jenis_olahraga']);$icon=strpos($type,'badminton')!==false?'fa-table-tennis-paddle-ball':(strpos($type,'basket')!==false?'fa-basketball':'fa-futbol');$fields[]=array('id'=>$row['ID_Lapangan'],'ownerId'=>$row['ID_Pemilik'],'name'=>$row['Nama_lapangan'],'type'=>$row['Jenis_olahraga'],'location'=>$row['Lokasi'],'facilities'=>$row['Fasilitas'],'description'=>$row['Deskripsi'],'priceValue'=>(int)$row['Harga'],'price'=>$this->adminRupiah($row['Harga']).'/jam','bookings'=>(int)$row['bookings'],'rating'=>number_format((float)$row['rating'],1),'reviews'=>(int)$row['reviews'],'status'=>$status,'badge'=>strtolower($status)==='aktif'?'success':'warning','progress'=>min(100,(int)$row['bookings']*10),'icon'=>$icon,'accent'=>strtolower($status)==='aktif'?'lime':'gold');}
        return $fields;
    }

    protected function adminReviewsFromDatabase()
    {
        $rows=$this->adminData()->rows("SELECT r.ID_Review,r.Rating,r.Komentar,r.Balasan,r.created_at,u.Nama,l.Nama_lapangan FROM review r INNER JOIN users u ON u.ID_User=r.ID_User INNER JOIN lapangan l ON l.ID_Lapangan=r.ID_Lapangan ORDER BY r.created_at DESC");$result=array();
        foreach($rows as $row){$name=$row['Nama'];$parts=preg_split('/\s+/',trim($name));$initials='';foreach(array_slice($parts,0,2) as $part){$initials.=strtoupper(substr($part,0,1));}$responded=trim((string)$row['Balasan'])!=='';$result[]=array('id'=>$row['ID_Review'],'initials'=>$initials,'user'=>$name,'field'=>$row['Nama_lapangan'],'rating'=>(float)$row['Rating'],'comment'=>$row['Komentar'],'reply'=>$row['Balasan'],'date'=>$this->adminDate(substr($row['created_at'],0,10)),'status'=>$responded?'Ditanggapi':'Belum Ditanggapi','statusClass'=>$responded?'success':'warning','accent'=>'blue');}
        return $result;
    }

    protected function adminReviewStatsFromDatabase()
    {
        $reviews=$this->adminReviewsFromDatabase();$total=count($reviews);$sum=0;$positive=0;$unanswered=0;foreach($reviews as $r){$sum+=$r['rating'];$positive+=$r['rating']>=4?1:0;$unanswered+=$r['status']==='Belum Ditanggapi'?1:0;}$average=$total?$sum/$total:0;
        return array(
            array('label'=>'Total Ulasan','value'=>(string)$total,'trend'=>'0','note'=>'data tersimpan','icon'=>'fa-star','accent'=>'gold','direction'=>'up'),
            array('label'=>'Rating Rata-rata','value'=>number_format($average,1),'trend'=>'0','note'=>'semua ulasan','icon'=>'fa-star','accent'=>'blue','direction'=>'up'),
            array('label'=>'Ulasan Baru','value'=>(string)$total,'trend'=>'0','note'=>'seluruh periode','icon'=>'fa-star','accent'=>'purple','direction'=>'up'),
            array('label'=>'Belum Ditanggapi','value'=>(string)$unanswered,'trend'=>'0','note'=>'perlu tindakan','icon'=>'fa-message','accent'=>'red','direction'=>'down'),
            array('label'=>'Ulasan Positif','value'=>$total?number_format(($positive/$total)*100,0).'%':'0%','trend'=>'0%','note'=>'dari total ulasan','icon'=>'fa-thumbs-up','accent'=>'green','direction'=>'up'),
        );
    }

    protected function adminRatingDistributionFromDatabase()
    {
        $counts=array(5=>0,4=>0,3=>0,2=>0,1=>0);foreach($this->adminReviewsFromDatabase() as $review){$star=max(1,min(5,(int)round($review['rating'])));$counts[$star]++;}$total=max(1,array_sum($counts));$colors=array(5=>'lime',4=>'green',3=>'gold',2=>'orange',1=>'red');$result=array();foreach($counts as $star=>$count){$result[]=array('label'=>$star.' Bintang','percent'=>round(($count/$total)*100),'count'=>$count,'color'=>$colors[$star]);}return $result;
    }

    protected function adminFieldRatingsFromDatabase()
    {
        $rows=$this->adminData()->rows("SELECT l.Nama_lapangan,l.Jenis_olahraga,l.Foto,COALESCE(AVG(r.Rating),0) rating,COUNT(r.ID_Review) reviews FROM lapangan l LEFT JOIN review r ON r.ID_Lapangan=l.ID_Lapangan WHERE l.deleted_at IS NULL GROUP BY l.ID_Lapangan ORDER BY rating DESC");$result=array();foreach($rows as $row){$result[]=array('name'=>$row['Nama_lapangan'],'rating'=>number_format((float)$row['rating'],1),'reviews'=>(int)$row['reviews'],'image'=>$this->adminFieldImage($row['Foto'],$row['Jenis_olahraga']));}return $result;
    }

    protected function adminTransactionsFromDatabase()
    {
        $result=array();foreach($this->adminBookingRows() as $row){if(empty($row['ID_Pembayaran'])){continue;}$payload=$this->adminBookingPayload($row['booking_status'],$row['payment_status']);$created=!empty($row['Waktu_pembayaran'])?$row['Waktu_pembayaran']:$row['payment_created_at'];$name=$row['customer_name'];$parts=preg_split('/\s+/',trim($name));$initials='';foreach(array_slice($parts,0,2) as $p){$initials.=strtoupper(substr($p,0,1));}$result[]=array('id'=>$row['ID_Pembayaran'],'booking'=>$row['ID_Booking'],'field'=>$row['Nama_lapangan'],'user'=>$name,'initials'=>$initials,'phone'=>$row['Nomor_telepon'],'method'=>$row['Metode'],'methodClass'=>$this->adminMethodClass($row['Metode']),'amount'=>$this->adminRupiah($row['Jumlah']),'status'=>$payload['label']==='Selesai'?'Berhasil':$payload['label'],'rawStatus'=>$row['payment_status'],'statusClass'=>$payload['class'],'dateValue'=>substr($created,0,10),'date'=>$this->adminDate(substr($created,0,10)),'time'=>substr($created,11,5),'accent'=>'green');}return $result;
    }

    protected function adminTransactionStatsFromDatabase()
    {
        $rows=$this->adminTransactionsFromDatabase();$income=0;$success=0;$failed=0;$refund=0;foreach($rows as $row){if($row['status']==='Berhasil'){$success++;$income+=(int)preg_replace('/[^0-9]/','',$row['amount']);}elseif(strtolower($row['status'])==='refund'){$refund++;}else{$failed++;}}
        return array(
            array('label'=>'Total Transaksi','value'=>(string)count($rows),'trend'=>'0','note'=>'data tersimpan','icon'=>'fa-wallet','accent'=>'green','direction'=>'up'),
            array('label'=>'Total Pendapatan','value'=>$this->adminRupiah($income),'trend'=>'0%','note'=>'transaksi berhasil','icon'=>'fa-money-check-dollar','accent'=>'blue','direction'=>'up'),
            array('label'=>'Transaksi Berhasil','value'=>(string)$success,'trend'=>'0','note'=>'data tersimpan','icon'=>'fa-circle-check','accent'=>'purple','direction'=>'up'),
            array('label'=>'Transaksi Gagal','value'=>(string)$failed,'trend'=>'0','note'=>'data tersimpan','icon'=>'fa-rectangle-xmark','accent'=>'red','direction'=>'down'),
            array('label'=>'Refund','value'=>(string)$refund,'trend'=>'0','note'=>'data tersimpan','icon'=>'fa-clock-rotate-left','accent'=>'gold','direction'=>'down'),
        );
    }

    protected function adminReportStatsFromDatabase($filters = null)
    {
        $data = $this->adminData();

        if ($filters === null) {
            $income = (int) $data->value("SELECT COALESCE(SUM(Jumlah),0) value FROM pembayaran WHERE LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid')");
            $bookings = (int) $data->value('SELECT COUNT(*) value FROM booking');
            $users = (int) $data->value("SELECT COUNT(*) value FROM users WHERE created_at >= DATE_FORMAT(CURDATE(),'%Y-%m-01')");
            $fields = (int) $data->value("SELECT COUNT(*) value FROM lapangan WHERE LOWER(Status)='aktif' AND deleted_at IS NULL");

            return array(
                array('label' => 'Total Pendapatan', 'value' => $this->adminRupiah($income), 'trend' => '0%', 'note' => 'seluruh periode', 'icon' => 'fa-wallet', 'accent' => 'green'),
                array('label' => 'Total Booking', 'value' => (string) $bookings, 'trend' => '0%', 'note' => 'seluruh periode', 'icon' => 'fa-calendar-check', 'accent' => 'purple'),
                array('label' => 'Total Pengguna Baru', 'value' => (string) $users, 'trend' => '0%', 'note' => 'bulan ini', 'icon' => 'fa-user-plus', 'accent' => 'blue'),
                array('label' => 'Total Lapangan Aktif', 'value' => (string) $fields, 'trend' => '0%', 'note' => 'saat ini', 'icon' => 'fa-table-cells-large', 'accent' => 'gold'),
            );
        }

        $paymentWhere = $this->adminReportPaymentWhere($filters, 'p', 'l');
        $bookingWhere = $this->adminReportBookingWhere($filters, 'b', 'l');
        $fieldWhere = $this->adminReportFieldWhere($filters, 'l');
        $income = (int) $data->value(
            'SELECT COALESCE(SUM(p.Jumlah),0) value FROM pembayaran p INNER JOIN booking b ON b.ID_Booking=p.ID_Booking INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan ' . $paymentWhere['sql'],
            $paymentWhere['types'],
            $paymentWhere['params']
        );
        $bookings = (int) $data->value(
            'SELECT COUNT(*) value FROM booking b INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan ' . $bookingWhere['sql'],
            $bookingWhere['types'],
            $bookingWhere['params']
        );
        $users = (int) $data->value(
            'SELECT COUNT(*) value FROM users WHERE DATE(created_at) BETWEEN ? AND ?',
            'ss',
            array($filters['start'], $filters['end'])
        );
        $fields = (int) $data->value(
            "SELECT COUNT(*) value FROM lapangan l " . $fieldWhere['sql'] . " AND LOWER(l.Status)='aktif'",
            $fieldWhere['types'],
            $fieldWhere['params']
        );

        return array(
            array('label' => 'Total Pendapatan', 'value' => $this->adminRupiah($income), 'trend' => '0%', 'note' => 'periode terpilih', 'icon' => 'fa-wallet', 'accent' => 'green'),
            array('label' => 'Total Booking', 'value' => (string) $bookings, 'trend' => '0%', 'note' => 'periode terpilih', 'icon' => 'fa-calendar-check', 'accent' => 'purple'),
            array('label' => 'Total Pengguna Baru', 'value' => (string) $users, 'trend' => '0%', 'note' => 'periode terpilih', 'icon' => 'fa-user-plus', 'accent' => 'blue'),
            array('label' => 'Total Lapangan Aktif', 'value' => (string) $fields, 'trend' => '0%', 'note' => $filters['field'] === '' ? 'saat ini' : 'filter lapangan', 'icon' => 'fa-table-cells-large', 'accent' => 'gold'),
        );
    }

    protected function adminRevenueReportFromDatabase($filters = null)
    {
        if ($filters === null) {
            $filters = $this->adminReportFilters(array('start' => date('Y-m-d', strtotime('-30 days')), 'end' => date('Y-m-d')));
        }

        $where = $this->adminReportPaymentWhere($filters, 'p', 'l');
        $rows = $this->adminData()->rows(
            'SELECT DATE(COALESCE(p.Waktu_pembayaran,p.created_at)) report_date,SUM(p.Jumlah) amount FROM pembayaran p INNER JOIN booking b ON b.ID_Booking=p.ID_Booking INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan ' . $where['sql'] . ' GROUP BY DATE(COALESCE(p.Waktu_pembayaran,p.created_at)) ORDER BY report_date',
            $where['types'],
            $where['params']
        );

        if (empty($rows)) {
            return array(array('label' => $this->adminReportShortDate($filters['end']), 'amount' => 'Rp0', 'rawAmount' => 0, 'x' => 50, 'y' => 92, 'highlight' => true));
        }

        $max = 0;
        foreach ($rows as $row) {
            $max = max($max, (int) $row['amount']);
        }

        $top = $this->adminNiceMoneyTop($max);
        $start = strtotime($filters['start']);
        $end = strtotime($filters['end']);
        $totalSeconds = max(1, $end - $start);
        $result = array();
        $highlight = 0;
        $best = -1;

        foreach ($rows as $index => $row) {
            $amount = (int) $row['amount'];
            if ($amount > $best) {
                $best = $amount;
                $highlight = $index;
            }

            $pointTime = strtotime($row['report_date']);
            $x = $start === $end ? 50 : round((max(0, min($totalSeconds, $pointTime - $start)) / $totalSeconds) * 100, 2);
            $result[] = array(
                'label' => $this->adminReportShortDate($row['report_date']),
                'amount' => $this->adminRupiah($amount),
                'rawAmount' => $amount,
                'x' => $x,
                'y' => round(92 - (($amount / $top) * 82), 2),
            );
        }

        $result[$highlight]['highlight'] = true;
        return $result;
    }

    protected function adminPaymentReportFromDatabase($filters = null)
    {
        if ($filters === null) {
            $rows = $this->adminData()->rows("SELECT Metode method,SUM(Jumlah) amount FROM pembayaran WHERE LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid') GROUP BY Metode ORDER BY amount DESC");
        } else {
            $where = $this->adminReportPaymentWhere($filters, 'p', 'l');
            $rows = $this->adminData()->rows(
                'SELECT p.Metode method,SUM(p.Jumlah) amount FROM pembayaran p INNER JOIN booking b ON b.ID_Booking=p.ID_Booking INNER JOIN jadwal j ON j.ID_Jadwal=b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan ' . $where['sql'] . ' GROUP BY p.Metode ORDER BY amount DESC',
                $where['types'],
                $where['params']
            );
        }

        if (empty($rows)) {
            return array(array('method' => 'Belum ada pembayaran', 'amount' => 'Rp0', 'percent' => 100, 'color' => 'light'));
        }

        $total = 0;
        foreach ($rows as $row) {
            $total += (int) $row['amount'];
        }

        $colors = array('blue', 'purple', 'teal', 'orange', 'light');
        $result = array();
        foreach ($rows as $index => $row) {
            $result[] = array(
                'method' => $row['method'],
                'amount' => $this->adminRupiah($row['amount']),
                'percent' => $total ? round(((int) $row['amount'] / $total) * 100) : 0,
                'color' => $colors[$index % count($colors)],
            );
        }

        return $result;
    }

    protected function adminFieldBookingReportFromDatabase($filters = null)
    {
        if ($filters === null) {
            $rows = $this->adminData()->rows("SELECT l.Nama_lapangan field,COUNT(b.ID_Booking) value FROM lapangan l LEFT JOIN jadwal j ON j.ID_Lapangan=l.ID_Lapangan LEFT JOIN booking b ON b.ID_Jadwal=j.ID_Jadwal WHERE l.deleted_at IS NULL GROUP BY l.ID_Lapangan ORDER BY value DESC LIMIT 6");
        } else {
            $conditions = array('l.deleted_at IS NULL');
            $types = 'ss';
            $params = array($filters['start'], $filters['end']);

            if ($filters['field'] !== '') {
                $conditions[] = 'l.ID_Lapangan=?';
                $types .= 's';
                $params[] = $filters['field'];
            }

            $rows = $this->adminData()->rows(
                'SELECT l.Nama_lapangan field,COUNT(CASE WHEN DATE(b.Waktu_transaksi) BETWEEN ? AND ? THEN b.ID_Booking END) value FROM lapangan l LEFT JOIN jadwal j ON j.ID_Lapangan=l.ID_Lapangan LEFT JOIN booking b ON b.ID_Jadwal=j.ID_Jadwal WHERE ' . implode(' AND ', $conditions) . ' GROUP BY l.ID_Lapangan ORDER BY value DESC LIMIT 6',
                $types,
                $params
            );
        }

        $max = 0;
        foreach ($rows as $row) {
            $max = max($max, (int) $row['value']);
        }

        $top = $this->adminNiceCountTop($max);
        $result = array();
        foreach ($rows as $row) {
            $value = (int) $row['value'];
            $result[] = array(
                'field' => $row['field'],
                'short' => htmlspecialchars($row['field'], ENT_QUOTES, 'UTF-8'),
                'value' => $value,
                'height' => min(100, round(($value / $top) * 100)),
            );
        }

        return $result;
    }

    protected function adminPaymentMethodsFromDatabase()
    {
        $rows=$this->adminData()->rows('SELECT * FROM metode_pembayaran ORDER BY Nama');$result=array();foreach($rows as $row){$result[]=array('id'=>$row['ID_Metode'],'name'=>$row['Nama'],'description'=>'Metode '.$row['Tipe'].'; biaya admin '.$this->adminRupiah($row['Biaya_admin']),'mark'=>strtoupper(substr($row['Nama'],0,2)),'accent'=>$this->adminMethodClass($row['Nama']),'enabled'=>(bool)$row['Aktif']);}return $result;
    }

    protected function adminBankAccountsFromDatabase()
    {
        $rows=$this->adminData()->rows("SELECT rp.*,p.nama_usaha FROM rekening_pemilik rp INNER JOIN pemilik_lapangan p ON p.ID_Pemilik=rp.ID_Pemilik ORDER BY rp.Utama DESC,rp.created_at DESC");$result=array();foreach($rows as $row){$active=strtolower($row['Status'])==='aktif';$result[]=array('id'=>$row['ID_Rekening'],'ownerId'=>$row['ID_Pemilik'],'bank'=>$row['Nama_bank'],'account'=>$row['Nomor_rekening'],'owner'=>$row['Nama_pemilik'],'status'=>$row['Status'],'statusClass'=>$active?'success':'inactive','accent'=>$this->adminMethodClass($row['Nama_bank']));}return $result;
    }

    protected function adminMethodClass($method)
    {
        $method=strtolower((string)$method);if(strpos($method,'qris')!==false){return 'qris';}if(strpos($method,'dana')!==false){return 'dana';}if(strpos($method,'ovo')!==false){return 'ovo';}return 'bank';
    }

    protected function adminFieldImage($photos, $type)
    {
        $decoded=json_decode((string)$photos,true);if(is_array($decoded)&&!empty($decoded[0])&&strpos($decoded[0],'..')===false){return app_url($decoded[0]);}$type=strtolower((string)$type);if(strpos($type,'badminton')!==false){return 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=240&auto=format&fit=crop';}return 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=240&auto=format&fit=crop';
    }

    protected function summaryCards()
    {
        return $this->adminSummaryCardsFromDatabase();

        return array(
            array(
                'label' => 'Total Customer',
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
        return $this->adminMonthlyRevenueFromDatabase();

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
        return $this->adminBookingStatusFromDatabase();

        return array(
            array('label' => 'Selesai', 'value' => '45%', 'count' => '234', 'color' => 'lime'),
            array('label' => 'Aktif', 'value' => '30%', 'count' => '156', 'color' => 'blue'),
            array('label' => 'Pending', 'value' => '15%', 'count' => '78', 'color' => 'gold'),
            array('label' => 'Dibatalkan', 'value' => '10%', 'count' => '52', 'color' => 'red'),
        );
    }

    protected function recentBookings()
    {
        return $this->adminBookingsFromDatabase();

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
        return $this->adminPopularFieldsFromDatabase();

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
        return $this->adminBottomMetricsFromDatabase();

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
        $this->ensureAdminSchedules();

        return $this->view('Admin/booking', array(
            'title' => 'Manajemen Booking | Arena Sport',
            'activeMenu' => 'booking',
            'userName' => $userName,
            'recentBookings' => $this->recentBookings(),
            'bookingCustomers' => $this->adminData()->rows("SELECT ID_User id,Nama name FROM users WHERE LOWER(Role)='customer' AND LOWER(Status)='aktif' ORDER BY Nama"),
            'availableSchedules' => $this->adminData()->rows("SELECT j.ID_Jadwal id,l.Nama_lapangan field,j.Tanggal date,j.Jam_Mulai start,j.Jam_Selesai end,IF(j.Harga>0,j.Harga,l.Harga) price FROM jadwal j INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan WHERE LOWER(j.Status) IN ('available','tersedia') AND j.Tanggal>=CURDATE() AND NOT EXISTS (SELECT 1 FROM booking b WHERE b.ID_Jadwal=j.ID_Jadwal AND LOWER(TRIM(b.Status)) NOT IN ('dibatalkan','cancelled','batal')) AND l.deleted_at IS NULL ORDER BY j.Tanggal,j.Jam_Mulai"),
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
            'fields' => $this->adminFieldsFromDatabase(),
            'fieldOwners' => $this->adminData()->rows("SELECT p.ID_Pemilik id,CONCAT(p.nama_usaha,' - ',u.Nama) name FROM pemilik_lapangan p INNER JOIN users u ON u.ID_User=p.ID_User WHERE LOWER(u.Status)='aktif' ORDER BY p.nama_usaha"),
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
        $customers = $this->adminCustomers();

        return $this->view('Admin/users', array(
            'title' => 'Kelola Customer | Arena Sport',
            'activeMenu' => 'user',
            'userName' => $userName,
            'users' => $customers,
            'userStats' => $this->adminCustomerStats($customers),
        ), 'layouts/admin');
    }

    public function pemilikLapangan()
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

        header('Location: ' . app_url('admin/users'));
        exit;
    }

    protected function adminCustomers()
    {
        try {
            $connection = Database::connection();
        } catch (\Throwable $exception) {
            return array();
        }

        $table = $this->adminUserTable($connection);
        if ($table === '') {
            return array();
        }

        $columns = $this->adminTableColumns($connection, $table);
        if (empty($columns)) {
            return array();
        }

        $idColumn = $this->firstAvailableColumn($columns, array('ID_User', 'id', 'user_id'));
        $nameColumn = $this->firstAvailableColumn($columns, array('Nama', 'name', 'nama'));
        $emailColumn = $this->firstAvailableColumn($columns, array('Email', 'email'));
        $phoneColumn = $this->firstAvailableColumn($columns, array('Nomor_telepon', 'No_Telepon', 'phone', 'telepon'));
        $roleColumn = $this->firstAvailableColumn($columns, array('Role', 'role', 'role_user'));
        $statusColumn = $this->firstAvailableColumn($columns, array('Status', 'status', 'status_user'));
        $registeredColumn = $this->firstAvailableColumn($columns, array('created_at', 'Created_at', 'tanggal_daftar', 'Tanggal_daftar', 'registered_at'));

        if ($nameColumn === '' || $emailColumn === '') {
            return array();
        }

        $select = array(
            $idColumn !== '' ? 'u.`' . $idColumn . '` AS id' : "'' AS id",
            'u.`' . $nameColumn . '` AS name',
            'u.`' . $emailColumn . '` AS email',
            $phoneColumn !== '' ? 'u.`' . $phoneColumn . '` AS phone' : "'' AS phone",
            $roleColumn !== '' ? 'u.`' . $roleColumn . '` AS role' : "'Customer' AS role",
            $statusColumn !== '' ? 'u.`' . $statusColumn . '` AS status' : "'Aktif' AS status",
            $registeredColumn !== '' ? 'u.`' . $registeredColumn . '` AS registered' : 'NULL AS registered',
        );

        $ownerJoin = '';
        if ($idColumn !== '' && $this->adminTableExists($connection, 'pemilik_lapangan')) {
            $select[] = 'p.`ID_Pemilik` AS owner_id';
            $ownerJoin = ' LEFT JOIN `pemilik_lapangan` p ON p.`ID_User` = u.`' . $idColumn . '`';
        } else {
            $select[] = 'NULL AS owner_id';
        }

        $orderColumn = $registeredColumn !== '' ? $registeredColumn : $nameColumn;
        $where = $roleColumn !== '' ? " WHERE LOWER(u.`" . $roleColumn . "`) <> 'admin'" : '';
        $sql = 'SELECT ' . implode(', ', $select) . ' FROM `' . $table . '` u' . $ownerJoin . $where . ' ORDER BY u.`' . $orderColumn . '` ASC';
        $result = mysqli_query($connection, $sql);

        if (!$result) {
            return array();
        }

        $users = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $role = $this->adminDisplayRole(isset($row['role']) ? $row['role'] : '', !empty($row['owner_id']));
            $status = $this->adminDisplayStatus(isset($row['status']) ? $row['status'] : '');

            $users[] = array(
                'id' => isset($row['id']) ? $row['id'] : '',
                'name' => isset($row['name']) && trim((string) $row['name']) !== '' ? $row['name'] : 'Tanpa Nama',
                'email' => isset($row['email']) ? $row['email'] : '',
                'phone' => isset($row['phone']) && trim((string) $row['phone']) !== '' ? $row['phone'] : '-',
                'role' => $role,
                'roleClass' => $this->adminRoleClass($role),
                'status' => $status,
                'statusClass' => $this->adminStatusClass($status),
                'registered' => $this->adminFormatDate(isset($row['registered']) ? $row['registered'] : ''),
            );
        }

        return $users;
    }

    protected function adminCustomerStats(array $users)
    {
        $stats = array(
            'total' => count($users),
            'active' => 0,
            'owners' => 0,
            'inactive' => 0,
        );

        foreach ($users as $user) {
            if (isset($user['status']) && $user['status'] === 'Aktif') {
                $stats['active']++;
            }

            if (isset($user['role']) && $user['role'] === 'Pemilik') {
                $stats['owners']++;
            }

            if (isset($user['status']) && $user['status'] !== 'Aktif') {
                $stats['inactive']++;
            }
        }

        return $stats;
    }

    protected function adminUserTable($connection)
    {
        if ($this->adminTableExists($connection, 'users')) {
            return 'users';
        }

        if ($this->adminTableExists($connection, 'user')) {
            return 'user';
        }

        return '';
    }

    protected function adminTableExists($connection, $table)
    {
        $safeTable = mysqli_real_escape_string($connection, $table);
        $result = mysqli_query($connection, "SHOW TABLES LIKE '$safeTable'");

        return $result && mysqli_num_rows($result) > 0;
    }

    protected function adminTableColumns($connection, $table)
    {
        $columns = array();
        $result = mysqli_query($connection, 'SHOW COLUMNS FROM `' . $table . '`');

        if (!$result) {
            return $columns;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            if (isset($row['Field'])) {
                $columns[] = $row['Field'];
            }
        }

        return $columns;
    }

    protected function firstAvailableColumn(array $columns, array $candidates)
    {
        foreach ($candidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }

        return '';
    }

    protected function adminDisplayRole($role, $isOwner)
    {
        $normalized = strtolower(trim((string) $role));

        if ($isOwner || in_array($normalized, array('pemilik', 'pemilik lapangan', 'owner', 'mitra'), true)) {
            return 'Pemilik';
        }

        if (in_array($normalized, array('admin', 'administrator', 'superadmin'), true)) {
            return 'Admin';
        }

        return 'Customer';
    }

    protected function adminDisplayStatus($status)
    {
        $normalized = strtolower(trim((string) $status));

        if ($normalized === '' || in_array($normalized, array('aktif', 'active', '1', 'verified'), true)) {
            return 'Aktif';
        }

        return 'Nonaktif';
    }

    protected function adminRoleClass($role)
    {
        if ($role === 'Pemilik') {
            return 'info';
        }

        if ($role === 'Admin') {
            return 'blue';
        }

        return 'success';
    }

    protected function adminStatusClass($status)
    {
        return $status === 'Aktif' ? 'success' : 'warning';
    }

    protected function adminFormatDate($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '-';
        }

        $timestamp = strtotime($value);
        if (!$timestamp) {
            return $value;
        }

        $months = array(
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        );

        return date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    }

    public function ulasan()
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
        $reviews = $this->adminReviews();

        return $this->view('Admin/ulasan', array(
            'title' => 'Ulasan & Rating | Arena Sport',
            'activeMenu' => 'ulasan',
            'userName' => $userName,
            'userRole' => $role,
            'reviewStats' => $this->adminReviewStats(),
            'reviews' => $reviews,
            'ratingDistribution' => $this->ratingDistribution(),
            'fieldRatings' => $this->fieldRatings(),
            'latestReviews' => array_slice($reviews, 0, 3),
        ), 'layouts/admin');
    }

    protected function adminReviewStats()
    {
        return $this->adminReviewStatsFromDatabase();

        return array(
            array('label' => 'Total Ulasan', 'value' => '128', 'trend' => '12', 'note' => 'dari bulan lalu', 'icon' => 'fa-star', 'accent' => 'gold', 'direction' => 'up'),
            array('label' => 'Rating Rata-rata', 'value' => '4.6', 'trend' => '0.2', 'note' => 'dari bulan lalu', 'icon' => 'fa-star', 'accent' => 'blue', 'direction' => 'up'),
            array('label' => 'Ulasan Baru', 'value' => '18', 'trend' => '6', 'note' => 'dari bulan lalu', 'icon' => 'fa-star', 'accent' => 'purple', 'direction' => 'up'),
            array('label' => 'Belum Ditanggapi', 'value' => '6', 'trend' => '2', 'note' => 'dari bulan lalu', 'icon' => 'fa-message', 'accent' => 'red', 'direction' => 'down'),
            array('label' => 'Ulasan Positif', 'value' => '92%', 'trend' => '5%', 'note' => 'dari bulan lalu', 'icon' => 'fa-thumbs-up', 'accent' => 'green', 'direction' => 'up'),
        );
    }

    protected function adminReviews()
    {
        return $this->adminReviewsFromDatabase();

        return array(
            array(
                'initials' => 'AF',
                'user' => 'Ahmad Fauzi',
                'field' => 'Arena Futsal Parepare',
                'rating' => 5.0,
                'comment' => 'Lapangan bagus, bersih dan nyaman. Pelayanan juga ramah!',
                'date' => '15 Mei 2024',
                'status' => 'Ditanggapi',
                'statusClass' => 'success',
                'accent' => 'blue',
            ),
            array(
                'initials' => 'SA',
                'user' => 'Siti Aminah',
                'field' => 'Mini Soccer Victory',
                'rating' => 4.5,
                'comment' => 'Perlengkapan lumayan baik, cocok untuk bermain futsal.',
                'date' => '14 Mei 2024',
                'status' => 'Belum Ditanggapi',
                'statusClass' => 'warning',
                'accent' => 'green',
            ),
            array(
                'initials' => 'BS',
                'user' => 'Budi Santoso',
                'field' => 'Lapangan Badminton Center',
                'rating' => 5.0,
                'comment' => 'Fasilitas lengkap dan terawat dengan baik.',
                'date' => '12 Mei 2024',
                'status' => 'Ditanggapi',
                'statusClass' => 'success',
                'accent' => 'purple',
            ),
            array(
                'initials' => 'DP',
                'user' => 'Dinda Putri',
                'field' => 'Basket Ball Center',
                'rating' => 3.5,
                'comment' => 'Permukaan lapangan agak licin, tapi overall oke.',
                'date' => '10 Mei 2024',
                'status' => 'Belum Ditanggapi',
                'statusClass' => 'warning',
                'accent' => 'gold',
            ),
            array(
                'initials' => 'AR',
                'user' => 'Andri Rahman',
                'field' => 'Arena Basket Ball Court',
                'rating' => 5.0,
                'comment' => 'Tempat luas dan parkir mudah.',
                'date' => '08 Mei 2024',
                'status' => 'Ditanggapi',
                'statusClass' => 'success',
                'accent' => 'teal',
            ),
        );
    }

    protected function ratingDistribution()
    {
        return $this->adminRatingDistributionFromDatabase();

        return array(
            array('label' => '5 Bintang', 'percent' => 72, 'count' => 92, 'color' => 'lime'),
            array('label' => '4 Bintang', 'percent' => 20, 'count' => 26, 'color' => 'green'),
            array('label' => '3 Bintang', 'percent' => 5, 'count' => 6, 'color' => 'gold'),
            array('label' => '2 Bintang', 'percent' => 2, 'count' => 2, 'color' => 'orange'),
            array('label' => '1 Bintang', 'percent' => 1, 'count' => 1, 'color' => 'red'),
        );
    }

    protected function fieldRatings()
    {
        return $this->adminFieldRatingsFromDatabase();

        return array(
            array('name' => 'Arena Futsal Parepare', 'rating' => '4.7', 'reviews' => 56, 'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=240&auto=format&fit=crop'),
            array('name' => 'Mini Soccer Victory', 'rating' => '4.6', 'reviews' => 34, 'image' => 'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?q=80&w=240&auto=format&fit=crop'),
            array('name' => 'Lapangan Badminton Center', 'rating' => '4.5', 'reviews' => 22, 'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=240&auto=format&fit=crop'),
            array('name' => 'Basket Ball Center', 'rating' => '4.2', 'reviews' => 16, 'image' => 'https://images.unsplash.com/photo-1546519638-68711109d298?q=80&w=240&auto=format&fit=crop'),
            array('name' => 'Arena Basket Ball Court', 'rating' => '4.3', 'reviews' => 14, 'image' => 'https://images.unsplash.com/photo-1521093721353-fcc2b798fbd5?q=80&w=240&auto=format&fit=crop'),
        );
    }

    public function transaksi()
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

        return $this->view('Admin/transaksi', array(
            'title' => 'Transaksi | Arena Sport',
            'activeMenu' => 'transaksi',
            'userName' => $userName,
            'userRole' => $role,
            'searchPlaceholder' => 'Cari transaksi, pengguna, lapangan...',
            'transactionStats' => $this->transactionStats(),
            'transactions' => $this->transactions(),
        ), 'layouts/admin');
    }

    protected function transactionStats()
    {
        return $this->adminTransactionStatsFromDatabase();

        return array(
            array('label' => 'Total Transaksi', 'value' => '362', 'trend' => '23', 'note' => 'dari minggu lalu', 'icon' => 'fa-wallet', 'accent' => 'green', 'direction' => 'up'),
            array('label' => 'Total Pendapatan', 'value' => 'Rp32.450.000', 'trend' => '15%', 'note' => 'dari minggu lalu', 'icon' => 'fa-money-check-dollar', 'accent' => 'blue', 'direction' => 'up'),
            array('label' => 'Transaksi Berhasil', 'value' => '348', 'trend' => '21', 'note' => 'dari minggu lalu', 'icon' => 'fa-circle-check', 'accent' => 'purple', 'direction' => 'up'),
            array('label' => 'Transaksi Gagal', 'value' => '14', 'trend' => '6', 'note' => 'dari minggu lalu', 'icon' => 'fa-rectangle-xmark', 'accent' => 'red', 'direction' => 'down'),
            array('label' => 'Refund', 'value' => '5', 'trend' => '1', 'note' => 'dari minggu lalu', 'icon' => 'fa-clock-rotate-left', 'accent' => 'gold', 'direction' => 'down'),
        );
    }

    protected function transactions()
    {
        return $this->adminTransactionsFromDatabase();

        return array(
            array(
                'id' => 'TRX-2024-05-001',
                'booking' => 'BK-2024-05-1021',
                'field' => 'Arena Futsal Parepare',
                'user' => 'Ahmad Fauzi',
                'initials' => 'AF',
                'phone' => '081234567890',
                'method' => 'VA BCA',
                'methodClass' => 'bca',
                'amount' => 'Rp100.000',
                'status' => 'Berhasil',
                'statusClass' => 'success',
                'date' => '15 Mei 2024',
                'time' => '10:30',
                'accent' => 'green',
            ),
            array(
                'id' => 'TRX-2024-05-002',
                'booking' => 'BK-2024-05-1020',
                'field' => 'Mini Soccer Victory',
                'user' => 'Siti Aminah',
                'initials' => 'SA',
                'phone' => '082345678901',
                'method' => 'GoPay',
                'methodClass' => 'gopay',
                'amount' => 'Rp120.000',
                'status' => 'Berhasil',
                'statusClass' => 'success',
                'date' => '15 Mei 2024',
                'time' => '09:15',
                'accent' => 'lime',
            ),
            array(
                'id' => 'TRX-2024-05-003',
                'booking' => 'BK-2024-05-1019',
                'field' => 'Lapangan Badminton Center',
                'user' => 'Budi Santoso',
                'initials' => 'BS',
                'phone' => '083456789012',
                'method' => 'GoPay',
                'methodClass' => 'gopay',
                'amount' => 'Rp150.000',
                'status' => 'Berhasil',
                'statusClass' => 'success',
                'date' => '14 Mei 2024',
                'time' => '21:45',
                'accent' => 'purple',
            ),
            array(
                'id' => 'TRX-2024-05-004',
                'booking' => 'BK-2024-05-1018',
                'field' => 'Basket Ball Center',
                'user' => 'Dinda Putri',
                'initials' => 'DP',
                'phone' => '084567890123',
                'method' => 'DANA',
                'methodClass' => 'dana',
                'amount' => 'Rp250.000',
                'status' => 'Gagal',
                'statusClass' => 'danger',
                'date' => '14 Mei 2024',
                'time' => '20:10',
                'accent' => 'gold',
            ),
            array(
                'id' => 'TRX-2024-05-005',
                'booking' => 'BK-2024-05-1017',
                'field' => 'Arena Basket Ball Court',
                'user' => 'Andri Rahman',
                'initials' => 'AR',
                'phone' => '085678901234',
                'method' => 'VA BCA',
                'methodClass' => 'bca',
                'amount' => 'Rp100.000',
                'status' => 'Berhasil',
                'statusClass' => 'success',
                'date' => '14 Mei 2024',
                'time' => '18:55',
                'accent' => 'teal',
            ),
            array(
                'id' => 'TRX-2024-05-006',
                'booking' => 'BK-2024-05-1016',
                'field' => 'Lapangan Badminton Center',
                'user' => 'Nur Aisyah',
                'initials' => 'NA',
                'phone' => '081112223333',
                'method' => 'OVO',
                'methodClass' => 'ovo',
                'amount' => 'Rp80.000',
                'status' => 'Berhasil',
                'statusClass' => 'success',
                'date' => '13 Mei 2024',
                'time' => '17:20',
                'accent' => 'slate',
            ),
            array(
                'id' => 'TRX-2024-05-007',
                'booking' => 'BK-2024-05-1015',
                'field' => 'Mini Soccer Victory',
                'user' => 'M. Rizky',
                'initials' => 'MR',
                'phone' => '082233445566',
                'method' => 'DANA',
                'methodClass' => 'dana',
                'amount' => 'Rp120.000',
                'status' => 'Refund',
                'statusClass' => 'refund',
                'date' => '13 Mei 2024',
                'time' => '16:05',
                'accent' => 'red',
            ),
            array(
                'id' => 'TRX-2024-05-008',
                'booking' => 'BK-2024-05-1014',
                'field' => 'Arena Futsal Parepare',
                'user' => 'Irfan Lestari',
                'initials' => 'IL',
                'phone' => '083344556677',
                'method' => 'VA Mandiri',
                'methodClass' => 'mandiri',
                'amount' => 'Rp100.000',
                'status' => 'Berhasil',
                'statusClass' => 'success',
                'date' => '12 Mei 2024',
                'time' => '15:40',
                'accent' => 'gray',
            ),
        );
    }

    public function laporan()
    {
        $admin = $this->requireAdmin();
        $reportFields = $this->adminReportFieldOptions();
        $reportFilters = $this->adminNormalizeReportField($this->adminReportFilters($_GET), $reportFields);
        $revenueReportPoints = $this->revenueReportPoints($reportFilters);
        $fieldBookingReport = $this->fieldBookingReport($reportFilters);

        return $this->view('Admin/laporan', array(
            'title' => 'Laporan | Arena Sport',
            'activeMenu' => 'laporan',
            'userName' => $admin['name'],
            'userRole' => $admin['role'],
            'searchPlaceholder' => 'Cari laporan...',
            'reportFilters' => $reportFilters,
            'reportFields' => $reportFields,
            'reportExportQuery' => $this->adminReportQuery($reportFilters),
            'reportStats' => $this->reportStats($reportFilters),
            'revenueReportPoints' => $revenueReportPoints,
            'revenueAxisLabels' => $this->adminRevenueAxisLabels($revenueReportPoints),
            'revenueDateTicks' => $this->adminReportDateTicks($reportFilters),
            'paymentReport' => $this->paymentReport($reportFilters),
            'fieldBookingReport' => $fieldBookingReport,
            'fieldBookingAxisLabels' => $this->adminBookingAxisLabels($fieldBookingReport),
            'reportDownloads' => $this->reportDownloads(),
        ), 'layouts/admin');
    }

    protected function reportStats($filters = null)
    {
        return $this->adminReportStatsFromDatabase($filters);

        return array(
            array('label' => 'Total Pendapatan', 'value' => 'Rp32.450.000', 'trend' => '15%', 'note' => 'dari periode sebelumnya', 'icon' => 'fa-wallet', 'accent' => 'green'),
            array('label' => 'Total Booking', 'value' => '362', 'trend' => '18%', 'note' => 'dari periode sebelumnya', 'icon' => 'fa-calendar-check', 'accent' => 'purple'),
            array('label' => 'Total Pengguna Baru', 'value' => '24', 'trend' => '20%', 'note' => 'dari periode sebelumnya', 'icon' => 'fa-user-plus', 'accent' => 'blue'),
            array('label' => 'Total Lapangan Aktif', 'value' => '24', 'trend' => '0%', 'note' => 'dari periode sebelumnya', 'icon' => 'fa-table-cells-large', 'accent' => 'gold'),
        );
    }

    protected function revenueReportPoints($filters = null)
    {
        return $this->adminRevenueReportFromDatabase($filters);

        return array(
            array('label' => '1 Mei', 'amount' => 'Rp300.000', 'x' => 0, 'y' => 92),
            array('label' => '4 Mei', 'amount' => 'Rp800.000', 'x' => 10, 'y' => 80),
            array('label' => '6 Mei', 'amount' => 'Rp850.000', 'x' => 17, 'y' => 78),
            array('label' => '8 Mei', 'amount' => 'Rp1.400.000', 'x' => 24, 'y' => 65),
            array('label' => '10 Mei', 'amount' => 'Rp1.200.000', 'x' => 31, 'y' => 69),
            array('label' => '12 Mei', 'amount' => 'Rp850.000', 'x' => 38, 'y' => 79),
            array('label' => '14 Mei', 'amount' => 'Rp2.000.000', 'x' => 44, 'y' => 51),
            array('label' => '15 Mei', 'amount' => 'Rp2.200.000', 'x' => 51, 'y' => 47),
            array('label' => '15 Mei', 'amount' => 'Rp3.450.000', 'x' => 57, 'y' => 22, 'highlight' => true),
            array('label' => '19 Mei', 'amount' => 'Rp2.500.000', 'x' => 64, 'y' => 41),
            array('label' => '20 Mei', 'amount' => 'Rp2.300.000', 'x' => 69, 'y' => 46),
            array('label' => '22 Mei', 'amount' => 'Rp2.700.000', 'x' => 75, 'y' => 36),
            array('label' => '25 Mei', 'amount' => 'Rp2.000.000', 'x' => 82, 'y' => 51),
            array('label' => '27 Mei', 'amount' => 'Rp2.750.000', 'x' => 88, 'y' => 34),
            array('label' => '29 Mei', 'amount' => 'Rp3.300.000', 'x' => 94, 'y' => 23),
            array('label' => '31 Mei', 'amount' => 'Rp4.100.000', 'x' => 100, 'y' => 6),
        );
    }

    protected function paymentReport($filters = null)
    {
        return $this->adminPaymentReportFromDatabase($filters);

        return array(
            array('method' => 'VA BCA', 'amount' => 'Rp14.602.500', 'percent' => 45, 'color' => 'blue'),
            array('method' => 'OVO', 'amount' => 'Rp8.112.500', 'percent' => 25, 'color' => 'purple'),
            array('method' => 'GoPay', 'amount' => 'Rp4.867.500', 'percent' => 15, 'color' => 'teal'),
            array('method' => 'DANA', 'amount' => 'Rp3.245.000', 'percent' => 10, 'color' => 'orange'),
            array('method' => 'Lainnya', 'amount' => 'Rp1.622.500', 'percent' => 5, 'color' => 'light'),
        );
    }

    protected function fieldBookingReport($filters = null)
    {
        return $this->adminFieldBookingReportFromDatabase($filters);

        return array(
            array('field' => 'Arena Futsal Parepare', 'short' => 'Arena Futsal<br>Parepare', 'value' => 120, 'height' => 100),
            array('field' => 'Badminton Center', 'short' => 'Badminton<br>Center', 'value' => 85, 'height' => 71),
            array('field' => 'Mini Soccer Victory', 'short' => 'Mini Soccer<br>Victory', 'value' => 78, 'height' => 65),
            array('field' => 'Basketball Court', 'short' => 'Basketball<br>Court', 'value' => 45, 'height' => 38),
            array('field' => 'Lapangan Tenis', 'short' => 'Lapangan<br>Tenis', 'value' => 34, 'height' => 28),
            array('field' => 'Arena Basket Ball Court', 'short' => 'Arena Basket<br>Ball Court', 'value' => 22, 'height' => 18),
        );
    }

    protected function reportDownloads()
    {
        return array(
            array('title' => 'Laporan Pendapatan', 'description' => 'Ringkasan pendapatan dan transaksi', 'icon' => 'fa-file-invoice-dollar', 'type' => 'transaksi'),
            array('title' => 'Laporan Booking', 'description' => 'Ringkasan data booking', 'icon' => 'fa-table-cells-large', 'type' => 'booking'),
            array('title' => 'Laporan Pengguna', 'description' => 'Ringkasan data pengguna', 'icon' => 'fa-address-card', 'type' => 'users'),
            array('title' => 'Laporan Lapangan', 'description' => 'Ringkasan data lapangan', 'icon' => 'fa-file-lines', 'type' => 'lapangan'),
        );
    }

    public function pengaturan()
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
        $activeSettingTab = isset($_GET['tab']) ? strtolower(trim((string) $_GET['tab'])) : 'akun';
        $allowedSettingTabs = array('umum', 'notifikasi', 'pembayaran', 'keamanan', 'akun');

        if (!in_array($activeSettingTab, $allowedSettingTabs, true)) {
            $activeSettingTab = 'umum';
        }

        return $this->view('Admin/pengaturan', array(
            'title' => 'Pengaturan | Arena Sport',
            'activeMenu' => 'pengaturan',
            'userName' => $userName,
            'userRole' => $role,
            'searchPlaceholder' => 'Cari sesuatu...',
            'activeSettingTab' => $activeSettingTab,
            'settingTabs' => $this->settingTabs(),
            'systemInfo' => $this->systemInformation(),
            'generalSettings' => $this->generalSettings(),
            'notificationChannels' => $this->notificationChannels(),
            'notificationTypes' => $this->notificationTypes(),
            'notificationPreviews' => $this->notificationPreviews(),
            'adminPaymentMethods' => $this->adminPaymentMethods(),
            'adminPaymentSettings' => $this->adminPaymentSettings(),
            'adminBankAccounts' => $this->adminBankAccounts(),
            'securitySettings' => $this->securitySettings(),
            'securityActivities' => $this->securityActivities(),
            'activeSessions' => $this->activeSessions(),
            'adminAccountProfile' => $this->adminAccountProfile($userName),
            'adminLoginSettings' => $this->adminLoginSettings(),
            'adminLoginActivity' => $this->adminLoginActivity(),
            'adminAccessRights' => $this->adminAccessRights(),
            'adminActiveDevices' => $this->adminActiveDevices(),
            'adminPreferences' => $this->adminPreferences(),
            'bankOwners' => $this->adminData()->rows("SELECT p.ID_Pemilik id,CONCAT(p.nama_usaha,' - ',u.Nama) name FROM pemilik_lapangan p INNER JOIN users u ON u.ID_User=p.ID_User ORDER BY p.nama_usaha"),
        ), 'layouts/admin');
    }

    protected function settingTabs()
    {
        return array(
            array('key' => 'umum', 'label' => 'Umum', 'icon' => 'fa-gear'),
            array('key' => 'notifikasi', 'label' => 'Notifikasi', 'icon' => 'fa-bell'),
            array('key' => 'pembayaran', 'label' => 'Pembayaran', 'icon' => 'fa-credit-card'),
            array('key' => 'keamanan', 'label' => 'Keamanan', 'icon' => 'fa-shield-halved'),
            array('key' => 'akun', 'label' => 'Akun', 'icon' => 'fa-id-card'),
        );
    }

    protected function generalSettings()
    {
        $preferences = $this->adminPreferences();
        return array(
            array('key' => 'maintenance_mode', 'label' => 'Maintenance Mode', 'description' => 'Aktifkan mode maintenance (aplikasi tidak dapat diakses user)', 'enabled' => $preferences['maintenance_mode'] === '1'),
            array('key' => 'user_registration', 'label' => 'Registrasi User', 'description' => 'Izinkan user baru untuk mendaftar', 'enabled' => $preferences['user_registration'] === '1'),
        );
    }

    protected function notificationChannels()
    {
        $preferences = $this->adminPreferences();
        return array(
            array('key' => 'notification_in_app', 'label' => 'Notifikasi In-App', 'description' => 'Terima notifikasi melalui aplikasi Arena Sport.', 'icon' => 'fa-bell', 'accent' => 'green', 'enabled' => $preferences['notification_in_app'] === '1'),
            array('key' => 'notification_email', 'label' => 'Email', 'description' => 'Terima notifikasi melalui email yang terdaftar.', 'icon' => 'fa-envelope', 'accent' => 'green', 'enabled' => $preferences['notification_email'] === '1'),
        );
    }

    protected function notificationTypes()
    {
        $preferences = $this->adminPreferences();
        return array(
            array('key' => 'notification_new_booking', 'label' => 'Booking Baru', 'description' => 'Notifikasi ketika ada booking baru pada lapangan Anda.', 'enabled' => $preferences['notification_new_booking'] === '1'),
            array('key' => 'notification_confirmed', 'label' => 'Booking Dikonfirmasi', 'description' => 'Notifikasi ketika booking dikonfirmasi oleh admin.', 'enabled' => $preferences['notification_confirmed'] === '1'),
            array('key' => 'notification_cancelled', 'label' => 'Booking Dibatalkan', 'description' => 'Notifikasi ketika booking dibatalkan oleh pengguna.', 'enabled' => $preferences['notification_cancelled'] === '1'),
            array('key' => 'notification_reminder', 'label' => 'Pengingat Booking', 'description' => 'Notifikasi pengingat sebelum waktu booking dimulai.', 'enabled' => $preferences['notification_reminder'] === '1'),
            array('key' => 'notification_review', 'label' => 'Ulasan & Rating Baru', 'description' => 'Notifikasi ketika ada ulasan atau rating baru diberikan.', 'enabled' => $preferences['notification_review'] === '1'),
            array('key' => 'notification_promo', 'label' => 'Promo & Informasi', 'description' => 'Notifikasi tentang promo, fitur baru, dan informasi penting.', 'enabled' => $preferences['notification_promo'] === '1'),
            array('key' => 'notification_security', 'label' => 'Sistem & Keamanan', 'description' => 'Notifikasi terkait keamanan akun dan aktivitas sistem.', 'enabled' => $preferences['notification_security'] === '1'),
        );
    }

    protected function notificationPreviews()
    {
        $rows = $this->adminData()->rows('SELECT Judul, Pesan, Tipe, created_at FROM notifikasi ORDER BY created_at DESC LIMIT 6');
        $previews = array();

        foreach ($rows as $row) {
            $previews[] = array('title' => $row['Judul'], 'description' => $row['Pesan'], 'time' => $this->adminDate(substr($row['created_at'], 0, 10)), 'icon' => 'fa-bell', 'accent' => strtolower($row['Tipe']) === 'error' ? 'red' : 'green');
        }

        return $previews;

        return array(
            array('title' => 'Booking Baru', 'description' => 'Booking baru untuk lapangan Futsal A pada 16 Juni 2024, 19:00 WIB.', 'time' => '2 menit lalu', 'icon' => 'fa-calendar-days', 'accent' => 'green'),
            array('title' => 'Booking Dikonfirmasi', 'description' => 'Booking Anda untuk lapangan Badminton 1 telah dikonfirmasi.', 'time' => '15 menit lalu', 'icon' => 'fa-check', 'accent' => 'blue'),
            array('title' => 'Booking Dibatalkan', 'description' => 'Booking untuk lapangan Tennis Court pada 15 Juni 2024 dibatalkan.', 'time' => '1 jam lalu', 'icon' => 'fa-xmark', 'accent' => 'gold'),
            array('title' => 'Pengingat Booking', 'description' => 'Booking Anda untuk lapangan Futsal B akan dimulai dalam 30 menit.', 'time' => '30 menit lalu', 'icon' => 'fa-bell', 'accent' => 'gold'),
            array('title' => 'Ulasan & Rating Baru', 'description' => 'Ada ulasan baru untuk lapangan Futsal A dari Budi Santoso.', 'time' => '2 jam lalu', 'icon' => 'fa-star', 'accent' => 'purple'),
            array('title' => 'Promo & Informasi', 'description' => 'Diskon 20% untuk semua lapangan di akhir pekan!', 'time' => '3 jam lalu', 'icon' => 'fa-bullhorn', 'accent' => 'teal'),
            array('title' => 'Sistem & Keamanan', 'description' => 'Login baru terdeteksi di perangkat Chrome, Windows.', 'time' => '5 jam lalu', 'icon' => 'fa-shield-halved', 'accent' => 'red'),
        );
    }

    protected function adminPaymentMethods()
    {
        return $this->adminPaymentMethodsFromDatabase();

        return array(
            array('name' => 'Transfer Bank', 'description' => 'Pembayaran melalui transfer ke rekening bank.', 'mark' => 'VA', 'accent' => 'bank', 'enabled' => true),
            array('name' => 'E-Wallet (OVO)', 'description' => 'Pembayaran melalui OVO.', 'mark' => 'O', 'accent' => 'ovo', 'enabled' => true),
            array('name' => 'E-Wallet (GoPay)', 'description' => 'Pembayaran melalui GoPay.', 'mark' => 'GP', 'accent' => 'gopay', 'enabled' => true),
            array('name' => 'E-Wallet (DANA)', 'description' => 'Pembayaran melalui DANA.', 'mark' => 'DN', 'accent' => 'dana', 'enabled' => true),
            array('name' => 'Virtual Account', 'description' => 'Pembayaran melalui Virtual Account.', 'mark' => 'VA', 'accent' => 'virtual', 'enabled' => true),
        );
    }

    protected function adminPaymentSettings()
    {
        $preferences = $this->adminPreferences();
        return array(
            array('key' => 'payment_timeout', 'label' => 'Batas Waktu Pembayaran', 'description' => 'Batas waktu maksimal pembayaran sebelum booking dibatalkan otomatis.', 'type' => 'select', 'value' => $preferences['payment_timeout'], 'options' => array('30 Menit', '60 Menit', '90 Menit')),
            array('key' => 'admin_fee', 'label' => 'Biaya Admin (Persentase)', 'description' => 'Persentase biaya admin yang dikenakan pada setiap transaksi.', 'type' => 'select', 'value' => $preferences['admin_fee'], 'options' => array('1 %', '2,5 %', '5 %')),
            array('key' => 'minimum_payment', 'label' => 'Minimal Pembayaran', 'description' => 'Nominal minimal pembayaran yang diperbolehkan.', 'type' => 'text', 'value' => $preferences['minimum_payment']),
        );
    }

    protected function adminBankAccounts()
    {
        return $this->adminBankAccountsFromDatabase();

        return array(
            array('bank' => 'BCA', 'account' => '1234 5678 9012 3456', 'owner' => 'Arena Sport', 'status' => 'Aktif', 'statusClass' => 'success', 'accent' => 'bca'),
            array('bank' => 'BNI', 'account' => '9876 5432 1098 7654', 'owner' => 'Arena Sport', 'status' => 'Aktif', 'statusClass' => 'success', 'accent' => 'bni'),
            array('bank' => 'Mandiri', 'account' => '1111 2222 3333 4444', 'owner' => 'Arena Sport', 'status' => 'Aktif', 'statusClass' => 'success', 'accent' => 'mandiri'),
            array('bank' => 'BRI', 'account' => '2222 3333 4444 5555', 'owner' => 'Arena Sport', 'status' => 'Nonaktif', 'statusClass' => 'inactive', 'accent' => 'bri'),
        );
    }

    protected function securitySettings()
    {
        $preferences = $this->adminPreferences();
        return array(
            array('label' => 'Sesi Aktif', 'description' => 'Lihat perangkat yang saat ini sedang login ke akun Anda.', 'icon' => 'fa-lock', 'accent' => 'blue', 'type' => 'button', 'button' => 'Lihat Sesi', 'url' => '#active-sessions'),
            array('label' => 'Ubah Password', 'description' => 'Ubah password akun administrator secara berkala.', 'icon' => 'fa-key', 'accent' => 'purple', 'type' => 'button', 'button' => 'Ubah Password', 'url' => app_url('admin/pengaturan?tab=akun')),
            array('label' => 'Verifikasi Email', 'description' => 'Email Anda telah terverifikasi.', 'icon' => 'fa-envelope-circle-check', 'accent' => 'gold', 'status' => 'Aktif', 'type' => 'verified', 'email' => 'admin@arenasport.com'),
            array('key' => 'notification_security', 'label' => 'Notifikasi Keamanan', 'description' => 'Dapatkan notifikasi untuk aktivitas keamanan penting.', 'icon' => 'fa-shield', 'accent' => 'teal', 'type' => 'toggle', 'enabled' => $preferences['notification_security'] === '1'),
        );
    }

    protected function securityActivities()
    {
        return array(
            array('title' => 'Sesi Admin Aktif', 'description' => 'IP: ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1'), 'date' => date('d/m/Y'), 'time' => date('H:i') . ' WITA', 'icon' => 'fa-right-to-bracket', 'accent' => 'green'),
        );
    }

    protected function activeSessions()
    {
        return array(
            array('device' => PHP_OS_FAMILY, 'type' => '', 'browser' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 80) : 'Browser saat ini', 'location' => 'Sesi saat ini', 'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1', 'lastActive' => date('d/m/Y H:i') . ' WITA', 'status' => 'Aktif', 'current' => true, 'icon' => 'fa-desktop', 'accent' => 'green'),
        );
    }

    protected function adminAccountProfile($name)
    {
        $row = $this->adminData()->row('SELECT Nama, Email, Nomor_telepon, Role FROM users WHERE ID_User = ? LIMIT 1', 's', array(isset($_SESSION['id_user']) ? $_SESSION['id_user'] : ''));

        if ($row) {
            $parts = preg_split('/\s+/', trim($row['Nama']));
            $initials = '';
            foreach (array_slice($parts, 0, 2) as $part) { $initials .= strtoupper(substr($part, 0, 1)); }
            return array('name' => $row['Nama'], 'initials' => $initials, 'email' => $row['Email'], 'phone' => $row['Nomor_telepon'], 'username' => strtolower(strstr($row['Email'], '@', true)), 'role' => ucfirst($row['Role']));
        }

        $displayName = trim((string) $name);

        if ($displayName === '' || strtolower($displayName) === 'ripal' || strtolower($displayName) === 'admin arena') {
            $displayName = 'Ripal Administrator';
        }

        return array(
            'name' => $displayName,
            'initials' => 'RI',
            'email' => 'admin@arenasport.com',
            'phone' => '0812-3456-7890',
            'username' => 'ripal_admin',
            'role' => 'Administrator',
        );
    }

    protected function adminLoginSettings()
    {
        $preferences = $this->adminPreferences();
        return array(
            array('key' => 'login_notification', 'label' => 'Notifikasi Login Baru', 'description' => 'Kirim notifikasi ketika ada login di perangkat baru', 'enabled' => $preferences['login_notification'] === '1'),
            array('key' => 'automatic_logout', 'label' => 'Logout Otomatis', 'description' => 'Logout otomatis jika tidak aktif selama 30 menit', 'enabled' => $preferences['automatic_logout'] === '1'),
            array('key' => 'login_history', 'label' => 'Simpan Riwayat Login', 'description' => 'Simpan riwayat perangkat yang pernah login', 'enabled' => $preferences['login_history'] === '1'),
        );
    }

    protected function adminLoginActivity()
    {
        return array(
            array('label' => 'Sesi Saat Ini', 'value' => date('d/m/Y H:i') . ' WITA'),
            array('label' => 'Browser', 'value' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 90) : 'Browser saat ini'),
            array('label' => 'Sistem Operasi Server', 'value' => PHP_OS_FAMILY),
            array('label' => 'IP Address', 'value' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1'),
        );
    }

    protected function adminAccessRights()
    {
        return array(
            'Kelola Lapangan',
            'Kelola Transaksi',
            'Kelola Booking',
            'Kelola Laporan',
            'Kelola Customer',
            'Pengaturan Sistem',
            'Manajemen Admin',
            'Kelola Ulasan & Rating',
        );
    }

    protected function adminActiveDevices()
    {
        return array(
            array('device' => PHP_OS_FAMILY . ' - Browser', 'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1', 'location' => 'Sesi saat ini', 'time' => date('d/m/Y H:i') . ' WITA', 'icon' => 'fa-desktop', 'current' => true),
        );
    }

    protected function systemInformation()
    {
        $version = $this->adminData()->value('SELECT VERSION() AS value');

        return array(
            array('label' => 'Versi Aplikasi', 'value' => 'v1.0.0', 'icon' => 'fa-server', 'accent' => 'green'),
            array('label' => 'Versi PHP', 'value' => PHP_VERSION, 'icon' => 'fa-code', 'accent' => 'blue'),
            array('label' => 'Database', 'value' => 'MariaDB ' . $version, 'icon' => 'fa-database', 'accent' => 'purple'),
            array('label' => 'Waktu Server', 'value' => date('d/m/Y H:i') . ' WITA', 'icon' => 'fa-clock', 'accent' => 'gold'),
            array('label' => 'Terakhir Update', 'value' => date('d/m/Y H:i') . ' WITA', 'icon' => 'fa-cloud-arrow-up', 'accent' => 'teal'),
        );
    }
}
