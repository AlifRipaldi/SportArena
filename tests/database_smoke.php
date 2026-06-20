<?php

require __DIR__ . '/../bootstrap/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_save_path(__DIR__ . '/../storage/sessions');
    session_start();
}

use App\Controllers\AdminController;
use App\Controllers\DashboardController;
use App\Controllers\PemilikController;
use App\Models\ArenaData;
use App\Core\Database;

function invokeProtected($object, $method, array $arguments = array())
{
    $reflection = new ReflectionMethod($object, $method);
    $reflection->setAccessible(true);

    return $reflection->invokeArgs($object, $arguments);
}

function assertArrayResult($label, $result, $minimum = 0)
{
    if (!is_array($result) || count($result) < $minimum) {
        throw new RuntimeException($label . ' gagal.');
    }

    echo '[OK] ' . $label . ': ' . count($result) . PHP_EOL;
}

function assertRender($label, $controller, array $methods)
{
    foreach ($methods as $method) {
        ob_start();
        $controller->{$method}();
        $content = ob_get_clean();

        if (trim((string) $content) === '') {
            throw new RuntimeException($label . ' ' . $method . ' tidak menghasilkan halaman.');
        }
    }

    echo '[OK] Render ' . $label . ': ' . count($methods) . ' halaman' . PHP_EOL;
}

$data = new ArenaData();

$customerId = $data->value("SELECT ID_User AS value FROM users WHERE Role = 'customer' ORDER BY ID_User LIMIT 1");
$_SESSION = array('id_user' => $customerId);
$dashboard = new DashboardController();
assertArrayResult('Customer booking', invokeProtected($dashboard, 'customerBookingsFromDatabase', array(false)));
assertArrayResult('Customer favorit', invokeProtected($dashboard, 'customerFavoritesFromDatabase'));
assertArrayResult('Customer ulasan', invokeProtected($dashboard, 'customerReviewsFromDatabase'));
assertArrayResult('Customer pengaturan', invokeProtected($dashboard, 'customerAccountSettings'), 1);
$availableVenues = invokeProtected($dashboard, 'databaseVenues');
assertArrayResult('Customer lapangan tersedia', $availableVenues, 1);
if (empty($availableVenues[0]['availableSchedules'])) {
    throw new RuntimeException('Customer belum memiliki slot jadwal yang dapat dipesan.');
}

$connection = Database::connection();
mysqli_begin_transaction($connection);
try {
    $scheduleId = $availableVenues[0]['availableSchedules'][0]['id'];
    $testBooking = invokeProtected($dashboard, 'reserveBooking', array($connection, $scheduleId, $customerId));
    invokeProtected($dashboard, 'createBookingNotifications', array($connection, $testBooking));
    $bookingStatement = mysqli_prepare($connection, 'SELECT COUNT(*) AS total FROM booking WHERE ID_Booking=? AND ID_User=?');
    mysqli_stmt_bind_param($bookingStatement, 'ss', $testBooking['id'], $customerId);
    mysqli_stmt_execute($bookingStatement);
    $bookingResult = mysqli_stmt_get_result($bookingStatement);
    $bookingCount = $bookingResult ? (int) mysqli_fetch_assoc($bookingResult)['total'] : 0;
    mysqli_stmt_close($bookingStatement);
    if ($bookingCount !== 1) {
        throw new RuntimeException('Transaksi pembuatan booking customer gagal.');
    }
    echo '[OK] Customer membuat booking dalam transaksi' . PHP_EOL;
} finally {
    mysqli_rollback($connection);
}

ob_start();
$dashboard->search();
$searchPage = ob_get_clean();
if (
    strpos($searchPage, app_url('dashboard/booking/tambah')) === false
    || strpos($searchPage, 'name="booking_token"') === false
    || strpos($searchPage, 'id="fieldDetailModal"') === false
    || strpos($searchPage, 'data-field-open') === false
) {
    throw new RuntimeException('Form booking customer belum terhubung ke route aman.');
}
echo '[OK] Form booking customer terhubung' . PHP_EOL;

ob_start();
$dashboard->booking();
$bookingPage = ob_get_clean();
if (strpos($bookingPage, 'name="booking_slot"') === false || strpos($bookingPage, 'name="payment_method"') === false) {
    throw new RuntimeException('Pengelolaan jadwal atau pembayaran booking customer belum aktif.');
}
echo '[OK] Pengelolaan booking customer aktif' . PHP_EOL;

ob_start();
$dashboard->fieldDetail($availableVenues[0]['id']);
$fieldDetailPage = ob_get_clean();
if (
    strpos($fieldDetailPage, 'class="customer-field-page"') === false
    || strpos($fieldDetailPage, 'id="customerFieldBooking"') === false
    || strpos($fieldDetailPage, app_url('dashboard/booking/tambah')) === false
) {
    throw new RuntimeException('Halaman detail dan booking lapangan customer belum aktif.');
}
echo '[OK] Detail lapangan customer aktif' . PHP_EOL;
assertRender('customer', $dashboard, array('dashboard', 'search', 'booking', 'riwayat', 'favorit', 'ulasan', 'profil', 'settings'));

$ownerUserId = $data->value(
    'SELECT p.ID_User AS value FROM pemilik_lapangan p INNER JOIN lapangan l ON l.ID_Pemilik = p.ID_Pemilik ORDER BY p.ID_User LIMIT 1'
);
$_SESSION = array('id_user' => $ownerUserId);
$owner = new PemilikController();
assertArrayResult('Pemilik lapangan', invokeProtected($owner, 'ownerFieldsFromDatabase'), 1);
assertArrayResult('Pemilik booking', invokeProtected($owner, 'ownerBookingsFromDatabase'));
assertArrayResult('Pemilik pendapatan bulanan', invokeProtected($owner, 'ownerMonthlyRevenueFromDatabase'), 12);
assertArrayResult('Pemilik ulasan', invokeProtected($owner, 'ownerReviewsFromDatabase'));
assertArrayResult('Pemilik profil lapangan', invokeProtected($owner, 'ownerManagedFieldsFromDatabase'), 1);
$_SESSION['role_user'] = 'pemilik';
$_SESSION['nama_user'] = 'Pemilik Test';
assertRender('pemilik', $owner, array('index', 'lapangan', 'booking', 'jadwal', 'pendapatan', 'transaksi', 'ulasan', 'profil', 'pengaturan'));

$adminId = $data->value("SELECT ID_User AS value FROM users WHERE Role = 'admin' ORDER BY ID_User LIMIT 1");
$_SESSION = array('id_user' => $adminId);
$admin = new AdminController();
assertArrayResult('Admin ringkasan', invokeProtected($admin, 'adminSummaryCardsFromDatabase'), 4);
assertArrayResult('Admin booking', invokeProtected($admin, 'adminBookingsFromDatabase'));
assertArrayResult('Admin lapangan', invokeProtected($admin, 'adminFieldsFromDatabase'), 1);
assertArrayResult('Admin ulasan', invokeProtected($admin, 'adminReviewsFromDatabase'));
assertArrayResult('Admin transaksi', invokeProtected($admin, 'adminTransactionsFromDatabase'));
assertArrayResult('Admin laporan', invokeProtected($admin, 'adminReportStatsFromDatabase'), 4);
assertArrayResult('Admin metode pembayaran', invokeProtected($admin, 'adminPaymentMethodsFromDatabase'), 1);
$_SESSION['role_user'] = 'admin';
$_SESSION['nama_user'] = 'Admin Test';
assertRender('admin', $admin, array('index', 'booking', 'lapangan', 'users', 'ulasan', 'transaksi', 'laporan', 'pengaturan'));

echo 'Database smoke test selesai.' . PHP_EOL;
