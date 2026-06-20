<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\ArenaData;
use App\Models\Lapangan;

class PemilikController extends Controller
{
    public function index()
    {
        $owner = $this->requireOwner();

        return $this->view('Owner/index', array(
            'title' => 'Dashboard Pemilik | Arena Sport',
            'activeMenu' => 'dashboard',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'summaryCards' => $this->summaryCards(),
            'monthlyRevenue' => $this->monthlyRevenue(),
            'bookingStatus' => $this->bookingStatus(),
            'recentBookings' => $this->recentBookings(),
            'ownerFields' => $this->ownerFields(),
            'latestReviews' => $this->latestReviews(),
        ), 'layouts/owner');
    }

    public function lapangan()
    {
        $owner = $this->requireOwner();
        $fieldOwnerId = $this->ownerLapanganPemilikId($owner);

        return $this->view('Owner/lapangan', array(
            'title' => 'Kelola Lapangan | Arena Sport',
            'activeMenu' => 'lapangan',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'lapangan' => $fieldOwnerId !== '' ? $this->getAllLapangan($fieldOwnerId) : array(),
        ), 'layouts/owner');
    }

    public function storeLapangan()
    {
        $owner = $this->requireOwner();
        $data = $this->lapanganPostData('Aktif');
        $data['photos'] = $this->storeLapanganPhotos();
        $fieldOwnerId = $this->ownerLapanganPemilikId($owner, true, $data['location']);

        if ($fieldOwnerId !== '') {
            $saved = (new Lapangan())->createForOwner($fieldOwnerId, $data);

            if (!$saved) {
                $this->deleteLapanganPhotoFiles($data['photos']);
            }
        } else {
            $this->deleteLapanganPhotoFiles($data['photos']);
        }

        $this->redirect('pemilik/lapangan');
    }

    public function updateLapangan()
    {
        $owner = $this->requireOwner();
        $fieldOwnerId = $this->ownerLapanganPemilikId($owner);
        $fieldId = isset($_POST['id_lapangan']) ? trim((string) $_POST['id_lapangan']) : '';

        if ($fieldId !== '' && $fieldOwnerId !== '') {
            $model = new Lapangan();
            $current = $model->findForOwner($fieldId, $fieldOwnerId);
            $data = $this->lapanganPostData('Nonaktif');
            $currentPhotos = $this->decodeLapanganPhotos($current && isset($current['Foto']) ? $current['Foto'] : '');
            $deletedPhotos = $this->cleanLapanganPhotos(isset($_POST['delete_photos']) && is_array($_POST['delete_photos']) ? $_POST['delete_photos'] : array());
            $remainingPhotos = array_values(array_filter($currentPhotos, function ($photo) use ($deletedPhotos) {
                return !in_array($photo, $deletedPhotos, true);
            }));
            $newPhotos = $this->storeLapanganPhotos(max(0, 5 - count($remainingPhotos)));

            $data['photos'] = array_slice(array_merge($remainingPhotos, $newPhotos), 0, 5);

            if ($model->updateForOwner($fieldId, $fieldOwnerId, $data)) {
                $this->deleteLapanganPhotoFiles($deletedPhotos);
            } else {
                $this->deleteLapanganPhotoFiles($newPhotos);
            }
        }

        $this->redirect('pemilik/lapangan');
    }

    public function deleteLapangan()
    {
        $owner = $this->requireOwner();
        $fieldOwnerId = $this->ownerLapanganPemilikId($owner);
        $fieldId = isset($_POST['id_lapangan']) ? trim((string) $_POST['id_lapangan']) : '';

        if ($fieldId !== '' && $fieldOwnerId !== '') {
            $model = new Lapangan();
            $current = $model->findForOwner($fieldId, $fieldOwnerId);
            $photos = $this->decodeLapanganPhotos($current && isset($current['Foto']) ? $current['Foto'] : '');

            if ($model->deleteForOwner($fieldId, $fieldOwnerId)) {
                $this->deleteLapanganPhotoFiles($photos);
            }
        }

        $this->redirect('pemilik/lapangan');
    }

    public function booking()
    {
        $owner = $this->requireOwner();

        return $this->view('Owner/booking', array(
            'title' => 'Manajemen Booking | Arena Sport',
            'activeMenu' => 'booking',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'bookings' => $this->getOwnerBookings(),
        ), 'layouts/owner');
    }

    public function jadwal()
    {
        $owner = $this->requireOwner();
        $selectedStatus = $this->sanitizeScheduleStatus(isset($_GET['status']) ? $_GET['status'] : 'Semua');
        $selectedDateValue = $this->sanitizeScheduleDate(isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'));
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $scheduleResult = $this->getFilteredSchedule($selectedStatus, $selectedDateValue, $currentPage);
        $fieldOwnerId = $this->ownerLapanganPemilikId($owner);

        return $this->view('Owner/jadwal', array(
            'title' => 'Jadwal Booking | Arena Sport',
            'activeMenu' => 'jadwal',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'statusTabs' => $this->scheduleStatusTabs(),
            'selectedStatus' => $selectedStatus,
            'selectedDate' => $this->formatScheduleDate($selectedDateValue),
            'selectedDateValue' => $selectedDateValue,
            'schedule' => $scheduleResult['items'],
            'pagination' => $scheduleResult['pagination'],
            'managedFields' => $fieldOwnerId !== '' ? $this->getAllLapangan($fieldOwnerId) : array(),
        ), 'layouts/owner');
    }

    public function storeJadwal()
    {
        $owner = $this->requireOwner();
        $ownerId = $this->ownerLapanganPemilikId($owner);
        $fieldId = isset($_POST['id_lapangan']) ? trim((string) $_POST['id_lapangan']) : '';
        $date = isset($_POST['tanggal']) ? trim((string) $_POST['tanggal']) : '';
        $start = isset($_POST['jam_mulai']) ? trim((string) $_POST['jam_mulai']) : '';
        $end = isset($_POST['jam_selesai']) ? trim((string) $_POST['jam_selesai']) : '';
        $price = isset($_POST['harga']) ? max(0, (int) $_POST['harga']) : 0;

        if ($ownerId !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && $date >= date('Y-m-d') && $start < $end) {
            $data = $this->ownerData();
            $ownsField = (int) $data->value('SELECT COUNT(*) AS value FROM lapangan WHERE ID_Lapangan=? AND ID_Pemilik=? AND deleted_at IS NULL', 'ss', array($fieldId, $ownerId));

            if ($ownsField > 0) {
                $id = 'JWL' . date('ymdHis') . random_int(10, 99);
                $data->execute("INSERT IGNORE INTO jadwal (ID_Jadwal,ID_Lapangan,Tanggal,Jam_Mulai,Jam_Selesai,Status,Harga) VALUES (?,?,?,?,?,'Available',?)", 'sssssi', array($id, $fieldId, $date, $start, $end, $price));
            }
        }

        header('Location: ' . app_url('pemilik/jadwal?date=' . rawurlencode($date !== '' ? $date : date('Y-m-d'))));
        exit;
    }

    public function pendapatan()
    {
        $owner = $this->requireOwner();
        $selectedPeriod = $this->sanitizeOwnerRevenuePeriod(isset($_GET['periode']) ? $_GET['periode'] : 'bulanan');
        $range = $this->resolveOwnerRevenueRange(
            $selectedPeriod,
            isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '',
            isset($_GET['tanggal_selesai']) ? $_GET['tanggal_selesai'] : ''
        );
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $transactions = $this->filterOwnerRevenueTransactions($range['start'], $range['end']);
        $previousRange = $this->resolveOwnerPreviousRevenueRange($range['start'], $range['end']);
        $previousTransactions = $this->filterOwnerRevenueTransactions($previousRange['start'], $previousRange['end']);
        $transactionResult = $this->paginateSchedule($transactions, $page, 5);
        $chart = $this->dailyRevenueChart($transactions, $range['start'], $range['end'], $selectedPeriod);

        return $this->view('Owner/pendapatan', array(
            'title' => 'Pendapatan | Arena Sport',
            'activeMenu' => 'pendapatan',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'periodTabs' => $this->ownerRevenuePeriodTabs(),
            'selectedPeriodKey' => $selectedPeriod,
            'selectedPeriod' => $this->formatOwnerReportPeriod($range['start'], $range['end']),
            'selectedStartDate' => $range['start']->format('Y-m-d'),
            'selectedEndDate' => $range['end']->format('Y-m-d'),
            'revenueStats' => $this->revenueStats($transactions, $previousTransactions, $selectedPeriod),
            'revenueChart' => $chart['points'],
            'revenueChartLabels' => $chart['labels'],
            'revenueSummary' => $this->revenueSummary($transactions),
            'revenueTransactions' => $transactionResult['items'],
            'revenuePagination' => $transactionResult['pagination'],
        ), 'layouts/owner');
    }

    public function downloadPendapatan()
    {
        $owner = $this->requireOwner();
        $period = $this->sanitizeOwnerReportPeriod(isset($_GET['periode_laporan']) ? $_GET['periode_laporan'] : '30_hari');
        $type = $this->sanitizeOwnerReportType(isset($_GET['tipe_laporan']) ? $_GET['tipe_laporan'] : 'pendapatan');
        $format = $this->sanitizeOwnerReportFormat(isset($_GET['format_laporan']) ? $_GET['format_laporan'] : 'xlsx');
        $range = $this->resolveOwnerReportRange(
            $period,
            isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '',
            isset($_GET['tanggal_selesai']) ? $_GET['tanggal_selesai'] : ''
        );
        $report = $this->buildOwnerRevenueReport($owner, $type, $range['start'], $range['end']);
        $filename = 'arena-sport-' . $report['slug'] . '-' . $range['start']->format('Ymd') . '-' . $range['end']->format('Ymd');

        if ($format === 'csv') {
            $this->sendOwnerCsvReport($report, $filename . '.csv');
        }

        if ($format === 'pdf') {
            $this->sendOwnerPdfReport($report, $filename . '.pdf');
        }

        $this->sendOwnerXlsxReport($report, $filename . '.xlsx');
    }

    public function transaksi()
    {
        $owner = $this->requireOwner();
        $filters = $this->ownerTransactionFiltersFromRequest();
        $allTransactions = $this->ownerTransactionRows();
        $filteredTransactions = $this->filterOwnerTransactions($allTransactions, $filters);
        $transactionResult = $this->paginateSchedule($filteredTransactions, isset($_GET['page']) ? (int) $_GET['page'] : 1, $filters['perPage']);

        return $this->view('Owner/transaksi', array(
            'title' => 'Lihat Semua Transaksi | Arena Sport',
            'activeMenu' => 'pendapatan',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'ownerTopbarSearchPlaceholder' => 'Cari transaksi...',
            'transactionStats' => $this->ownerTransactionStats($filteredTransactions),
            'transactions' => $transactionResult['items'],
            'transactionPagination' => $transactionResult['pagination'],
            'transactionTotal' => count($filteredTransactions),
            'transactionAllTotal' => count($allTransactions),
            'transactionFilters' => $this->ownerTransactionViewFilters($filters),
            'transactionTypeOptions' => $this->ownerTransactionTypeOptions(),
            'transactionMethodOptions' => $this->ownerTransactionMethodOptions(),
            'transactionStatusOptions' => $this->ownerTransactionStatusOptions(),
        ), 'layouts/owner');
    }

    public function downloadTransaksi()
    {
        $this->requireOwner();
        $filters = $this->ownerTransactionFiltersFromRequest();
        $transactions = $this->filterOwnerTransactions($this->ownerTransactionRows(), $filters);
        $filename = 'arena-sport-transaksi-' . $filters['start']->format('Ymd') . '-' . $filters['end']->format('Ymd') . '.csv';

        $this->sendOwnerTransactionCsv($transactions, $filters, $filename);
    }

    public function ulasan()
    {
        $owner = $this->requireOwner();

        return $this->view('Owner/ulasan', array(
            'title' => 'Ulasan & Rating | Arena Sport',
            'activeMenu' => 'ulasan',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'reviewStats' => $this->ownerReviewStats(),
            'ratingDistribution' => $this->ownerRatingDistribution(),
            'fieldRatings' => $this->ownerFieldRatings(),
            'reviews' => $this->ownerReviewRows(),
        ), 'layouts/owner');
    }

    public function profil()
    {
        $owner = $this->requireOwner();
        $profile = $this->ownerProfile($owner);
        $flash = $this->pullOwnerProfileFlash();

        return $this->view('Owner/profil', array(
            'title' => 'Profil | Arena Sport',
            'activeMenu' => 'profil',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'profile' => $profile,
            'profileFlash' => $flash,
            'managedFields' => $this->profileManagedFields(),
        ), 'layouts/owner');
    }

    public function updateProfil()
    {
        $owner = $this->requireOwner();
        $action = isset($_POST['profile_action']) ? $_POST['profile_action'] : 'update_profile';

        if ($action === 'change_password') {
            $this->changeOwnerPassword($owner);
            header('Location: ' . app_url('pemilik/profil'));
            exit;
        }

        $this->saveOwnerProfile($owner);
        header('Location: ' . app_url('pemilik/profil'));
        exit;
    }

    public function pengaturan()
    {
        $owner = $this->requireOwner();
        $profile = $this->ownerProfile($owner);

        return $this->view('Owner/pengaturan', array(
            'title' => 'Pengaturan | Arena Sport',
            'activeMenu' => 'pengaturan',
            'userName' => $owner['name'],
            'userRole' => $owner['role'],
            'profile' => $profile,
            'settingsGroups' => $this->ownerSettingsGroups(),
            'helpItems' => $this->ownerHelpItems(),
        ), 'layouts/owner');
    }

    protected function requireOwner()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['id_user'])) {
            header('Location: ' . app_url('public/login.php'));
            exit;
        }

        $role = isset($_SESSION['role_user']) ? $_SESSION['role_user'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');

        if (!$this->isOwnerRole($role)) {
            header('Location: ' . app_url('dashboard'));
            exit;
        }

        return array(
            'id' => isset($_SESSION['id_user']) ? $_SESSION['id_user'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''),
            'name' => isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : (isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pemilik Arena'),
            'email' => isset($_SESSION['email_user']) ? $_SESSION['email_user'] : '',
            'phone' => isset($_SESSION['telepon_user']) ? $_SESSION['telepon_user'] : '',
            'role' => $role,
        );
    }

    protected function isOwnerRole($role)
    {
        return in_array($this->normalizeRole($role), array('owner', 'pemilik', 'pemilik lapangan', 'mitra'), true);
    }

    protected function normalizeRole($role)
    {
        return strtolower(str_replace(array('_', '-'), ' ', trim((string) $role)));
    }

    protected function ownerData()
    {
        return new ArenaData();
    }

    protected function ownerUserId()
    {
        return isset($_SESSION['id_user']) ? trim((string) $_SESSION['id_user']) : '';
    }

    protected function ownerDatabaseId()
    {
        $value = $this->ownerData()->value(
            'SELECT ID_Pemilik AS value FROM pemilik_lapangan WHERE ID_User = ? LIMIT 1',
            's',
            array($this->ownerUserId())
        );

        return $value !== null ? trim((string) $value) : '';
    }

    protected function ownerBookingDatabaseRows()
    {
        $ownerId = $this->ownerDatabaseId();

        if ($ownerId === '') {
            return array();
        }

        return $this->ownerData()->rows(
            "SELECT b.ID_Booking, b.Status AS booking_status, b.Total_harga, b.Waktu_transaksi,
                    u.Nama AS customer_name, u.Nomor_telepon,
                    j.Tanggal, j.Jam_Mulai, j.Jam_Selesai,
                    l.ID_Lapangan, l.Nama_lapangan, l.Jenis_olahraga, l.Foto,
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
             WHERE l.ID_Pemilik = ?
             ORDER BY j.Tanggal DESC, j.Jam_Mulai DESC",
            's',
            array($ownerId)
        );
    }

    protected function ownerStatusPayload($bookingStatus, $paymentStatus = '')
    {
        $status = $this->normalizeRole($bookingStatus . ' ' . $paymentStatus);

        if (strpos($status, 'batal') !== false || strpos($status, 'refund') !== false || strpos($status, 'gagal') !== false) {
            return array('key' => 'dibatalkan', 'label' => 'Dibatalkan', 'class' => 'danger');
        }

        if (strpos($status, 'selesai') !== false || strpos($status, 'berhasil') !== false || strpos($status, 'dibayar') !== false || strpos($status, 'lunas') !== false || strpos($status, 'paid') !== false) {
            return array('key' => 'selesai', 'label' => 'Selesai', 'class' => 'active');
        }

        if (strpos($status, 'menunggu') !== false || strpos($status, 'pending') !== false) {
            return array('key' => 'menunggu', 'label' => 'Pending', 'class' => 'warning');
        }

        return array('key' => 'aktif', 'label' => 'Aktif', 'class' => 'success');
    }

    protected function ownerBookingsFromDatabase()
    {
        $bookings = array();

        foreach ($this->ownerBookingDatabaseRows() as $row) {
            $status = $this->ownerStatusPayload($row['booking_status'], isset($row['payment_status']) ? $row['payment_status'] : '');
            $bookings[] = array(
                'code' => $row['ID_Booking'],
                'field' => $row['Nama_lapangan'],
                'user' => $row['customer_name'],
                'date' => $this->ownerFormatDatabaseDate($row['Tanggal']),
                'time' => substr((string) $row['Jam_Mulai'], 0, 5) . ' - ' . substr((string) $row['Jam_Selesai'], 0, 5),
                'status' => $status['label'],
                'statusClass' => $status['class'],
                'total' => $this->formatOwnerRupiah($row['Total_harga']),
            );
        }

        return $bookings;
    }

    protected function ownerScheduleFromDatabase()
    {
        $schedule = array();
        $ownerId = $this->ownerDatabaseId();
        if ($ownerId === '') { return $schedule; }
        $rows = $this->ownerData()->rows(
            "SELECT j.ID_Jadwal,j.Tanggal,j.Jam_Mulai,j.Jam_Selesai,j.Status AS schedule_status,j.Harga,
                    l.Nama_lapangan,l.Harga AS field_price,
                    b.ID_Booking,b.Status AS booking_status,b.Total_harga,
                    u.Nama AS customer_name,p.Status AS payment_status
             FROM jadwal j INNER JOIN lapangan l ON l.ID_Lapangan=j.ID_Lapangan
             LEFT JOIN booking b ON b.ID_Booking=(SELECT b2.ID_Booking FROM booking b2 WHERE b2.ID_Jadwal=j.ID_Jadwal ORDER BY b2.Waktu_transaksi DESC LIMIT 1)
             LEFT JOIN users u ON u.ID_User=b.ID_User
             LEFT JOIN pembayaran p ON p.ID_Pembayaran=(SELECT p2.ID_Pembayaran FROM pembayaran p2 WHERE p2.ID_Booking=b.ID_Booking ORDER BY p2.created_at DESC LIMIT 1)
             WHERE l.ID_Pemilik=? AND l.deleted_at IS NULL ORDER BY j.Tanggal DESC,j.Jam_Mulai DESC",
            's', array($ownerId)
        );

        foreach ($rows as $row) {
            $status = !empty($row['ID_Booking'])
                ? $this->ownerStatusPayload($row['booking_status'], isset($row['payment_status']) ? $row['payment_status'] : '')
                : array('key' => 'aktif', 'label' => strtolower($row['schedule_status']) === 'booked' ? 'Terisi' : 'Aktif', 'class' => 'success');
            $start = substr((string) $row['Jam_Mulai'], 0, 5);
            $end = substr((string) $row['Jam_Selesai'], 0, 5);
            $minutes = max(0, (int) ((strtotime($end) - strtotime($start)) / 60));
            $schedule[] = array(
                'tenant' => !empty($row['customer_name']) ? $row['customer_name'] : 'Tersedia',
                'field' => $row['Nama_lapangan'],
                'date' => $this->ownerFormatDatabaseDate($row['Tanggal']),
                'dateValue' => $row['Tanggal'],
                'time' => $start . ' - ' . $end,
                'duration' => $minutes >= 60 && $minutes % 60 === 0 ? ($minutes / 60) . ' Jam' : $minutes . ' Menit',
                'status' => $status['label'],
                'statusClass' => $status['class'],
                'total' => $this->formatOwnerRupiah(!empty($row['Total_harga']) ? $row['Total_harga'] : (!empty($row['Harga']) ? $row['Harga'] : $row['field_price'])),
            );
        }

        return $schedule;
    }

    protected function ownerMonthlyRevenueFromDatabase()
    {
        $ownerId = $this->ownerDatabaseId();
        $amounts = array_fill(1, 12, 0);

        if ($ownerId !== '') {
            $rows = $this->ownerData()->rows(
                "SELECT MONTH(COALESCE(p.Waktu_pembayaran,p.created_at)) AS month_number,
                        COALESCE(SUM(p.Jumlah),0) AS amount
                 FROM pembayaran p
                 INNER JOIN booking b ON b.ID_Booking = p.ID_Booking
                 INNER JOIN jadwal j ON j.ID_Jadwal = b.ID_Jadwal
                 INNER JOIN lapangan l ON l.ID_Lapangan = j.ID_Lapangan
                 WHERE l.ID_Pemilik = ?
                   AND LOWER(p.Status) IN ('berhasil','dibayar','lunas','success','paid')
                   AND YEAR(COALESCE(p.Waktu_pembayaran,p.created_at)) = YEAR(CURDATE())
                 GROUP BY MONTH(COALESCE(p.Waktu_pembayaran,p.created_at))",
                's',
                array($ownerId)
            );

            foreach ($rows as $row) {
                $amounts[(int) $row['month_number']] = (int) $row['amount'];
            }
        }

        $max = max(1, max($amounts));
        $points = array();

        foreach ($amounts as $month => $amount) {
            $points[] = array(
                'month' => $this->shortOwnerMonthName($month),
                'amount' => $this->formatOwnerRupiah($amount),
                'x' => round((($month - 1) / 11) * 100, 2),
                'y' => round(92 - (($amount / $max) * 82), 2),
            );
        }

        return $points;
    }

    protected function ownerBookingStatusFromDatabase()
    {
        $counts = array('Selesai' => 0, 'Aktif' => 0, 'Pending' => 0, 'Dibatalkan' => 0);

        foreach ($this->ownerBookingDatabaseRows() as $row) {
            $status = $this->ownerStatusPayload($row['booking_status'], isset($row['payment_status']) ? $row['payment_status'] : '');
            $counts[$status['label']] = isset($counts[$status['label']]) ? $counts[$status['label']] + 1 : 1;
        }

        $total = max(1, array_sum($counts));
        $colors = array('Selesai' => 'lime', 'Aktif' => 'blue', 'Pending' => 'gold', 'Dibatalkan' => 'red');
        $result = array();

        foreach ($counts as $label => $count) {
            $result[] = array('label' => $label, 'value' => number_format(($count / $total) * 100, 0) . '%', 'count' => (string) $count, 'color' => $colors[$label]);
        }

        return $result;
    }

    protected function ownerFieldsFromDatabase()
    {
        $ownerId = $this->ownerDatabaseId();

        if ($ownerId === '') {
            return array();
        }

        $rows = $this->ownerData()->rows(
            "SELECT l.*, COALESCE(AVG(r.Rating),0) AS rating, COUNT(r.ID_Review) AS reviews
             FROM lapangan l LEFT JOIN review r ON r.ID_Lapangan = l.ID_Lapangan
             WHERE l.ID_Pemilik = ? AND l.deleted_at IS NULL
             GROUP BY l.ID_Lapangan ORDER BY l.created_at DESC",
            's',
            array($ownerId)
        );
        $fields = array();

        foreach ($rows as $row) {
            $fields[] = array(
                'name' => $row['Nama_lapangan'], 'location' => $row['Lokasi'],
                'rating' => number_format((float) $row['rating'], 1), 'reviews' => (string) $row['reviews'],
                'price' => $this->formatOwnerRupiah($row['Harga']), 'status' => $row['Status'],
                'visual' => $this->lapanganVisual($row['Jenis_olahraga']),
            );
        }

        return $fields;
    }

    protected function ownerRevenueTransactionsFromDatabase()
    {
        $transactions = array();

        foreach ($this->ownerBookingDatabaseRows() as $row) {
            $paymentStatus = strtolower(trim((string) (isset($row['payment_status']) ? $row['payment_status'] : '')));

            if (!in_array($paymentStatus, array('berhasil', 'dibayar', 'lunas', 'success', 'paid'), true)) {
                continue;
            }

            $amount = !empty($row['Jumlah']) ? (int) $row['Jumlah'] : (int) $row['Total_harga'];
            $fee = (int) round($amount * 0.02);
            $transactions[] = array(
                'date' => substr((string) (!empty($row['Waktu_pembayaran']) ? $row['Waktu_pembayaran'] : $row['payment_created_at']), 0, 10),
                'field' => $row['Nama_lapangan'], 'tenant' => $row['customer_name'],
                'total' => $this->formatOwnerRupiah($amount), 'fee' => $this->formatOwnerRupiah($fee),
                'net' => $this->formatOwnerRupiah($amount - $fee), 'status' => 'Dibayar',
                'method' => isset($row['Metode']) ? $row['Metode'] : '-',
                'methodIcon' => 'fa-building-columns', 'methodClass' => $this->ownerMethodClass(isset($row['Metode']) ? $row['Metode'] : ''),
            );
        }

        return $transactions;
    }

    protected function ownerTransactionsFromDatabase()
    {
        $transactions = array();

        foreach ($this->ownerBookingDatabaseRows() as $row) {
            $status = $this->ownerStatusPayload($row['booking_status'], isset($row['payment_status']) ? $row['payment_status'] : '');
            $created = !empty($row['payment_created_at']) ? $row['payment_created_at'] : $row['Waktu_transaksi'];
            $amount = !empty($row['Jumlah']) ? (int) $row['Jumlah'] : (int) $row['Total_harga'];
            $method = isset($row['Metode']) && trim((string) $row['Metode']) !== '' ? $row['Metode'] : '-';
            $transactions[] = array(
                'orderId' => !empty($row['ID_Pembayaran']) ? $row['ID_Pembayaran'] : $row['ID_Booking'],
                'typeKey' => 'booking', 'type' => 'Booking Lapangan',
                'date' => $this->ownerFormatDatabaseDate(substr((string) $created, 0, 10)),
                'dateValue' => substr((string) $created, 0, 10), 'time' => substr((string) $created, 11, 5) . ' WITA',
                'customer' => $row['customer_name'], 'phone' => $row['Nomor_telepon'],
                'field' => $row['Nama_lapangan'], 'bookingDate' => $this->ownerFormatDatabaseDate($row['Tanggal']),
                'bookingTime' => substr((string) $row['Jam_Mulai'], 0, 5) . ' - ' . substr((string) $row['Jam_Selesai'], 0, 5),
                'methodKey' => $this->ownerMethodClass($method), 'method' => $method,
                'methodClass' => $this->ownerMethodClass($method), 'methodIcon' => 'fa-building-columns',
                'total' => $this->formatOwnerRupiah($amount), 'statusKey' => $status['key'],
                'status' => $status['label'], 'statusClass' => $status['class'],
            );
        }

        return $transactions;
    }

    protected function ownerReviewsFromDatabase()
    {
        $ownerId = $this->ownerDatabaseId();

        if ($ownerId === '') {
            return array();
        }

        $rows = $this->ownerData()->rows(
            "SELECT r.Rating, r.Komentar, r.created_at, u.Nama, u.Email, l.Nama_lapangan
             FROM review r INNER JOIN users u ON u.ID_User = r.ID_User
             INNER JOIN lapangan l ON l.ID_Lapangan = r.ID_Lapangan
             WHERE l.ID_Pemilik = ? ORDER BY r.created_at DESC",
            's', array($ownerId)
        );
        $reviews = array();

        foreach ($rows as $row) {
            $name = $row['Nama'];
            $reviews[] = array(
                'name' => $name, 'username' => '@' . strstr((string) $row['Email'], '@', true),
                'field' => $row['Nama_lapangan'], 'rating' => (float) $row['Rating'],
                'review' => $row['Komentar'], 'date' => $this->ownerFormatDatabaseDate(substr((string) $row['created_at'], 0, 10)),
                'time' => substr((string) $row['created_at'], 11, 5),
                'avatar' => 'https://ui-avatars.com/api/?name=' . rawurlencode($name) . '&background=245b84&color=ffffff',
            );
        }

        return $reviews;
    }

    protected function ownerReviewStatsFromDatabase()
    {
        $reviews = $this->ownerReviewsFromDatabase();
        $total = count($reviews); $sum = 0; $positive = 0;
        foreach ($reviews as $review) { $sum += $review['rating']; $positive += $review['rating'] >= 4 ? 1 : 0; }
        $average = $total > 0 ? $sum / $total : 0; $negative = $total - $positive;

        return array(
            array('label' => 'Rating Rata-rata', 'value' => number_format($average, 1) . ' / 5', 'note' => '(' . $total . ' ulasan)', 'icon' => 'fa-star', 'accent' => 'lime', 'rating' => $average),
            array('label' => 'Total Ulasan', 'value' => (string) $total, 'trend' => '0%', 'note' => 'data tersimpan', 'icon' => 'fa-comment-dots', 'accent' => 'blue'),
            array('label' => 'Ulasan Positif', 'value' => (string) $positive, 'trend' => $total ? number_format(($positive / $total) * 100, 1) . '%' : '0%', 'note' => 'dari total ulasan', 'icon' => 'fa-thumbs-up', 'accent' => 'purple'),
            array('label' => 'Ulasan Negatif', 'value' => (string) $negative, 'trend' => $total ? number_format(($negative / $total) * 100, 1) . '%' : '0%', 'note' => 'dari total ulasan', 'icon' => 'fa-thumbs-down', 'accent' => 'orange'),
        );
    }

    protected function ownerRatingDistributionFromDatabase()
    {
        $counts = array(5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0);
        foreach ($this->ownerReviewsFromDatabase() as $review) { $star = max(1, min(5, (int) round($review['rating']))); $counts[$star]++; }
        $total = max(1, array_sum($counts)); $result = array();
        foreach ($counts as $stars => $count) { $result[] = array('stars' => $stars, 'count' => $count, 'percent' => ($count / $total) * 100); }
        return $result;
    }

    protected function ownerFieldRatingsFromDatabase()
    {
        $ownerId = $this->ownerDatabaseId();
        if ($ownerId === '') { return array(); }
        $rows = $this->ownerData()->rows("SELECT l.Nama_lapangan, l.Jenis_olahraga, l.Foto, COALESCE(AVG(r.Rating),0) rating, COUNT(r.ID_Review) reviews FROM lapangan l LEFT JOIN review r ON r.ID_Lapangan=l.ID_Lapangan WHERE l.ID_Pemilik=? AND l.deleted_at IS NULL GROUP BY l.ID_Lapangan ORDER BY rating DESC", 's', array($ownerId));
        $fields = array();
        foreach ($rows as $row) { $rating=(float)$row['rating']; $fields[]=array('name'=>$row['Nama_lapangan'],'rating'=>number_format($rating,1),'reviews'=>(string)$row['reviews'],'percent'=>($rating/5)*100,'image'=>$this->ownerFieldImage($row['Foto'],$row['Jenis_olahraga'])); }
        return $fields;
    }

    protected function ownerManagedFieldsFromDatabase()
    {
        $ownerId = $this->ownerDatabaseId();
        if ($ownerId === '') { return array(); }
        $rows = $this->ownerData()->rows('SELECT * FROM lapangan WHERE ID_Pemilik=? AND deleted_at IS NULL ORDER BY created_at DESC', 's', array($ownerId));
        $fields = array();
        foreach ($rows as $row) { $fields[]=array('name'=>$row['Nama_lapangan'],'type'=>$row['Jenis_olahraga'],'location'=>$row['Lokasi'],'price'=>$this->formatOwnerRupiah($row['Harga']),'status'=>$row['Status'],'image'=>$this->ownerFieldImage($row['Foto'],$row['Jenis_olahraga'])); }
        return $fields;
    }

    protected function ownerFieldImage($photos, $type)
    {
        $decoded = json_decode((string) $photos, true);
        if (is_array($decoded) && !empty($decoded[0]) && strpos($decoded[0], '..') === false) { return app_url($decoded[0]); }
        $type = strtolower((string) $type);
        if (strpos($type, 'badminton') !== false) { return 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=500&auto=format&fit=crop'; }
        return 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=500&auto=format&fit=crop';
    }

    protected function ownerMethodClass($method)
    {
        $method = strtolower((string) $method);
        if (strpos($method, 'qris') !== false) { return 'qris'; }
        if (strpos($method, 'dana') !== false) { return 'dana'; }
        if (strpos($method, 'ovo') !== false) { return 'ovo'; }
        return 'bank';
    }

    protected function ownerFormatDatabaseDate($date)
    {
        try { return $this->formatOwnerReportDate(new \DateTimeImmutable((string) $date)); }
        catch (\Throwable $exception) { return '-'; }
    }

    protected function summaryCards()
    {
        $ownerId = $this->ownerDatabaseId();
        $data = $this->ownerData();
        $fieldCount = $ownerId !== '' ? (int) $data->value("SELECT COUNT(*) AS value FROM lapangan WHERE ID_Pemilik = ? AND deleted_at IS NULL", 's', array($ownerId)) : 0;
        $todayBookings = $ownerId !== '' ? (int) $data->value("SELECT COUNT(*) AS value FROM booking b INNER JOIN jadwal j ON j.ID_Jadwal = b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan = j.ID_Lapangan WHERE l.ID_Pemilik = ? AND j.Tanggal = CURDATE()", 's', array($ownerId)) : 0;
        $monthIncome = $ownerId !== '' ? (int) $data->value("SELECT COALESCE(SUM(p.Jumlah), 0) AS value FROM pembayaran p INNER JOIN booking b ON b.ID_Booking = p.ID_Booking INNER JOIN jadwal j ON j.ID_Jadwal = b.ID_Jadwal INNER JOIN lapangan l ON l.ID_Lapangan = j.ID_Lapangan WHERE l.ID_Pemilik = ? AND LOWER(p.Status) IN ('berhasil','dibayar','lunas','success','paid') AND YEAR(COALESCE(p.Waktu_pembayaran,p.created_at)) = YEAR(CURDATE()) AND MONTH(COALESCE(p.Waktu_pembayaran,p.created_at)) = MONTH(CURDATE())", 's', array($ownerId)) : 0;
        $rating = $ownerId !== '' ? (float) $data->value("SELECT COALESCE(AVG(r.Rating), 0) AS value FROM review r INNER JOIN lapangan l ON l.ID_Lapangan = r.ID_Lapangan WHERE l.ID_Pemilik = ?", 's', array($ownerId)) : 0;

        return array(
            array(
                'label' => 'Total Lapangan',
                'value' => (string) $fieldCount,
                'trend' => 'Lapangan Aktif',
                'note' => '',
                'icon' => 'fa-map-location-dot',
                'accent' => 'lime',
            ),
            array(
                'label' => 'Booking Hari Ini',
                'value' => (string) $todayBookings,
                'trend' => '12%',
                'note' => 'dari kemarin',
                'icon' => 'fa-calendar-days',
                'accent' => 'blue',
            ),
            array(
                'label' => 'Pendapatan Bulan Ini',
                'value' => $this->formatOwnerRupiah($monthIncome),
                'trend' => '18.6%',
                'note' => 'dari bulan lalu',
                'icon' => 'fa-rupiah-sign',
                'accent' => 'green',
            ),
            array(
                'label' => 'Rating Rata-rata',
                'value' => number_format($rating, 1),
                'trend' => '0.3',
                'note' => 'dari bulan lalu',
                'icon' => 'fa-star',
                'accent' => 'purple',
            ),
        );
    }

    protected function monthlyRevenue()
    {
        return $this->ownerMonthlyRevenueFromDatabase();

        return array(
            array('month' => 'Jan', 'amount' => 'Rp2jt', 'x' => 0, 'y' => 80),
            array('month' => 'Feb', 'amount' => 'Rp3.4jt', 'x' => 9.1, 'y' => 66),
            array('month' => 'Mar', 'amount' => 'Rp4jt', 'x' => 18.2, 'y' => 60),
            array('month' => 'Apr', 'amount' => 'Rp5.1jt', 'x' => 27.3, 'y' => 49),
            array('month' => 'Mei', 'amount' => 'Rp7jt', 'x' => 36.4, 'y' => 30),
            array('month' => 'Jun', 'amount' => 'Rp4.8jt', 'x' => 45.5, 'y' => 52),
            array('month' => 'Jul', 'amount' => 'Rp6.5jt', 'x' => 54.6, 'y' => 35),
            array('month' => 'Agu', 'amount' => 'Rp8.3jt', 'x' => 63.7, 'y' => 17),
            array('month' => 'Sep', 'amount' => 'Rp7.1jt', 'x' => 72.8, 'y' => 29),
            array('month' => 'Okt', 'amount' => 'Rp9.1jt', 'x' => 81.9, 'y' => 9),
            array('month' => 'Nov', 'amount' => 'Rp9.8jt', 'x' => 91, 'y' => 2),
            array('month' => 'Des', 'amount' => 'Rp7jt', 'x' => 100, 'y' => 30),
        );
    }

    protected function bookingStatus()
    {
        return $this->ownerBookingStatusFromDatabase();

        return array(
            array('label' => 'Selesai', 'value' => '55%', 'count' => '66', 'color' => 'lime'),
            array('label' => 'Aktif', 'value' => '25%', 'count' => '30', 'color' => 'blue'),
            array('label' => 'Pending', 'value' => '15%', 'count' => '18', 'color' => 'gold'),
            array('label' => 'Dibatalkan', 'value' => '5%', 'count' => '6', 'color' => 'red'),
        );
    }

    protected function recentBookings()
    {
        return array_slice($this->getOwnerBookings(), 0, 5);

        return array(
            array('code' => 'AS-20260617-001', 'field' => 'Arena Futsal A', 'user' => 'Ahmad', 'date' => '17 Juni 2026', 'time' => '19:00 - 20:00', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('code' => 'AS-20260617-002', 'field' => 'Arena Badminton 1', 'user' => 'Rizal', 'date' => '17 Juni 2026', 'time' => '20:00 - 21:00', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp60.000'),
            array('code' => 'AS-20260618-003', 'field' => 'Arena Futsal B', 'user' => 'Akbar', 'date' => '18 Juni 2026', 'time' => '16:00 - 17:00', 'status' => 'Selesai', 'statusClass' => 'active', 'total' => 'Rp80.000'),
            array('code' => 'AS-20260618-004', 'field' => 'Arena Badminton 2', 'user' => 'Dewi', 'date' => '18 Juni 2026', 'time' => '18:00 - 19:00', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp60.000'),
            array('code' => 'AS-20260619-005', 'field' => 'Arena Futsal A', 'user' => 'Fajar', 'date' => '19 Juni 2026', 'time' => '17:00 - 18:00', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp80.000'),
        );
    }

    protected function ownerFields()
    {
        return $this->ownerFieldsFromDatabase();

        return array(
            array('name' => 'Arena Futsal A', 'location' => 'Parepare', 'rating' => '4.8', 'reviews' => '120', 'price' => 'Rp80.000', 'status' => 'Aktif', 'visual' => 'futsal'),
            array('name' => 'Arena Badminton 1', 'location' => 'Parepare', 'rating' => '4.7', 'reviews' => '85', 'price' => 'Rp60.000', 'status' => 'Aktif', 'visual' => 'badminton'),
        );
    }

    protected function latestReviews()
    {
        $rows = array_slice($this->ownerReviewRows(), 0, 3);
        $reviews = array();

        foreach ($rows as $row) {
            $reviews[] = array('name' => $row['name'], 'time' => $row['date'], 'rating' => $row['rating'], 'text' => $row['review']);
        }

        return $reviews;

        return array(
            array('name' => 'Rahman', 'time' => '2 hari lalu', 'rating' => 5, 'text' => 'Lapangan bersih dan nyaman, pelayanan ramah, rekomendasi!'),
            array('name' => 'Akbar', 'time' => '3 hari lalu', 'rating' => 4, 'text' => 'Parkiran luas dan lokasi strategis, mantap!'),
            array('name' => 'Dewi', 'time' => '5 hari lalu', 'rating' => 5, 'text' => 'Fasilitas lengkap dan terawat dengan baik.'),
        );
    }

    protected function getAllLapangan($ownerId)
    {
        $rows = (new Lapangan())->allByOwner($ownerId);
        $fields = array();

        foreach ($rows as $row) {
            $priceNumber = isset($row['Harga']) ? (int) $row['Harga'] : 0;
            $type = isset($row['Jenis_olahraga']) ? $row['Jenis_olahraga'] : '';
            $status = isset($row['Status']) && trim((string) $row['Status']) !== '' ? $row['Status'] : 'Aktif';

            $fields[] = array(
                'id' => isset($row['ID_Lapangan']) ? $row['ID_Lapangan'] : '',
                'name' => isset($row['Nama_lapangan']) ? $row['Nama_lapangan'] : '',
                'type' => $type,
                'location' => isset($row['Lokasi']) ? $row['Lokasi'] : '',
                'price' => $this->formatRupiah($priceNumber),
                'priceNumber' => $priceNumber,
                'status' => $status,
                'cardStatus' => $status,
                'rating' => '0',
                'reviews' => '0',
                'visual' => $this->lapanganVisual($type),
                'description' => isset($row['Deskripsi']) && trim((string) $row['Deskripsi']) !== '' ? $row['Deskripsi'] : 'Belum ada deskripsi.',
                'hours' => '06:00 - 23:00 Setiap Hari',
                'facilities' => $this->decodeLapanganFacilities(isset($row['Fasilitas']) ? $row['Fasilitas'] : ''),
                'photos' => $this->lapanganPhotoPayload(isset($row['Foto']) ? $row['Foto'] : ''),
                'rules' => array('Jaga kebersihan area lapangan', 'Gunakan perlengkapan olahraga yang sesuai'),
            );
        }

        return $fields;
    }

    protected function lapanganPostData($defaultStatus)
    {
        $facilities = array();

        if (isset($_POST['facilities']) && is_array($_POST['facilities'])) {
            $facilities = $_POST['facilities'];
        } elseif (isset($_POST['fasilitas']) && is_array($_POST['fasilitas'])) {
            $facilities = $_POST['fasilitas'];
        }

        $status = isset($_POST['status']) ? trim((string) $_POST['status']) : $defaultStatus;

        if (isset($_POST['active'])) {
            $status = 'Aktif';
        } elseif ($defaultStatus === 'Nonaktif') {
            $status = 'Nonaktif';
        }

        return array(
            'name' => $this->postText(array('name', 'nama_lapangan')),
            'location' => $this->postText(array('location', 'lokasi')),
            'type' => $this->postText(array('type', 'jenis_lapangan')),
            'price' => $this->postNumber(array('price', 'harga_per_jam')),
            'status' => $status,
            'description' => $this->postText(array('description', 'deskripsi')),
            'facilities' => $this->cleanLapanganFacilities($facilities),
        );
    }

    protected function ownerLapanganPemilikId(array $owner, $createIfMissing = false, $address = '')
    {
        $ownerUserId = isset($owner['id']) ? trim((string) $owner['id']) : '';

        if ($ownerUserId === '') {
            return '';
        }

        try {
            $connection = Database::connection();
        } catch (\Throwable $exception) {
            return '';
        }

        $statement = mysqli_prepare($connection, 'SELECT ID_Pemilik FROM pemilik_lapangan WHERE ID_User = ? LIMIT 1');

        if ($statement) {
            mysqli_stmt_bind_param($statement, 's', $ownerUserId);
            mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement);
            $row = $result ? mysqli_fetch_assoc($result) : null;
            mysqli_stmt_close($statement);

            if ($row && !empty($row['ID_Pemilik'])) {
                return $row['ID_Pemilik'];
            }
        }

        if (!$createIfMissing) {
            return '';
        }

        $fieldOwnerId = $this->generateOwnerFieldId($ownerUserId);
        $businessName = isset($owner['name']) && trim((string) $owner['name']) !== '' ? $owner['name'] : 'Pemilik Lapangan';
        $businessAddress = trim((string) $address) !== '' ? trim((string) $address) : 'Belum diisi';
        $insert = mysqli_prepare(
            $connection,
            'INSERT INTO pemilik_lapangan (ID_Pemilik, ID_User, nama_usaha, alamat) VALUES (?, ?, ?, ?)'
        );

        if (!$insert) {
            return '';
        }

        mysqli_stmt_bind_param($insert, 'ssss', $fieldOwnerId, $ownerUserId, $businessName, $businessAddress);
        $saved = mysqli_stmt_execute($insert);
        mysqli_stmt_close($insert);

        return $saved ? $fieldOwnerId : '';
    }

    protected function generateOwnerFieldId($ownerUserId)
    {
        $cleanOwnerId = preg_replace('/[^A-Za-z0-9]/', '', (string) $ownerUserId);

        if ($cleanOwnerId === '') {
            $cleanOwnerId = date('ymdHis');
        }

        return substr('PML' . $cleanOwnerId, 0, 50);
    }

    protected function postText(array $keys)
    {
        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                return trim((string) $_POST[$key]);
            }
        }

        return '';
    }

    protected function postNumber(array $keys)
    {
        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                return max(0, (int) preg_replace('/[^0-9]/', '', (string) $_POST[$key]));
            }
        }

        return 0;
    }

    protected function cleanLapanganFacilities(array $facilities)
    {
        $clean = array();

        foreach ($facilities as $facility) {
            $facility = trim((string) $facility);

            if ($facility !== '' && !in_array($facility, $clean, true)) {
                $clean[] = $facility;
            }
        }

        return $clean;
    }

    protected function decodeLapanganFacilities($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return array();
        }

        $decoded = json_decode($value, true);

        if (is_array($decoded)) {
            return $this->cleanLapanganFacilities($decoded);
        }

        return $this->cleanLapanganFacilities(explode(',', $value));
    }

    protected function lapanganPhotoPayload($value)
    {
        $photos = array();

        foreach ($this->decodeLapanganPhotos($value) as $path) {
            $photos[] = array(
                'path' => $path,
                'url' => app_url($path),
            );
        }

        return $photos;
    }

    protected function decodeLapanganPhotos($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return array();
        }

        $decoded = json_decode($value, true);

        if (is_array($decoded)) {
            return $this->cleanLapanganPhotos($decoded);
        }

        return $this->cleanLapanganPhotos(explode(',', $value));
    }

    protected function cleanLapanganPhotos(array $photos)
    {
        $clean = array();

        foreach ($photos as $photo) {
            $photo = str_replace('\\', '/', trim((string) $photo));

            if ($photo !== '' && strpos($photo, '..') === false && strpos($photo, 'storage/uploads/lapangan/') === 0 && !in_array($photo, $clean, true)) {
                $clean[] = $photo;
            }
        }

        return $clean;
    }

    protected function storeLapanganPhotos($maxFiles = 5)
    {
        $storedPaths = array();
        $maxFiles = max(0, min(5, (int) $maxFiles));

        if ($maxFiles < 1 || empty($_FILES['foto_lapangan']) || !isset($_FILES['foto_lapangan']['name'])) {
            return $storedPaths;
        }

        $uploadDir = $this->lapanganUploadDirectory();

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $names = is_array($_FILES['foto_lapangan']['name']) ? $_FILES['foto_lapangan']['name'] : array($_FILES['foto_lapangan']['name']);
        $tmpNames = is_array($_FILES['foto_lapangan']['tmp_name']) ? $_FILES['foto_lapangan']['tmp_name'] : array($_FILES['foto_lapangan']['tmp_name']);
        $errors = is_array($_FILES['foto_lapangan']['error']) ? $_FILES['foto_lapangan']['error'] : array($_FILES['foto_lapangan']['error']);
        $sizes = is_array($_FILES['foto_lapangan']['size']) ? $_FILES['foto_lapangan']['size'] : array($_FILES['foto_lapangan']['size']);
        $totalFiles = count($names);

        for ($index = 0; $index < $totalFiles && count($storedPaths) < $maxFiles; $index++) {
            if (!isset($errors[$index]) || (int) $errors[$index] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ((int) $errors[$index] !== UPLOAD_ERR_OK || empty($tmpNames[$index]) || !is_uploaded_file($tmpNames[$index])) {
                continue;
            }

            if (isset($sizes[$index]) && (int) $sizes[$index] > 5 * 1024 * 1024) {
                continue;
            }

            $imageInfo = @getimagesize($tmpNames[$index]);
            $allowedTypes = array(
                IMAGETYPE_JPEG => 'jpg',
                IMAGETYPE_PNG => 'png',
            );

            if (!$imageInfo || !isset($allowedTypes[$imageInfo[2]])) {
                continue;
            }

            $filename = 'lapangan-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowedTypes[$imageInfo[2]];
            $destination = $uploadDir . DIRECTORY_SEPARATOR . $filename;

            if (move_uploaded_file($tmpNames[$index], $destination)) {
                $storedPaths[] = 'storage/uploads/lapangan/' . $filename;
            }
        }

        return $storedPaths;
    }

    protected function deleteLapanganPhotoFiles(array $photos)
    {
        $uploadDir = realpath($this->lapanganUploadDirectory());

        if (!$uploadDir) {
            return;
        }

        foreach ($this->cleanLapanganPhotos($photos) as $photo) {
            $filename = basename($photo);
            $path = realpath($this->lapanganUploadDirectory() . DIRECTORY_SEPARATOR . $filename);

            if ($path && strpos($path, $uploadDir) === 0 && is_file($path)) {
                @unlink($path);
            }
        }
    }

    protected function lapanganUploadDirectory()
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'lapangan';
    }

    protected function formatRupiah($value)
    {
        return 'Rp' . number_format(max(0, (int) $value), 0, ',', '.');
    }

    protected function lapanganVisual($type)
    {
        $type = $this->normalizeRole($type);

        if (strpos($type, 'badminton') !== false) {
            return 'badminton';
        }

        if (strpos($type, 'futsal') !== false) {
            return 'futsal';
        }

        return 'futsal-alt';
    }

    protected function getOwnerBookings()
    {
        return $this->ownerBookingsFromDatabase();

        return array(
            array('code' => 'AS-20260617-001', 'field' => 'Arena Futsal A', 'user' => 'Ahmad', 'date' => '17 Juni 2026', 'time' => '19:00 - 20:00', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('code' => 'AS-20260617-002', 'field' => 'Arena Badminton 1', 'user' => 'Rizal', 'date' => '17 Juni 2026', 'time' => '20:00 - 21:00', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp60.000'),
            array('code' => 'AS-20260618-003', 'field' => 'Arena Futsal B', 'user' => 'Akbar', 'date' => '18 Juni 2026', 'time' => '16:00 - 17:00', 'status' => 'Selesai', 'statusClass' => 'active', 'total' => 'Rp80.000'),
            array('code' => 'AS-20260618-004', 'field' => 'Arena Badminton 2', 'user' => 'Dewi', 'date' => '18 Juni 2026', 'time' => '18:00 - 19:00', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp60.000'),
        );
    }

    protected function getSchedule()
    {
        return $this->ownerScheduleFromDatabase();

        return array(
            array('tenant' => 'Ahmad', 'field' => 'Arena Futsal A', 'date' => '16 Juni 2025', 'dateValue' => '2025-06-16', 'time' => '19:00 - 20:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('tenant' => 'Rizal', 'field' => 'Arena Badminton 1', 'date' => '16 Juni 2025', 'dateValue' => '2025-06-16', 'time' => '20:00 - 21:00', 'duration' => '1 Jam', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp60.000'),
            array('tenant' => 'Nadia', 'field' => 'Arena Futsal B', 'date' => '16 Juni 2025', 'dateValue' => '2025-06-16', 'time' => '08:00 - 09:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp75.000'),
            array('tenant' => 'Bima', 'field' => 'Arena Badminton 1', 'date' => '16 Juni 2025', 'dateValue' => '2025-06-16', 'time' => '09:00 - 10:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp60.000'),
            array('tenant' => 'Lina', 'field' => 'Arena Futsal A', 'date' => '16 Juni 2025', 'dateValue' => '2025-06-16', 'time' => '10:00 - 11:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('tenant' => 'Yusuf', 'field' => 'Arena Badminton 1', 'date' => '16 Juni 2025', 'dateValue' => '2025-06-16', 'time' => '11:00 - 12:00', 'duration' => '1 Jam', 'status' => 'Selesai', 'statusClass' => 'active', 'total' => 'Rp60.000'),
            array('tenant' => 'Maya', 'field' => 'Arena Futsal B', 'date' => '16 Juni 2025', 'dateValue' => '2025-06-16', 'time' => '12:00 - 13:00', 'duration' => '1 Jam', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp75.000'),
            array('tenant' => 'Ilham', 'field' => 'Arena Futsal A', 'date' => '16 Juni 2025', 'dateValue' => '2025-06-16', 'time' => '13:00 - 14:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('tenant' => 'Tari', 'field' => 'Arena Badminton 1', 'date' => '16 Juni 2025', 'dateValue' => '2025-06-16', 'time' => '14:00 - 15:00', 'duration' => '1 Jam', 'status' => 'Dibatalkan', 'statusClass' => 'danger', 'total' => 'Rp60.000'),
            array('tenant' => 'Hendra', 'field' => 'Arena Futsal B', 'date' => '16 Juni 2025', 'dateValue' => '2025-06-16', 'time' => '15:00 - 16:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp75.000'),
            array('tenant' => 'Putri', 'field' => 'Arena Futsal A', 'date' => '16 Juni 2025', 'dateValue' => '2025-06-16', 'time' => '16:00 - 17:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('tenant' => 'Kirana', 'field' => 'Arena Badminton 1', 'date' => '16 Juni 2025', 'dateValue' => '2025-06-16', 'time' => '17:00 - 18:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp60.000'),
            array('tenant' => 'Akbar', 'field' => 'Arena Futsal B', 'date' => '17 Juni 2025', 'dateValue' => '2025-06-17', 'time' => '16:00 - 17:00', 'duration' => '1 Jam', 'status' => 'Selesai', 'statusClass' => 'active', 'total' => 'Rp75.000'),
            array('tenant' => 'Dewi', 'field' => 'Arena Badminton 1', 'date' => '17 Juni 2025', 'dateValue' => '2025-06-17', 'time' => '18:00 - 19:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp60.000'),
            array('tenant' => 'Fajar', 'field' => 'Arena Futsal A', 'date' => '17 Juni 2025', 'dateValue' => '2025-06-17', 'time' => '17:00 - 18:00', 'duration' => '1 Jam', 'status' => 'Pending', 'statusClass' => 'warning', 'total' => 'Rp80.000'),
            array('tenant' => 'Rudi', 'field' => 'Arena Futsal A', 'date' => '18 Juni 2025', 'dateValue' => '2025-06-18', 'time' => '20:00 - 21:00', 'duration' => '1 Jam', 'status' => 'Aktif', 'statusClass' => 'success', 'total' => 'Rp80.000'),
            array('tenant' => 'Sandi', 'field' => 'Arena Badminton 1', 'date' => '18 Juni 2025', 'dateValue' => '2025-06-18', 'time' => '19:00 - 20:00', 'duration' => '1 Jam', 'status' => 'Dibatalkan', 'statusClass' => 'danger', 'total' => 'Rp60.000'),
        );
    }

    protected function getFilteredSchedule($selectedStatus, $selectedDateValue, $page)
    {
        $filtered = array();

        foreach ($this->getSchedule() as $booking) {
            $matchesStatus = $selectedStatus === 'Semua' || $booking['status'] === $selectedStatus;
            $matchesDate = $booking['dateValue'] === $selectedDateValue;

            if ($matchesStatus && $matchesDate) {
                $filtered[] = $booking;
            }
        }

        return $this->paginateSchedule($filtered, $page, 7);
    }

    protected function paginateSchedule(array $schedule, $page, $perPage)
    {
        $total = count($schedule);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $currentPage = max(1, min($totalPages, (int) $page));
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($schedule, $offset, $perPage);
        $firstItem = $total > 0 ? $offset + 1 : 0;
        $lastItem = $total > 0 ? $offset + count($items) : 0;

        return array(
            'items' => $items,
            'pagination' => array(
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'total' => $total,
                'firstItem' => $firstItem,
                'lastItem' => $lastItem,
                'perPage' => $perPage,
            ),
        );
    }

    protected function sanitizeScheduleStatus($status)
    {
        $normalizedStatus = $this->normalizeScheduleFilter($status);

        foreach ($this->scheduleStatusTabs() as $allowedStatus) {
            if ($this->normalizeScheduleFilter($allowedStatus) === $normalizedStatus) {
                return $allowedStatus;
            }
        }

        return 'Semua';
    }

    protected function sanitizeScheduleDate($date)
    {
        $date = trim((string) $date);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        return date('Y-m-d');
    }

    protected function normalizeScheduleFilter($value)
    {
        return strtolower(trim((string) $value));
    }

    protected function scheduleStatusTabs()
    {
        return array('Semua', 'Aktif', 'Pending', 'Selesai', 'Dibatalkan');
    }

    protected function formatScheduleDate($date)
    {
        $parts = explode('-', $date);
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

        if (count($parts) !== 3) {
            return $this->ownerFormatDatabaseDate(date('Y-m-d'));
        }

        $monthNumber = (int) $parts[1];
        $monthName = isset($months[$monthNumber]) ? $months[$monthNumber] : 'Juni';

        return (int) $parts[2] . ' ' . $monthName . ' ' . $parts[0];
    }

    protected function ownerRevenuePeriodTabs()
    {
        return array(
            'mingguan' => 'Mingguan',
            'bulanan' => 'Bulanan',
            'tahunan' => 'Tahunan',
        );
    }

    protected function sanitizeOwnerRevenuePeriod($period)
    {
        $period = strtolower(trim((string) $period));
        $allowed = array_keys($this->ownerRevenuePeriodTabs());

        return in_array($period, $allowed, true) ? $period : 'bulanan';
    }

    protected function resolveOwnerRevenueRange($period, $startInput, $endInput)
    {
        $baseDate = new \DateTimeImmutable('today');
        $start = null;
        $end = null;

        if ($startInput !== '' || $endInput !== '') {
            $start = $this->parseOwnerReportDate($startInput);
            $end = $this->parseOwnerReportDate($endInput);
        }

        if (!$start || !$end) {
            if ($period === 'mingguan') {
                $start = $baseDate->modify('-6 days');
                $end = $baseDate;
            } elseif ($period === 'tahunan') {
                $start = new \DateTimeImmutable($baseDate->format('Y') . '-01-01');
                $end = new \DateTimeImmutable($baseDate->format('Y') . '-12-31');
            } else {
                $start = $baseDate->modify('first day of this month');
                $end = $baseDate->modify('last day of this month');
            }
        }

        if ($start > $end) {
            $swap = $start;
            $start = $end;
            $end = $swap;
        }

        return array('start' => $start, 'end' => $end);
    }

    protected function resolveOwnerPreviousRevenueRange(\DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        $days = $start->diff($end)->days + 1;
        $previousEnd = $start->modify('-1 day');
        $previousStart = $previousEnd->modify('-' . ($days - 1) . ' days');

        return array('start' => $previousStart, 'end' => $previousEnd);
    }

    protected function revenueStats(array $transactions, array $previousTransactions, $period)
    {
        $summary = $this->ownerRevenueTotals($transactions);
        $previousSummary = $this->ownerRevenueTotals($previousTransactions);
        $days = max(1, count(array_unique(array_map(function ($transaction) {
            return $transaction['date'];
        }, $transactions))));
        $periodNotes = array(
            'mingguan' => 'dari minggu lalu',
            'bulanan' => 'dari periode sebelumnya',
            'tahunan' => 'dari periode sebelumnya',
        );
        $note = isset($periodNotes[$period]) ? $periodNotes[$period] : 'dari periode sebelumnya';

        return array(
            array('label' => 'Total Pendapatan', 'value' => $this->formatOwnerRupiah($summary['gross']), 'trend' => $this->formatOwnerTrend($summary['gross'], $previousSummary['gross']), 'note' => $note, 'icon' => 'fa-rupiah-sign', 'accent' => 'green'),
            array('label' => 'Total Transaksi', 'value' => (string) $summary['count'], 'trend' => $this->formatOwnerTrend($summary['count'], $previousSummary['count']), 'note' => $note, 'icon' => 'fa-calendar-check', 'accent' => 'cyan'),
            array('label' => 'Rata-rata per Hari', 'value' => $this->formatOwnerRupiah($summary['count'] > 0 ? (int) round($summary['gross'] / $days) : 0), 'trend' => $this->formatOwnerTrend($summary['average'], $previousSummary['average']), 'note' => $note, 'icon' => 'fa-chart-column', 'accent' => 'purple'),
            array('label' => 'Potongan Platform (2%)', 'value' => $this->formatOwnerRupiah($summary['fee']), 'trend' => $this->formatOwnerTrend($summary['fee'], $previousSummary['fee']), 'note' => $note, 'icon' => 'fa-percent', 'accent' => 'gold'),
        );
    }

    protected function dailyRevenueChart(array $transactions, \DateTimeImmutable $start, \DateTimeImmutable $end, $period)
    {
        $labels = array();
        $buckets = array();
        $cursor = $start;
        $groupByMonth = $period === 'tahunan' || $start->diff($end)->days > 62;

        if ($groupByMonth) {
            $cursor = new \DateTimeImmutable($start->format('Y-m-01'));
            $last = new \DateTimeImmutable($end->format('Y-m-01'));

            while ($cursor <= $last) {
                $key = $cursor->format('Y-m');
                $labels[$key] = $this->shortOwnerMonthName((int) $cursor->format('n'));
                $buckets[$key] = 0;
                $cursor = $cursor->modify('+1 month');
            }
        } else {
            while ($cursor <= $end) {
                $key = $cursor->format('Y-m-d');
                $labels[$key] = (int) $cursor->format('j') . ' ' . $this->shortOwnerMonthName((int) $cursor->format('n'));
                $buckets[$key] = 0;
                $cursor = $cursor->modify('+1 day');
            }
        }

        foreach ($transactions as $transaction) {
            $date = $this->parseOwnerReportDate($transaction['date']);

            if (!$date) {
                continue;
            }

            $key = $groupByMonth ? $date->format('Y-m') : $date->format('Y-m-d');

            if (isset($buckets[$key])) {
                $buckets[$key] += $this->ownerRupiahToInt($transaction['total']);
            }
        }

        $values = array_values($buckets);
        $max = max(1, max($values));
        $count = count($values);
        $points = array();
        $index = 0;
        $highlightIndex = 0;
        $highlightAmount = -1;

        foreach ($buckets as $key => $amount) {
            if ($amount > $highlightAmount) {
                $highlightAmount = $amount;
                $highlightIndex = $index;
            }

            $points[] = array(
                'label' => $labels[$key],
                'amount' => $this->formatOwnerRupiah($amount),
                'x' => $count > 1 ? round(($index / ($count - 1)) * 100, 2) : 50,
                'y' => round(92 - (($amount / $max) * 82), 2),
            );
            $index++;
        }

        if (isset($points[$highlightIndex])) {
            $points[$highlightIndex]['highlight'] = true;
        }

        return array(
            'points' => $points,
            'labels' => array_values($labels),
        );
    }

    protected function revenueSummary(array $transactions)
    {
        $summary = $this->ownerRevenueTotals($transactions);

        return array(
            array('label' => 'Pendapatan Kotor', 'value' => $this->formatOwnerRupiah($summary['gross']), 'icon' => 'fa-money-bill-trend-up', 'accent' => 'green', 'tone' => 'positive'),
            array('label' => 'Potongan Platform (2%)', 'value' => '-' . $this->formatOwnerRupiah($summary['fee']), 'icon' => 'fa-percent', 'accent' => 'red', 'tone' => 'negative'),
            array('label' => 'Pendapatan Bersih', 'value' => $this->formatOwnerRupiah($summary['net']), 'icon' => 'fa-wallet', 'accent' => 'blue', 'tone' => 'positive'),
            array('label' => 'Dibayarkan ke Rekening', 'value' => $this->formatOwnerRupiah($summary['net']), 'icon' => 'fa-building-columns', 'accent' => 'purple', 'tone' => 'positive'),
            array('label' => 'Saldo Tersedia', 'value' => 'Rp0', 'icon' => 'fa-coins', 'accent' => 'gold', 'tone' => 'neutral'),
        );
    }

    protected function revenueTransactions()
    {
        return $this->ownerRevenueTransactionsFromDatabase();

        $names = array('Ahmad', 'Rizal', 'Akbar', 'Dewi', 'Fajar', 'Sinta', 'Bima', 'Nadia', 'Raka', 'Putri', 'Gilang', 'Maya');
        $fields = array('Arena Futsal A', 'Arena Badminton 1', 'Arena Futsal B', 'Arena Basket Indoor');
        $methods = array(
            array('method' => 'Transfer Bank', 'methodIcon' => 'fa-building-columns', 'methodClass' => 'bank'),
            array('method' => 'E-Wallet (OVO)', 'methodIcon' => 'fa-wallet', 'methodClass' => 'ovo'),
            array('method' => 'E-Wallet (Dana)', 'methodIcon' => 'fa-wallet', 'methodClass' => 'dana'),
        );
        $amounts = array(60000, 75000, 80000, 90000, 120000, 150000, 180000);
        $transactions = array();

        for ($day = 1; $day <= 30; $day++) {
            $itemsInDay = $day % 5 === 0 ? 4 : (($day % 2) + 2);

            for ($item = 0; $item < $itemsInDay; $item++) {
                $seed = ($day + $item) % count($names);
                $amount = $amounts[($day + ($item * 2)) % count($amounts)];
                $fee = (int) round($amount * 0.02);
                $method = $methods[($day + $item) % count($methods)];

                $transactions[] = array_merge(array(
                    'date' => $day . ' Jun 2025',
                    'field' => $fields[($day + $item) % count($fields)],
                    'tenant' => $names[$seed],
                    'total' => $this->formatOwnerRupiah($amount),
                    'fee' => $this->formatOwnerRupiah($fee),
                    'net' => $this->formatOwnerRupiah($amount - $fee),
                    'status' => 'Dibayar',
                ), $method);
            }
        }

        return array_reverse($transactions);
    }

    protected function ownerRevenueTotals(array $transactions)
    {
        $gross = 0;
        $fee = 0;
        $net = 0;

        foreach ($transactions as $transaction) {
            $gross += $this->ownerRupiahToInt($transaction['total']);
            $fee += $this->ownerRupiahToInt($transaction['fee']);
            $net += $this->ownerRupiahToInt($transaction['net']);
        }

        return array(
            'count' => count($transactions),
            'gross' => $gross,
            'fee' => $fee,
            'net' => $net,
            'average' => count($transactions) > 0 ? (int) round($gross / count($transactions)) : 0,
        );
    }

    protected function formatOwnerTrend($current, $previous)
    {
        if ((int) $previous <= 0) {
            return (int) $current > 0 ? '100%' : '0%';
        }

        $trend = (($current - $previous) / $previous) * 100;

        return number_format(abs($trend), 1, ',', '.') . '%';
    }

    protected function shortOwnerMonthName($monthNumber)
    {
        $months = array(
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        );

        return isset($months[$monthNumber]) ? $months[$monthNumber] : '';
    }

    protected function sanitizeOwnerReportPeriod($period)
    {
        $period = strtolower(trim((string) $period));
        $allowed = array('hari_ini', 'kemarin', '7_hari', '30_hari', 'bulan_ini', 'kustom');

        return in_array($period, $allowed, true) ? $period : '30_hari';
    }

    protected function sanitizeOwnerReportType($type)
    {
        $type = strtolower(trim((string) $type));
        $allowed = array('pendapatan', 'transaksi', 'potongan');

        return in_array($type, $allowed, true) ? $type : 'pendapatan';
    }

    protected function sanitizeOwnerReportFormat($format)
    {
        $format = strtolower(trim((string) $format));
        $allowed = array('xlsx', 'pdf', 'csv');

        return in_array($format, $allowed, true) ? $format : 'xlsx';
    }

    protected function resolveOwnerReportRange($period, $startInput, $endInput)
    {
        $baseDate = new \DateTimeImmutable('today');
        $start = $baseDate->modify('first day of this month');
        $end = $baseDate;

        if ($period === 'hari_ini') {
            $start = $baseDate;
            $end = $baseDate;
        } elseif ($period === 'kemarin') {
            $start = $baseDate->modify('-1 day');
            $end = $start;
        } elseif ($period === '7_hari') {
            $start = $baseDate->modify('-6 days');
            $end = $baseDate;
        } elseif ($period === '30_hari') {
            $start = $baseDate->modify('-29 days');
            $end = $baseDate;
        } elseif ($period === 'bulan_ini') {
            $start = $baseDate->modify('first day of this month');
            $end = $baseDate->modify('last day of this month');
        } elseif ($period === 'kustom') {
            $start = $this->parseOwnerReportDate($startInput);
            $end = $this->parseOwnerReportDate($endInput);

            if (!$start) {
                $start = $baseDate->modify('first day of this month');
            }

            if (!$end) {
                $end = $baseDate->modify('last day of this month');
            }
        }

        if ($start > $end) {
            $swap = $start;
            $start = $end;
            $end = $swap;
        }

        return array('start' => $start, 'end' => $end);
    }

    protected function buildOwnerRevenueReport(array $owner, $type, \DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        $titleMap = array(
            'pendapatan' => 'Laporan Pendapatan',
            'transaksi' => 'Laporan Transaksi',
            'potongan' => 'Laporan Potongan Platform',
        );
        $slugMap = array(
            'pendapatan' => 'laporan-pendapatan',
            'transaksi' => 'laporan-transaksi',
            'potongan' => 'laporan-potongan-platform',
        );
        $headersMap = array(
            'pendapatan' => array('Tanggal', 'Lapangan', 'Nama Penyewa', 'Total', 'Potongan Platform', 'Pendapatan Bersih', 'Status'),
            'transaksi' => array('Tanggal', 'Nama Penyewa', 'Lapangan', 'Metode Pembayaran', 'Total', 'Status'),
            'potongan' => array('Tanggal', 'Lapangan', 'Total Transaksi', 'Potongan Platform', 'Pendapatan Bersih'),
        );
        $transactions = $this->filterOwnerRevenueTransactions($start, $end);
        $rows = array();
        $totalGross = 0;
        $totalFee = 0;
        $totalNet = 0;

        foreach ($transactions as $transaction) {
            $totalGross += $this->ownerRupiahToInt($transaction['total']);
            $totalFee += $this->ownerRupiahToInt($transaction['fee']);
            $totalNet += $this->ownerRupiahToInt($transaction['net']);

            if ($type === 'transaksi') {
                $rows[] = array(
                    $transaction['date'],
                    $transaction['tenant'],
                    $transaction['field'],
                    $transaction['method'],
                    $transaction['total'],
                    $transaction['status'],
                );
                continue;
            }

            if ($type === 'potongan') {
                $rows[] = array(
                    $transaction['date'],
                    $transaction['field'],
                    $transaction['total'],
                    $transaction['fee'],
                    $transaction['net'],
                );
                continue;
            }

            $rows[] = array(
                $transaction['date'],
                $transaction['field'],
                $transaction['tenant'],
                $transaction['total'],
                $transaction['fee'],
                $transaction['net'],
                $transaction['status'],
            );
        }

        if (empty($rows)) {
            $emptyRow = array('Tidak ada data pada periode ini');

            while (count($emptyRow) < count($headersMap[$type])) {
                $emptyRow[] = '';
            }

            $rows[] = $emptyRow;
        }

        return array(
            'title' => $titleMap[$type],
            'slug' => $slugMap[$type],
            'ownerName' => isset($owner['name']) ? $owner['name'] : 'Pemilik Arena',
            'period' => $this->formatOwnerReportPeriod($start, $end),
            'generatedAt' => date('d/m/Y H:i'),
            'headers' => $headersMap[$type],
            'rows' => $rows,
            'summary' => array(
                array('Total Transaksi', (string) count($transactions)),
                array('Pendapatan Kotor', $this->formatOwnerRupiah($totalGross)),
                array('Potongan Platform', $this->formatOwnerRupiah($totalFee)),
                array('Pendapatan Bersih', $this->formatOwnerRupiah($totalNet)),
            ),
        );
    }

    protected function filterOwnerRevenueTransactions(\DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        $filtered = array();

        foreach ($this->revenueTransactions() as $transaction) {
            $date = $this->parseOwnerReportDate($transaction['date']);

            if ($date && $date >= $start && $date <= $end) {
                $filtered[] = $transaction;
            }
        }

        return $filtered;
    }

    protected function parseOwnerReportDate($date)
    {
        $date = trim((string) $date);

        if ($date === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return new \DateTimeImmutable($date);
        }

        $months = array(
            'jan' => 1,
            'januari' => 1,
            'feb' => 2,
            'februari' => 2,
            'mar' => 3,
            'maret' => 3,
            'apr' => 4,
            'april' => 4,
            'mei' => 5,
            'may' => 5,
            'jun' => 6,
            'juni' => 6,
            'jul' => 7,
            'juli' => 7,
            'agu' => 8,
            'agus' => 8,
            'agustus' => 8,
            'aug' => 8,
            'sep' => 9,
            'september' => 9,
            'okt' => 10,
            'oktober' => 10,
            'oct' => 10,
            'nov' => 11,
            'november' => 11,
            'des' => 12,
            'desember' => 12,
            'dec' => 12,
        );
        $normalized = strtolower(preg_replace('/\s+/', ' ', str_replace(array(',', '.'), ' ', $date)));

        if (!preg_match('/^(\d{1,2})\s+([a-z]+)\s+(\d{4})$/', $normalized, $matches)) {
            return null;
        }

        $monthName = $matches[2];

        if (!isset($months[$monthName])) {
            return null;
        }

        return new \DateTimeImmutable(sprintf('%04d-%02d-%02d', (int) $matches[3], $months[$monthName], (int) $matches[1]));
    }

    protected function formatOwnerReportPeriod(\DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
            return $this->formatOwnerReportDate($start);
        }

        return $this->formatOwnerReportDate($start) . ' - ' . $this->formatOwnerReportDate($end);
    }

    protected function formatOwnerReportDate(\DateTimeImmutable $date)
    {
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
        $monthNumber = (int) $date->format('n');

        return (int) $date->format('j') . ' ' . $months[$monthNumber] . ' ' . $date->format('Y');
    }

    protected function ownerRupiahToInt($value)
    {
        return (int) preg_replace('/[^0-9]/', '', (string) $value);
    }

    protected function formatOwnerRupiah($amount)
    {
        return 'Rp' . number_format((int) $amount, 0, ',', '.');
    }

    protected function ownerReportSpreadsheetRows(array $report)
    {
        $summaryLabels = array();
        $summaryValues = array();

        foreach ($report['summary'] as $item) {
            $summaryLabels[] = $item[0];
            $summaryValues[] = $item[1];
        }

        $rows = array(
            array('ARENA SPORT'),
            array($report['title']),
            array('Pemilik', $report['ownerName'], 'Periode', $report['period'], 'Dibuat', $report['generatedAt']),
            array(''),
            array('Ringkasan Laporan'),
            $summaryLabels,
            $summaryValues,
            array(''),
            $report['headers'],
        );

        foreach ($report['rows'] as $row) {
            $rows[] = $row;
        }

        $rows[] = array('');
        $rows[] = array('Catatan', 'Laporan ini dibuat otomatis oleh Arena Sport sesuai filter periode dan tipe laporan yang dipilih.');

        return $rows;
    }

    protected function ownerReportColumnCount(array $report)
    {
        return max(7, count($report['headers']), count($report['summary']));
    }

    protected function sendOwnerCsvReport(array $report, $filename)
    {
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF");

        foreach ($this->ownerReportSpreadsheetRows($report) as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        $this->clearOwnerReportOutputBuffer();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $this->sanitizeOwnerReportFilename($filename) . '"');
        header('Content-Length: ' . strlen($content));
        echo $content;
        exit;
    }

    protected function sendOwnerXlsxReport(array $report, $filename)
    {
        if (!class_exists('\ZipArchive')) {
            $this->sendOwnerCsvReport($report, preg_replace('/\.xlsx$/', '.csv', $filename));
        }

        $tempFile = @tempnam($this->ownerReportTempDirectory(), 'arena-report-');

        if (!$tempFile) {
            $tempFile = @tempnam(sys_get_temp_dir(), 'arena-report-');
        }

        $zip = new \ZipArchive();

        if (!$tempFile || $zip->open($tempFile, \ZipArchive::OVERWRITE) !== true) {
            $this->sendOwnerCsvReport($report, preg_replace('/\.xlsx$/', '.csv', $filename));
        }

        $zip->addFromString('[Content_Types].xml', $this->ownerXlsxContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->ownerXlsxRootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->ownerXlsxWorkbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->ownerXlsxWorkbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->ownerXlsxStylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->ownerXlsxSheetXml($report));
        $zip->close();

        $this->clearOwnerReportOutputBuffer();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $this->sanitizeOwnerReportFilename($filename) . '"');
        header('Content-Length: ' . filesize($tempFile));
        readfile($tempFile);
        unlink($tempFile);
        exit;
    }

    protected function ownerReportTempDirectory()
    {
        $directory = __DIR__ . '/../../storage/logs';

        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        return is_dir($directory) && is_writable($directory) ? $directory : sys_get_temp_dir();
    }

    protected function sendOwnerPdfReport(array $report, $filename)
    {
        $content = $this->ownerBuildDesignedPdf($report);
        $this->clearOwnerReportOutputBuffer();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $this->sanitizeOwnerReportFilename($filename) . '"');
        header('Content-Length: ' . strlen($content));
        echo $content;
        exit;
    }

    protected function ownerXlsxContentTypesXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    protected function ownerXlsxRootRelsXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    protected function ownerXlsxWorkbookXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Laporan" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    protected function ownerXlsxWorkbookRelsXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    protected function ownerXlsxStylesXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="7">'
            . '<font><sz val="11"/><color rgb="FF1F2937"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="22"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="15"/><color rgb="FFB7FF4D"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="10"/><color rgb="FF486070"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="14"/><color rgb="FF0F2410"/><name val="Calibri"/></font>'
            . '<font><i/><sz val="10"/><color rgb="FF64748B"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="8">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF07121F"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF64D82F"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFEAF8DD"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF102033"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFF6FAF2"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFFFFFFF"/><bgColor indexed="64"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="2">'
            . '<border/>'
            . '<border><left style="thin"><color rgb="FFD8E1EA"/></left><right style="thin"><color rgb="FFD8E1EA"/></right><top style="thin"><color rgb="FFD8E1EA"/></top><bottom style="thin"><color rgb="FFD8E1EA"/></bottom></border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="11">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="2" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="6" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="3" fillId="3" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="4" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="5" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="3" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="7" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="6" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="6" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '<dxfs count="0"/>'
            . '<tableStyles count="0" defaultTableStyle="TableStyleMedium2" defaultPivotStyle="PivotStyleLight16"/>'
            . '</styleSheet>';
    }

    protected function ownerXlsxSheetXml(array $report)
    {
        $rows = $this->ownerReportSpreadsheetRows($report);
        $columnCount = $this->ownerReportColumnCount($report);
        $lastColumn = $this->ownerXlsxColumnName($columnCount);
        $tableHeaderRow = 9;
        $tableFirstRow = $tableHeaderRow + 1;
        $tableLastRow = $tableHeaderRow + count($report['rows']);
        $noteRow = $tableLastRow + 2;
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="9" topLeftCell="A10" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="18"/>'
            . '<cols>'
            . '<col min="1" max="1" width="16" customWidth="1"/>'
            . '<col min="2" max="2" width="24" customWidth="1"/>'
            . '<col min="3" max="3" width="22" customWidth="1"/>'
            . '<col min="4" max="4" width="20" customWidth="1"/>'
            . '<col min="5" max="5" width="20" customWidth="1"/>'
            . '<col min="6" max="6" width="22" customWidth="1"/>'
            . '<col min="7" max="7" width="16" customWidth="1"/>'
            . '</cols>'
            . '<sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 1;
            $style = $this->ownerXlsxRowStyle($rowNumber, $tableFirstRow, $tableLastRow, $noteRow);
            $height = $this->ownerXlsxRowHeight($rowNumber);
            $xml .= '<row r="' . $rowNumber . '"' . ($height ? ' ht="' . $height . '" customHeight="1"' : '') . '>';
            $cellsToRender = in_array($rowNumber, array(1, 2, 5, $noteRow), true) ? 1 : max($columnCount, count($row));

            for ($columnIndex = 0; $columnIndex < $cellsToRender; $columnIndex++) {
                $value = isset($row[$columnIndex]) ? $row[$columnIndex] : '';
                $cell = $this->ownerXlsxColumnName($columnIndex + 1) . $rowNumber;
                $xml .= '<c r="' . $cell . '"' . ($style ? ' s="' . $style . '"' : '') . ' t="inlineStr"><is><t xml:space="preserve">'
                    . htmlspecialchars((string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8')
                    . '</t></is></c>';
            }

            $xml .= '</row>';
        }

        $xml .= '</sheetData>'
            . '<autoFilter ref="A' . $tableHeaderRow . ':' . $lastColumn . $tableLastRow . '"/>'
            . '<mergeCells count="4">'
            . '<mergeCell ref="A1:' . $lastColumn . '1"/>'
            . '<mergeCell ref="A2:' . $lastColumn . '2"/>'
            . '<mergeCell ref="A5:' . $lastColumn . '5"/>'
            . '<mergeCell ref="A' . $noteRow . ':' . $lastColumn . $noteRow . '"/>'
            . '</mergeCells>'
            . '<pageMargins left="0.4" right="0.4" top="0.6" bottom="0.6" header="0.3" footer="0.3"/>'
            . '</worksheet>';

        return $xml;
    }

    protected function ownerXlsxRowStyle($rowNumber, $tableFirstRow, $tableLastRow, $noteRow)
    {
        if ($rowNumber === 1) {
            return 1;
        }

        if ($rowNumber === 2) {
            return 2;
        }

        if ($rowNumber === 3) {
            return 3;
        }

        if ($rowNumber === 5) {
            return 4;
        }

        if ($rowNumber === 6) {
            return 5;
        }

        if ($rowNumber === 7) {
            return 6;
        }

        if ($rowNumber === 9) {
            return 7;
        }

        if ($rowNumber >= $tableFirstRow && $rowNumber <= $tableLastRow) {
            return $rowNumber % 2 === 0 ? 8 : 9;
        }

        if ($rowNumber === $noteRow) {
            return 10;
        }

        return 0;
    }

    protected function ownerXlsxRowHeight($rowNumber)
    {
        $heights = array(
            1 => 30,
            2 => 24,
            3 => 26,
            5 => 24,
            6 => 28,
            7 => 32,
            9 => 28,
        );

        return isset($heights[$rowNumber]) ? $heights[$rowNumber] : 0;
    }

    protected function ownerBuildDesignedPdf(array $report)
    {
        $rowChunks = $this->ownerPdfReportRowChunks($report['rows']);
        $pageCount = count($rowChunks);
        $fontRegularObjectNumber = 3 + ($pageCount * 2);
        $fontBoldObjectNumber = $fontRegularObjectNumber + 1;
        $objects = array(
            '<< /Type /Catalog /Pages 2 0 R >>',
            '__PAGES__',
        );
        $pageReferences = array();

        foreach ($rowChunks as $pageIndex => $rows) {
            $pageObjectNumber = count($objects) + 1;
            $contentObjectNumber = $pageObjectNumber + 1;
            $pageReferences[] = $pageObjectNumber . ' 0 R';
            $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 ' . $fontRegularObjectNumber . ' 0 R /F2 ' . $fontBoldObjectNumber . ' 0 R >> >> /Contents ' . $contentObjectNumber . ' 0 R >>';
            $objects[] = $this->ownerPdfReportPageStream($report, $rows, $pageIndex + 1, $pageCount);
        }

        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';
        $objects[1] = '<< /Type /Pages /Kids [' . implode(' ', $pageReferences) . '] /Count ' . $pageCount . ' >>';

        $pdf = "%PDF-1.4\n";
        $offsets = array(0);

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($index = 1; $index < count($offsets); $index++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$index]);
        }

        $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    protected function ownerPdfReportRowChunks(array $rows)
    {
        $chunks = array();
        $remaining = $rows;
        $chunks[] = array_splice($remaining, 0, 10);

        while (!empty($remaining)) {
            $chunks[] = array_splice($remaining, 0, 16);
        }

        return $chunks;
    }

    protected function ownerPdfReportPageStream(array $report, array $rows, $pageNumber, $pageCount)
    {
        $commands = array();
        $isFirstPage = $pageNumber === 1;
        $tableTop = $isFirstPage ? 360 : 468;

        $commands[] = $this->ownerPdfRectCommand(0, 0, 842, 595, 'F7FAF2');
        $commands[] = $this->ownerPdfRectCommand(0, 506, 842, 89, '07121F');
        $commands[] = $this->ownerPdfRectCommand(0, 506, 842, 6, '64D82F');
        $commands[] = $this->ownerPdfTextCommand('ARENA SPORT', 40, 555, 19, 'F2', 'FFFFFF');
        $commands[] = $this->ownerPdfTextCommand($report['title'], 40, 531, 13, 'F2', 'B7FF4D');
        $commands[] = $this->ownerPdfTextCommand('Pemilik', 596, 562, 8, 'F1', 'A8B6C5');
        $commands[] = $this->ownerPdfTextCommand($this->ownerPdfFitText($report['ownerName'], 34), 596, 548, 10, 'F2', 'FFFFFF');
        $commands[] = $this->ownerPdfTextCommand('Periode', 596, 531, 8, 'F1', 'A8B6C5');
        $commands[] = $this->ownerPdfTextCommand($this->ownerPdfFitText($report['period'], 34), 596, 517, 9, 'F2', 'FFFFFF');

        if ($isFirstPage) {
            $commands[] = $this->ownerPdfTextCommand('Ringkasan Laporan', 40, 480, 12, 'F2', '102033');
            $commands = array_merge($commands, $this->ownerPdfSummaryCardCommands($report['summary']));
        } else {
            $commands[] = $this->ownerPdfTextCommand('Lanjutan Data Laporan', 40, 486, 12, 'F2', '102033');
        }

        $commands = array_merge($commands, $this->ownerPdfTableCommands($report['headers'], $rows, $tableTop));
        $commands[] = $this->ownerPdfRectCommand(40, 42, 762, 1.2, 'D7E6C8');
        $commands[] = $this->ownerPdfTextCommand('Dibuat otomatis oleh Arena Sport pada ' . $report['generatedAt'], 40, 24, 8, 'F1', '64748B');
        $commands[] = $this->ownerPdfTextCommand('Hal. ' . $pageNumber . '/' . $pageCount, 748, 24, 8, 'F2', '64748B');
        $stream = implode("\n", $commands);

        return "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
    }

    protected function ownerPdfSummaryCardCommands(array $summary)
    {
        $commands = array();
        $x = 40;
        $y = 414;
        $width = 178;
        $height = 52;
        $gap = 13;

        foreach ($summary as $index => $item) {
            $cardX = $x + (($width + $gap) * $index);
            $commands[] = $this->ownerPdfRectCommand($cardX, $y, $width, $height, 'FFFFFF', 'DCE8D1');
            $commands[] = $this->ownerPdfRectCommand($cardX, $y + $height - 5, $width, 5, $index === 2 ? 'F35E6B' : '64D82F');
            $commands[] = $this->ownerPdfTextCommand($item[0], $cardX + 12, $y + 31, 8, 'F2', '526372');
            $commands[] = $this->ownerPdfTextCommand($item[1], $cardX + 12, $y + 13, 13, 'F2', $index === 2 ? 'D83A48' : '173915');
        }

        return $commands;
    }

    protected function ownerPdfTableCommands(array $headers, array $rows, $top)
    {
        $commands = array();
        $x = 40;
        $headerHeight = 27;
        $rowHeight = 24;
        $widths = $this->ownerPdfReportColumnWidths(count($headers));
        $tableWidth = array_sum($widths);
        $commands[] = $this->ownerPdfRectCommand($x, $top - $headerHeight, $tableWidth, $headerHeight, '102033');
        $currentX = $x;

        foreach ($headers as $index => $header) {
            $commands[] = $this->ownerPdfTextCommand($this->ownerPdfFitText($header, $this->ownerPdfMaxCharsForWidth($widths[$index])), $currentX + 6, $top - 17, 8, 'F2', 'FFFFFF');
            $currentX += $widths[$index];
        }

        foreach ($rows as $rowIndex => $row) {
            $rowTop = $top - $headerHeight - ($rowHeight * $rowIndex);
            $fill = $rowIndex % 2 === 0 ? 'FFFFFF' : 'F0F7EA';
            $commands[] = $this->ownerPdfRectCommand($x, $rowTop - $rowHeight, $tableWidth, $rowHeight, $fill, 'DDE8D6');
            $currentX = $x;

            foreach ($headers as $columnIndex => $header) {
                $value = isset($row[$columnIndex]) ? $row[$columnIndex] : '';
                $textColor = stripos($header, 'potongan') !== false ? 'C84650' : (stripos($header, 'bersih') !== false ? '247A25' : '1F2937');
                $commands[] = $this->ownerPdfTextCommand($this->ownerPdfFitText($value, $this->ownerPdfMaxCharsForWidth($widths[$columnIndex])), $currentX + 6, $rowTop - 16, 8, $columnIndex >= 3 ? 'F2' : 'F1', $textColor);
                $currentX += $widths[$columnIndex];
            }
        }

        return $commands;
    }

    protected function ownerPdfReportColumnWidths($headerCount)
    {
        if ($headerCount === 7) {
            return array(82, 145, 115, 96, 102, 118, 82);
        }

        if ($headerCount === 6) {
            return array(88, 145, 145, 132, 115, 95);
        }

        if ($headerCount === 5) {
            return array(92, 190, 148, 150, 150);
        }

        $width = (int) floor(740 / max(1, $headerCount));

        return array_fill(0, max(1, $headerCount), $width);
    }

    protected function ownerPdfMaxCharsForWidth($width)
    {
        return max(8, (int) floor($width / 5.4));
    }

    protected function ownerPdfFitText($text, $maxLength)
    {
        $text = $this->ownerPdfText($text);

        if (strlen($text) <= $maxLength) {
            return $text;
        }

        return substr($text, 0, max(1, $maxLength - 3)) . '...';
    }

    protected function ownerPdfRectCommand($x, $y, $width, $height, $fillHex, $strokeHex = '', $strokeWidth = 0.6)
    {
        $fill = $this->ownerPdfHexColor($fillHex) . ' rg';

        if ($strokeHex !== '') {
            return $fill . ' ' . $this->ownerPdfHexColor($strokeHex) . ' RG ' . $strokeWidth . ' w ' . $x . ' ' . $y . ' ' . $width . ' ' . $height . ' re B';
        }

        return $fill . ' ' . $x . ' ' . $y . ' ' . $width . ' ' . $height . ' re f';
    }

    protected function ownerPdfTextCommand($text, $x, $y, $fontSize, $fontName, $hexColor)
    {
        return $this->ownerPdfHexColor($hexColor) . ' rg BT /' . $fontName . ' ' . $fontSize . ' Tf ' . $x . ' ' . $y . ' Td (' . $this->ownerPdfEscape($this->ownerPdfText($text)) . ') Tj ET';
    }

    protected function ownerPdfHexColor($hex)
    {
        $hex = ltrim((string) $hex, '#');

        if (strlen($hex) !== 6) {
            $hex = '000000';
        }

        $red = hexdec(substr($hex, 0, 2)) / 255;
        $green = hexdec(substr($hex, 2, 2)) / 255;
        $blue = hexdec(substr($hex, 4, 2)) / 255;

        return sprintf('%.3F %.3F %.3F', $red, $green, $blue);
    }

    protected function ownerXlsxColumnName($number)
    {
        $name = '';

        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)) . $name;
            $number = (int) floor($number / 26);
        }

        return $name;
    }

    protected function ownerBuildSimplePdf(array $lines)
    {
        $pages = array();
        $currentPage = array();
        $currentLineCount = 0;

        foreach ($lines as $index => $line) {
            $wrappedLines = $this->ownerPdfWrapLine($line, $index === 0 ? 86 : 118);

            foreach ($wrappedLines as $wrappedLineIndex => $wrappedLine) {
                $lineHeight = $index === 0 && $wrappedLineIndex === 0 ? 2 : 1;

                if ($currentLineCount + $lineHeight > 30) {
                    $pages[] = $currentPage;
                    $currentPage = array();
                    $currentLineCount = 0;
                }

                $currentPage[] = array(
                    'text' => $wrappedLine,
                    'title' => $index === 0 && $wrappedLineIndex === 0,
                );
                $currentLineCount += $lineHeight;
            }
        }

        if (!empty($currentPage)) {
            $pages[] = $currentPage;
        }

        if (empty($pages)) {
            $pages[] = array(array('text' => 'Laporan tidak berisi data.', 'title' => false));
        }

        $objects = array();
        $pageReferences = array();
        $fontObjectNumber = 3 + (count($pages) * 2);

        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[] = '__PAGES__';

        foreach ($pages as $pageIndex => $pageLines) {
            $pageObjectNumber = count($objects) + 1;
            $contentObjectNumber = $pageObjectNumber + 1;
            $pageReferences[] = $pageObjectNumber . ' 0 R';
            $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 ' . $fontObjectNumber . ' 0 R >> >> /Contents ' . $contentObjectNumber . ' 0 R >>';
            $objects[] = $this->ownerPdfPageStream($pageLines, $pageIndex + 1, count($pages));
        }

        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[1] = '<< /Type /Pages /Kids [' . implode(' ', $pageReferences) . '] /Count ' . count($pages) . ' >>';

        $pdf = "%PDF-1.4\n";
        $offsets = array(0);

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($index = 1; $index < count($offsets); $index++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$index]);
        }

        $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    protected function ownerPdfPageStream(array $pageLines, $pageNumber, $totalPages)
    {
        $commands = array();
        $y = 552;

        foreach ($pageLines as $line) {
            $fontSize = !empty($line['title']) ? 16 : 9;
            $commands[] = 'BT /F1 ' . $fontSize . ' Tf 40 ' . $y . ' Td (' . $this->ownerPdfEscape($this->ownerPdfText($line['text'])) . ') Tj ET';
            $y -= !empty($line['title']) ? 24 : 17;
        }

        $commands[] = 'BT /F1 8 Tf 760 24 Td (' . $this->ownerPdfEscape('Hal. ' . $pageNumber . '/' . $totalPages) . ') Tj ET';
        $stream = implode("\n", $commands);

        return "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
    }

    protected function ownerPdfWrapLine($line, $maxLength)
    {
        $line = trim((string) $line);

        if ($line === '') {
            return array('');
        }

        $wrapped = wordwrap($line, $maxLength, "\n", true);
        $lines = explode("\n", $wrapped);
        $result = array();

        foreach ($lines as $wrappedLine) {
            $result[] = $wrappedLine;
        }

        return $result;
    }

    protected function ownerPdfText($text)
    {
        $text = (string) $text;
        $converted = function_exists('iconv') ? @iconv('UTF-8', 'Windows-1252//TRANSLIT', $text) : false;

        if ($converted !== false) {
            $text = $converted;
        }

        $text = preg_replace('/[^\x20-\x7E]/', '', $text);

        return strlen($text) > 132 ? substr($text, 0, 129) . '...' : $text;
    }

    protected function ownerPdfEscape($text)
    {
        return str_replace(array('\\', '(', ')'), array('\\\\', '\\(', '\\)'), $text);
    }

    protected function sanitizeOwnerReportFilename($filename)
    {
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '-', (string) $filename);

        return trim($filename, '-') !== '' ? $filename : 'laporan-arena-sport';
    }

    protected function clearOwnerReportOutputBuffer()
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    protected function ownerTransactionFiltersFromRequest()
    {
        $start = $this->parseOwnerReportDate(isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '');
        $end = $this->parseOwnerReportDate(isset($_GET['tanggal_selesai']) ? $_GET['tanggal_selesai'] : '');

        if (!$start) {
            $start = new \DateTimeImmutable('first day of this month');
        }

        if (!$end) {
            $end = new \DateTimeImmutable('last day of this month');
        }

        if ($start > $end) {
            $swap = $start;
            $start = $end;
            $end = $swap;
        }

        return array(
            'start' => $start,
            'end' => $end,
            'type' => $this->sanitizeOwnerTransactionOption(isset($_GET['tipe']) ? $_GET['tipe'] : 'semua', $this->ownerTransactionTypeOptions(), 'semua'),
            'method' => $this->sanitizeOwnerTransactionOption(isset($_GET['metode']) ? $_GET['metode'] : 'semua', $this->ownerTransactionMethodOptions(), 'semua'),
            'status' => $this->sanitizeOwnerTransactionOption(isset($_GET['status']) ? $_GET['status'] : 'semua', $this->ownerTransactionStatusOptions(), 'semua'),
            'search' => $this->sanitizeOwnerTransactionSearch(isset($_GET['q']) ? $_GET['q'] : ''),
            'perPage' => $this->sanitizeOwnerTransactionPerPage(isset($_GET['per_page']) ? $_GET['per_page'] : 10),
        );
    }

    protected function ownerTransactionViewFilters(array $filters)
    {
        return array(
            'startDate' => $filters['start']->format('Y-m-d'),
            'endDate' => $filters['end']->format('Y-m-d'),
            'dateLabel' => $this->formatOwnerTransactionDateRange($filters['start'], $filters['end']),
            'type' => $filters['type'],
            'method' => $filters['method'],
            'status' => $filters['status'],
            'search' => $filters['search'],
            'perPage' => $filters['perPage'],
        );
    }

    protected function formatOwnerTransactionDateRange(\DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        $startLabel = $this->formatOwnerTransactionDate($start);
        $endLabel = $this->formatOwnerTransactionDate($end);

        return $start->format('Y-m-d') === $end->format('Y-m-d') ? $startLabel : $startLabel . ' - ' . $endLabel;
    }

    protected function formatOwnerTransactionDate(\DateTimeImmutable $date)
    {
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

        return sprintf('%02d %s %s', (int) $date->format('j'), $months[(int) $date->format('n')], $date->format('Y'));
    }

    protected function ownerTransactionTypeOptions()
    {
        return array(
            'semua' => 'Semua Tipe',
            'booking' => 'Booking Lapangan',
            'refund' => 'Refund',
            'pencairan' => 'Pencairan',
        );
    }

    protected function ownerTransactionMethodOptions()
    {
        return array(
            'semua' => 'Semua Metode',
            'qris' => 'QRIS',
            'dana' => 'DANA',
            'ovo' => 'OVO',
            'bank' => 'Transfer Bank',
        );
    }

    protected function ownerTransactionStatusOptions()
    {
        return array(
            'semua' => 'Semua Status',
            'selesai' => 'Selesai',
            'menunggu' => 'Menunggu',
            'dibatalkan' => 'Dibatalkan',
        );
    }

    protected function sanitizeOwnerTransactionOption($value, array $options, $default)
    {
        $normalized = strtolower(trim((string) $value));

        if (isset($options[$normalized])) {
            return $normalized;
        }

        foreach ($options as $key => $label) {
            if ($normalized === strtolower($label)) {
                return $key;
            }
        }

        return $default;
    }

    protected function sanitizeOwnerTransactionPerPage($value)
    {
        $perPage = (int) $value;
        $allowed = array(10, 25, 50);

        return in_array($perPage, $allowed, true) ? $perPage : 10;
    }

    protected function sanitizeOwnerTransactionSearch($value)
    {
        $search = preg_replace('/\s+/', ' ', trim((string) $value));

        return substr($search, 0, 90);
    }

    protected function filterOwnerTransactions(array $transactions, array $filters)
    {
        $filtered = array();
        $search = strtolower($filters['search']);

        foreach ($transactions as $transaction) {
            $date = $this->parseOwnerReportDate(isset($transaction['date']) ? $transaction['date'] : '');

            if (!$date || $date < $filters['start'] || $date > $filters['end']) {
                continue;
            }

            if ($filters['type'] !== 'semua' && (!isset($transaction['typeKey']) || $transaction['typeKey'] !== $filters['type'])) {
                continue;
            }

            if ($filters['method'] !== 'semua' && (!isset($transaction['methodKey']) || $transaction['methodKey'] !== $filters['method'])) {
                continue;
            }

            if ($filters['status'] !== 'semua' && (!isset($transaction['statusKey']) || $transaction['statusKey'] !== $filters['status'])) {
                continue;
            }

            if ($search !== '' && strpos($this->ownerTransactionSearchHaystack($transaction), $search) === false) {
                continue;
            }

            $filtered[] = $transaction;
        }

        return $filtered;
    }

    protected function ownerTransactionSearchHaystack(array $transaction)
    {
        $values = array(
            isset($transaction['orderId']) ? $transaction['orderId'] : '',
            isset($transaction['type']) ? $transaction['type'] : '',
            isset($transaction['date']) ? $transaction['date'] : '',
            isset($transaction['time']) ? $transaction['time'] : '',
            isset($transaction['customer']) ? $transaction['customer'] : '',
            isset($transaction['phone']) ? $transaction['phone'] : '',
            isset($transaction['field']) ? $transaction['field'] : '',
            isset($transaction['bookingDate']) ? $transaction['bookingDate'] : '',
            isset($transaction['bookingTime']) ? $transaction['bookingTime'] : '',
            isset($transaction['method']) ? $transaction['method'] : '',
            isset($transaction['total']) ? $transaction['total'] : '',
            isset($transaction['status']) ? $transaction['status'] : '',
        );

        return strtolower(implode(' ', $values));
    }

    protected function ownerTransactionStats($transactions = null)
    {
        if (!is_array($transactions)) {
            $transactions = $this->ownerTransactionRows();
        }

        $totalIncome = 0;
        $waiting = 0;
        $completed = 0;
        $cancelled = 0;

        foreach ($transactions as $transaction) {
            $statusKey = isset($transaction['statusKey']) ? $transaction['statusKey'] : '';
            $typeKey = isset($transaction['typeKey']) ? $transaction['typeKey'] : '';

            if ($statusKey === 'menunggu') {
                $waiting++;
            } elseif ($statusKey === 'dibatalkan') {
                $cancelled++;
            } elseif ($statusKey === 'selesai') {
                $completed++;

                if ($typeKey === 'booking') {
                    $totalIncome += $this->ownerRupiahToInt(isset($transaction['total']) ? $transaction['total'] : 0);
                }
            }
        }

        return array(
            array('label' => 'Total Transaksi', 'value' => (string) count($transactions), 'note' => 'Hasil filter', 'icon' => 'fa-money-check-dollar', 'accent' => 'lime'),
            array('label' => 'Total Pendapatan', 'value' => $this->formatOwnerRupiah($totalIncome), 'note' => 'Booking selesai', 'icon' => 'fa-sack-dollar', 'accent' => 'blue'),
            array('label' => 'Menunggu Pembayaran', 'value' => (string) $waiting, 'note' => 'Transaksi', 'icon' => 'fa-clock', 'accent' => 'gold'),
            array('label' => 'Transaksi Selesai', 'value' => (string) $completed, 'note' => 'Transaksi', 'icon' => 'fa-check', 'accent' => 'green'),
            array('label' => 'Transaksi Dibatalkan', 'value' => (string) $cancelled, 'note' => 'Transaksi', 'icon' => 'fa-xmark', 'accent' => 'red'),
        );
    }

    protected function ownerTransactionRows()
    {
        return $this->ownerTransactionsFromDatabase();

        $customers = array(
            array('name' => 'Budi Santoso', 'phone' => '0812-3456-7890'),
            array('name' => 'Rizky Maulana', 'phone' => '0821-1111-2222'),
            array('name' => 'Ahmad Fauzi', 'phone' => '0813-2222-3333'),
            array('name' => 'Deni Kurniawan', 'phone' => '0857-7777-8888'),
            array('name' => 'Joko Prasetyo', 'phone' => '0812-9999-0000'),
            array('name' => 'M. Iqbal', 'phone' => '0823-1234-5678'),
            array('name' => 'Andi Setiawan', 'phone' => '0811-2222-3333'),
            array('name' => 'Farhan Ramadhan', 'phone' => '0856-4444-5555'),
            array('name' => 'Rama Saputra', 'phone' => '0821-3333-4444'),
            array('name' => 'Kevin Putra', 'phone' => '0812-1234-0000'),
            array('name' => 'Sinta Lestari', 'phone' => '0813-4444-1111'),
            array('name' => 'Nadia Putri', 'phone' => '0852-6666-1122'),
        );
        $fields = array(
            array('name' => 'Arena Futsal A', 'amount' => 80000),
            array('name' => 'Arena Futsal B', 'amount' => 75000),
            array('name' => 'Arena Badminton 1', 'amount' => 60000),
            array('name' => 'Arena Basket Indoor', 'amount' => 120000),
        );
        $methods = array(
            array('key' => 'qris', 'method' => 'QRIS', 'methodClass' => 'qris', 'methodIcon' => 'fa-qrcode'),
            array('key' => 'dana', 'method' => 'DANA', 'methodClass' => 'dana', 'methodIcon' => 'fa-wallet'),
            array('key' => 'ovo', 'method' => 'OVO', 'methodClass' => 'ovo', 'methodIcon' => 'fa-circle-dot'),
            array('key' => 'bank', 'method' => 'Transfer Bank', 'methodClass' => 'bank', 'methodIcon' => 'fa-building-columns'),
        );
        $typeOptions = $this->ownerTransactionTypeOptions();
        $transactions = array();
        $sequence = 1;

        for ($day = 31; $day >= 1 && count($transactions) < 152; $day--) {
            for ($slot = 1; $slot <= 5 && count($transactions) < 152; $slot++) {
                $customer = $customers[($sequence + $day + $slot) % count($customers)];
                $field = $fields[($sequence + $slot) % count($fields)];
                $method = $methods[($sequence + $day) % count($methods)];
                $typeKey = 'booking';
                $amount = $field['amount'] + ((($sequence + $slot) % 3) * 10000);

                if ($sequence % 41 === 0) {
                    $typeKey = 'pencairan';
                    $method = $methods[3];
                    $amount = 350000 + (($sequence % 6) * 50000);
                } elseif ($sequence % 23 === 0) {
                    $typeKey = 'refund';
                    $amount = $field['amount'];
                }

                $status = $this->ownerTransactionStatusPayload($sequence);
                $transactionHour = 8 + (($sequence + $slot) % 14);
                $transactionMinute = ($day * 7 + $slot * 11) % 60;
                $bookingStart = 7 + (($sequence + $day + $slot) % 15);
                $bookingEnd = $bookingStart + 1;

                $transactions[] = array_merge(array(
                    'orderId' => sprintf('ORD-2405%02d-%03d', $day, $slot),
                    'typeKey' => $typeKey,
                    'type' => isset($typeOptions[$typeKey]) ? $typeOptions[$typeKey] : 'Booking Lapangan',
                    'date' => $day . ' Mei 2024',
                    'dateValue' => sprintf('2024-05-%02d', $day),
                    'time' => sprintf('%02d:%02d WIB', $transactionHour, $transactionMinute),
                    'customer' => $customer['name'],
                    'phone' => $customer['phone'],
                    'field' => $field['name'],
                    'bookingDate' => $day . ' Mei 2024',
                    'bookingTime' => sprintf('%02d:00 - %02d:00', $bookingStart, $bookingEnd),
                    'methodKey' => $method['key'],
                    'method' => $method['method'],
                    'methodClass' => $method['methodClass'],
                    'methodIcon' => $method['methodIcon'],
                    'total' => $this->formatOwnerRupiah($amount),
                ), $status);

                $sequence++;
            }
        }

        return $transactions;
    }

    protected function ownerTransactionStatusPayload($sequence)
    {
        $cancelledSequences = array(17, 68, 119);
        $waitingSequences = array(9, 28, 47, 66, 85, 104, 123, 142);

        if (in_array($sequence, $cancelledSequences, true)) {
            return array('statusKey' => 'dibatalkan', 'status' => 'Dibatalkan', 'statusClass' => 'danger');
        }

        if (in_array($sequence, $waitingSequences, true)) {
            return array('statusKey' => 'menunggu', 'status' => 'Menunggu', 'statusClass' => 'warning');
        }

        return array('statusKey' => 'selesai', 'status' => 'Selesai', 'statusClass' => 'success');
    }

    protected function sendOwnerTransactionCsv(array $transactions, array $filters, $filename)
    {
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, array('ARENA SPORT'));
        fputcsv($handle, array('Laporan Transaksi'));
        fputcsv($handle, array('Periode', $this->formatOwnerTransactionDateRange($filters['start'], $filters['end']), 'Dibuat', date('d/m/Y H:i')));
        fputcsv($handle, array(''));
        fputcsv($handle, array('Order ID', 'Tipe Transaksi', 'Tanggal', 'Waktu', 'Pelanggan', 'No HP', 'Lapangan', 'Waktu Booking', 'Metode Pembayaran', 'Total', 'Status'));

        if (empty($transactions)) {
            fputcsv($handle, array('Tidak ada data transaksi sesuai filter.'));
        }

        foreach ($transactions as $transaction) {
            fputcsv($handle, array(
                $transaction['orderId'],
                $transaction['type'],
                $transaction['date'],
                $transaction['time'],
                $transaction['customer'],
                $transaction['phone'],
                $transaction['field'],
                $transaction['bookingDate'] . ' ' . $transaction['bookingTime'],
                $transaction['method'],
                $transaction['total'],
                $transaction['status'],
            ));
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        $this->clearOwnerReportOutputBuffer();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $this->sanitizeOwnerReportFilename($filename) . '"');
        header('Content-Length: ' . strlen($content));
        echo $content;
        exit;
    }

    protected function ownerReviewStats()
    {
        return $this->ownerReviewStatsFromDatabase();

        return array(
            array('label' => 'Rating Rata-rata', 'value' => '4.8 / 5', 'note' => '(156 ulasan)', 'icon' => 'fa-star', 'accent' => 'lime', 'rating' => 4.8),
            array('label' => 'Total Ulasan', 'value' => '156', 'trend' => '12.3%', 'note' => 'dari bulan lalu', 'icon' => 'fa-comment-dots', 'accent' => 'blue'),
            array('label' => 'Ulasan Positif', 'value' => '142', 'trend' => '91.0%', 'note' => 'dari total ulasan', 'icon' => 'fa-thumbs-up', 'accent' => 'purple'),
            array('label' => 'Ulasan Negatif', 'value' => '14', 'trend' => '9.0%', 'note' => 'dari total ulasan', 'icon' => 'fa-thumbs-down', 'accent' => 'orange'),
        );
    }

    protected function ownerRatingDistribution()
    {
        return $this->ownerRatingDistributionFromDatabase();

        return array(
            array('stars' => 5, 'count' => 98, 'percent' => 62.8),
            array('stars' => 4, 'count' => 36, 'percent' => 23.1),
            array('stars' => 3, 'count' => 14, 'percent' => 9.0),
            array('stars' => 2, 'count' => 5, 'percent' => 3.2),
            array('stars' => 1, 'count' => 3, 'percent' => 1.9),
        );
    }

    protected function ownerFieldRatings()
    {
        return $this->ownerFieldRatingsFromDatabase();

        return array(
            array('name' => 'Arena Futsal A', 'rating' => '4.8', 'reviews' => '85', 'percent' => 94, 'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=360&auto=format&fit=crop'),
            array('name' => 'Arena Badminton 1', 'rating' => '4.7', 'reviews' => '45', 'percent' => 88, 'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=360&auto=format&fit=crop'),
            array('name' => 'Arena Futsal B', 'rating' => '4.6', 'reviews' => '26', 'percent' => 86, 'image' => 'https://images.unsplash.com/photo-1551958219-acbc608c6377?q=80&w=360&auto=format&fit=crop'),
        );
    }

    protected function ownerReviewRows()
    {
        return $this->ownerReviewsFromDatabase();

        return array(
            array('name' => 'Ahmad Rizki', 'username' => '@ahmadrzki', 'field' => 'Arena Futsal A', 'rating' => 5.0, 'review' => 'Lapangan bersih dan nyaman, pencahayaan bagus.', 'date' => '16 Juni 2025', 'time' => '19:45', 'avatar' => 'https://ui-avatars.com/api/?name=Ahmad+Rizki&background=245b84&color=ffffff'),
            array('name' => 'Dewi Sartika', 'username' => '@dewii.srt', 'field' => 'Arena Badminton 1', 'rating' => 4.5, 'review' => 'Net dan lantai bagus, hanya saja AC kurang dingin.', 'date' => '16 Juni 2025', 'time' => '17:20', 'avatar' => 'https://ui-avatars.com/api/?name=Dewi+Sartika&background=245b84&color=ffffff'),
            array('name' => 'Fajar Maulana', 'username' => '@fajarmaulana', 'field' => 'Arena Futsal B', 'rating' => 5.0, 'review' => 'Mantap! lapangan luas dan parkir nyaman.', 'date' => '15 Juni 2025', 'time' => '21:10', 'avatar' => 'https://ui-avatars.com/api/?name=Fajar+Maulana&background=245b84&color=ffffff'),
            array('name' => 'Rizal Aditya', 'username' => '@rizaladtya', 'field' => 'Arena Futsal A', 'rating' => 3.5, 'review' => 'Secara keseluruhan bagus, mungkin kamar mandi perlu ditingkatkan.', 'date' => '15 Juni 2025', 'time' => '16:05', 'avatar' => 'https://ui-avatars.com/api/?name=Rizal+Aditya&background=245b84&color=ffffff'),
            array('name' => 'Nurfadilah', 'username' => '@nurfadilah_', 'field' => 'Arena Badminton 1', 'rating' => 5.0, 'review' => 'Pelayanan ramah, lapangan top!', 'date' => '15 Juni 2025', 'time' => '15:30', 'avatar' => 'https://ui-avatars.com/api/?name=Nurfadilah&background=d7d3cc&color=394150'),
        );
    }

    protected function ownerProfile(array $owner)
    {
        $account = $this->findOwnerAccount(isset($owner['id']) ? $owner['id'] : '');
        $extras = $this->readOwnerProfileExtras(isset($owner['id']) ? $owner['id'] : '');
        $displayName = isset($owner['name']) && trim((string) $owner['name']) !== '' ? $owner['name'] : 'Rahmat';
        $email = isset($owner['email']) && trim((string) $owner['email']) !== '' ? $owner['email'] : 'rahmat@email.com';
        $phone = isset($owner['phone']) && trim((string) $owner['phone']) !== '' ? $owner['phone'] : '0812-3456-7890';

        if ($account) {
            $displayName = isset($account['name']) && trim((string) $account['name']) !== '' ? $account['name'] : $displayName;
            $email = isset($account['email']) && trim((string) $account['email']) !== '' ? $account['email'] : $email;
            $phone = isset($account['phone']) && trim((string) $account['phone']) !== '' ? $account['phone'] : $phone;
        }

        if (!empty($extras['name'])) {
            $displayName = $extras['name'];
        }

        if (!empty($extras['email'])) {
            $email = $extras['email'];
        }

        if (!empty($extras['phone'])) {
            $phone = $extras['phone'];
        }

        $avatar = $account && !empty($account['avatar']) ? $account['avatar'] : (isset($extras['avatar']) ? $extras['avatar'] : '');
        $location = $account && !empty($account['location']) ? $account['location'] : (isset($extras['location']) ? $extras['location'] : 'Parepare, Sulawesi Selatan');
        $joined = $account && !empty($account['created_at']) ? $this->ownerFormatDatabaseDate(substr($account['created_at'], 0, 10)) : '-';
        $fieldCount = count($this->ownerManagedFieldsFromDatabase());

        return array(
            'name' => $displayName,
            'email' => $email,
            'phone' => $phone,
            'location' => trim((string) $location) !== '' ? $location : 'Parepare, Sulawesi Selatan',
            'joined' => $joined,
            'totalFields' => $fieldCount . ' Lapangan',
            'lastLogin' => '-',
            'avatar' => $this->profileAvatarUrl($avatar),
        );
    }

    protected function saveOwnerProfile(array $owner)
    {
        $ownerId = isset($owner['id']) ? trim((string) $owner['id']) : '';
        $name = $this->cleanProfileValue(isset($_POST['name']) ? $_POST['name'] : '', 120);
        $email = $this->cleanProfileValue(isset($_POST['email']) ? $_POST['email'] : '', 160);
        $phone = $this->cleanProfileValue(isset($_POST['phone']) ? $_POST['phone'] : '', 30);
        $location = $this->cleanProfileValue(isset($_POST['location']) ? $_POST['location'] : '', 180);
        if ($name === '' || $email === '') {
            $this->setOwnerProfileFlash('error', 'Nama lengkap dan email wajib diisi.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setOwnerProfileFlash('error', 'Format email belum valid.');
            return;
        }

        if ($this->ownerEmailConflicts($ownerId, $email)) {
            $this->setOwnerProfileFlash('error', 'Email sudah digunakan akun lain.');
            return;
        }

        $extras = $this->readOwnerProfileExtras($ownerId);
        $avatar = isset($extras['avatar']) ? $extras['avatar'] : '';
        $uploadResult = $this->storeOwnerProfilePhoto($ownerId);

        if (!$uploadResult['ok']) {
            $this->setOwnerProfileFlash('error', $uploadResult['message']);
            return;
        }

        if ($uploadResult['path'] !== '') {
            $avatar = $uploadResult['path'];
        }

        $extras = array_merge($extras, array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'location' => $location,
            'avatar' => $avatar,
            'updated_at' => date('Y-m-d H:i:s'),
        ));

        $profileSaved = $this->writeOwnerProfileExtras($ownerId, $extras);
        $databaseSaved = $this->updateOwnerAccount($ownerId, $name, $email, $phone, $location, $avatar);

        $_SESSION['nama_user'] = $name;
        $_SESSION['nama'] = $name;
        $_SESSION['email_user'] = $email;
        $_SESSION['telepon_user'] = $phone;

        if ($profileSaved && $databaseSaved) {
            $this->setOwnerProfileFlash('success', 'Profil berhasil disimpan. Data akun juga tersimpan ke database.');
            return;
        }

        if ($profileSaved) {
            $this->setOwnerProfileFlash('success', 'Profil berhasil disimpan. Data tambahan tersimpan lokal; database belum bisa diperbarui.');
            return;
        }

        $this->setOwnerProfileFlash('error', 'Profil belum bisa disimpan. Coba lagi beberapa saat lagi.');
    }

    protected function changeOwnerPassword(array $owner)
    {
        $ownerId = isset($owner['id']) ? trim((string) $owner['id']) : '';
        $currentPassword = isset($_POST['current_password']) ? (string) $_POST['current_password'] : '';
        $newPassword = isset($_POST['new_password']) ? (string) $_POST['new_password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? (string) $_POST['confirm_password'] : '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $this->setOwnerProfileFlash('error', 'Semua kolom password wajib diisi.');
            return;
        }

        if (strlen($newPassword) < 6) {
            $this->setOwnerProfileFlash('error', 'Password baru minimal 6 karakter.');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->setOwnerProfileFlash('error', 'Konfirmasi password baru belum sama.');
            return;
        }

        $account = $this->findOwnerAccount($ownerId, true);

        if (!$account || empty($account['password'])) {
            $this->setOwnerProfileFlash('error', 'Data akun belum bisa dibaca dari database.');
            return;
        }

        $storedPassword = (string) $account['password'];

        if (!password_verify($currentPassword, $storedPassword) && !hash_equals($storedPassword, $currentPassword)) {
            $this->setOwnerProfileFlash('error', 'Password lama tidak sesuai.');
            return;
        }

        if ($this->updateOwnerPassword($ownerId, password_hash($newPassword, PASSWORD_DEFAULT))) {
            $this->setOwnerProfileFlash('success', 'Password berhasil diperbarui.');
            return;
        }

        $this->setOwnerProfileFlash('error', 'Password belum bisa diperbarui di database.');
    }

    protected function findOwnerAccount($ownerId, $withPassword = false)
    {
        $ownerId = trim((string) $ownerId);

        if ($ownerId === '') {
            return null;
        }

        try {
            $connection = Database::connection();
        } catch (\Throwable $exception) {
            return null;
        }

        $table = $this->ownerUserTable($connection);
        $columns = $this->tableColumns($connection, $table);
        $idColumn = in_array('ID_User', $columns, true) ? 'ID_User' : (in_array('id', $columns, true) ? 'id' : '');

        if ($idColumn === '') {
            return null;
        }

        $statement = mysqli_prepare($connection, 'SELECT * FROM `' . $table . '` WHERE `' . $idColumn . '` = ? LIMIT 1');

        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 's', $ownerId);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);

        if (!$row) {
            return null;
        }

        $account = array(
            'name' => isset($row['Nama']) ? $row['Nama'] : (isset($row['name']) ? $row['name'] : ''),
            'email' => isset($row['Email']) ? $row['Email'] : (isset($row['email']) ? $row['email'] : ''),
            'phone' => isset($row['Nomor_telepon']) ? $row['Nomor_telepon'] : (isset($row['phone']) ? $row['phone'] : ''),
            'location' => isset($row['Alamat']) ? $row['Alamat'] : '',
            'avatar' => isset($row['Avatar']) ? $row['Avatar'] : '',
            'created_at' => isset($row['created_at']) ? $row['created_at'] : '',
        );

        if ($withPassword) {
            $account['password'] = isset($row['Password']) ? $row['Password'] : (isset($row['password']) ? $row['password'] : '');
        }

        return $account;
    }

    protected function updateOwnerAccount($ownerId, $name, $email, $phone, $location = '', $avatar = '')
    {
        $ownerId = trim((string) $ownerId);

        if ($ownerId === '') {
            return false;
        }

        try {
            $connection = Database::connection();
        } catch (\Throwable $exception) {
            return false;
        }

        $table = $this->ownerUserTable($connection);
        $columns = $this->tableColumns($connection, $table);
        $idColumn = in_array('ID_User', $columns, true) ? 'ID_User' : (in_array('id', $columns, true) ? 'id' : '');
        $nameColumn = in_array('Nama', $columns, true) ? 'Nama' : (in_array('name', $columns, true) ? 'name' : '');
        $emailColumn = in_array('Email', $columns, true) ? 'Email' : (in_array('email', $columns, true) ? 'email' : '');
        $phoneColumn = in_array('Nomor_telepon', $columns, true) ? 'Nomor_telepon' : (in_array('phone', $columns, true) ? 'phone' : '');

        if ($idColumn === '' || $nameColumn === '' || $emailColumn === '') {
            return false;
        }

        if ($phoneColumn !== '') {
            $sql = 'UPDATE `' . $table . '` SET `' . $nameColumn . '` = ?, `' . $emailColumn . '` = ?, `' . $phoneColumn . '` = ?, `Alamat` = ?, `Avatar` = ? WHERE `' . $idColumn . '` = ?';
            $statement = mysqli_prepare($connection, $sql);

            if (!$statement) {
                return false;
            }

            mysqli_stmt_bind_param($statement, 'ssssss', $name, $email, $phone, $location, $avatar, $ownerId);
        } else {
            $sql = 'UPDATE `' . $table . '` SET `' . $nameColumn . '` = ?, `' . $emailColumn . '` = ? WHERE `' . $idColumn . '` = ?';
            $statement = mysqli_prepare($connection, $sql);

            if (!$statement) {
                return false;
            }

            mysqli_stmt_bind_param($statement, 'sss', $name, $email, $ownerId);
        }

        $saved = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return $saved;
    }

    protected function updateOwnerPassword($ownerId, $hashedPassword)
    {
        try {
            $connection = Database::connection();
        } catch (\Throwable $exception) {
            return false;
        }

        $table = $this->ownerUserTable($connection);
        $columns = $this->tableColumns($connection, $table);
        $idColumn = in_array('ID_User', $columns, true) ? 'ID_User' : (in_array('id', $columns, true) ? 'id' : '');
        $passwordColumn = in_array('Password', $columns, true) ? 'Password' : (in_array('password', $columns, true) ? 'password' : '');

        if ($idColumn === '' || $passwordColumn === '') {
            return false;
        }

        $statement = mysqli_prepare($connection, 'UPDATE `' . $table . '` SET `' . $passwordColumn . '` = ?, `Must_Reset_Password` = 0 WHERE `' . $idColumn . '` = ?');

        if (!$statement) {
            return false;
        }

        mysqli_stmt_bind_param($statement, 'ss', $hashedPassword, $ownerId);
        $saved = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return $saved;
    }

    protected function emailIsUsedByAnotherOwner($connection, $table, $idColumn, $emailColumn, $ownerId, $email)
    {
        $statement = mysqli_prepare($connection, 'SELECT `' . $idColumn . '` FROM `' . $table . '` WHERE `' . $emailColumn . '` = ? AND `' . $idColumn . '` <> ? LIMIT 1');

        if (!$statement) {
            return false;
        }

        mysqli_stmt_bind_param($statement, 'ss', $email, $ownerId);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);
        $exists = $result && mysqli_fetch_assoc($result);
        mysqli_stmt_close($statement);

        return (bool) $exists;
    }

    protected function ownerEmailConflicts($ownerId, $email)
    {
        try {
            $connection = Database::connection();
        } catch (\Throwable $exception) {
            return false;
        }

        $table = $this->ownerUserTable($connection);
        $columns = $this->tableColumns($connection, $table);
        $idColumn = in_array('ID_User', $columns, true) ? 'ID_User' : (in_array('id', $columns, true) ? 'id' : '');
        $emailColumn = in_array('Email', $columns, true) ? 'Email' : (in_array('email', $columns, true) ? 'email' : '');

        if ($idColumn === '' || $emailColumn === '') {
            return false;
        }

        return $this->emailIsUsedByAnotherOwner($connection, $table, $idColumn, $emailColumn, $ownerId, $email);
    }

    protected function ownerUserTable($connection)
    {
        $result = mysqli_query($connection, "SHOW TABLES LIKE 'users'");

        return $result && mysqli_num_rows($result) > 0 ? 'users' : 'user';
    }

    protected function tableColumns($connection, $table)
    {
        $columns = array();
        $result = mysqli_query($connection, 'SHOW COLUMNS FROM `' . $table . '`');

        if (!$result) {
            return $columns;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $columns[] = $row['Field'];
        }

        return $columns;
    }

    protected function storeOwnerProfilePhoto($ownerId)
    {
        if (empty($_FILES['avatar']) || !isset($_FILES['avatar']['error']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
            return array('ok' => true, 'path' => '', 'message' => '');
        }

        if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            return array('ok' => false, 'path' => '', 'message' => 'Foto profil gagal diupload.');
        }

        if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            return array('ok' => false, 'path' => '', 'message' => 'Ukuran foto maksimal 2MB.');
        }

        $imageInfo = @getimagesize($_FILES['avatar']['tmp_name']);
        $allowedTypes = array(
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
        );

        if (!$imageInfo || !isset($allowedTypes[$imageInfo[2]])) {
            return array('ok' => false, 'path' => '', 'message' => 'Format foto harus PNG atau JPG.');
        }

        $directory = __DIR__ . '/../../storage/uploads/profiles';

        if (!is_dir($directory) && !mkdir($directory, 0775, true)) {
            return array('ok' => false, 'path' => '', 'message' => 'Folder upload profil belum bisa dibuat.');
        }

        $safeOwnerId = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $ownerId);
        $safeOwnerId = $safeOwnerId !== '' ? $safeOwnerId : 'owner';
        $filename = 'owner_' . $safeOwnerId . '_' . time() . '.' . $allowedTypes[$imageInfo[2]];
        $target = $directory . '/' . $filename;

        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
            return array('ok' => false, 'path' => '', 'message' => 'Foto profil belum bisa disimpan.');
        }

        return array('ok' => true, 'path' => 'storage/uploads/profiles/' . $filename, 'message' => '');
    }

    protected function readOwnerProfileExtras($ownerId)
    {
        $file = $this->ownerProfileStorageFile($ownerId);

        if (!is_file($file)) {
            return array();
        }

        $content = file_get_contents($file);
        $data = json_decode($content, true);

        return is_array($data) ? $data : array();
    }

    protected function writeOwnerProfileExtras($ownerId, array $data)
    {
        $directory = dirname($this->ownerProfileStorageFile($ownerId));

        if (!is_dir($directory) && !mkdir($directory, 0775, true)) {
            return false;
        }

        return file_put_contents($this->ownerProfileStorageFile($ownerId), json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }

    protected function ownerProfileStorageFile($ownerId)
    {
        $safeOwnerId = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $ownerId);
        $safeOwnerId = $safeOwnerId !== '' ? $safeOwnerId : 'owner';

        return __DIR__ . '/../../storage/profiles/' . $safeOwnerId . '.json';
    }

    protected function profileAvatarUrl($avatar)
    {
        $avatar = trim((string) $avatar);

        if ($avatar === '') {
            return 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=320&auto=format&fit=crop';
        }

        if (preg_match('#^https?://#', $avatar)) {
            return $avatar;
        }

        return app_url($avatar);
    }

    protected function cleanProfileValue($value, $maxLength)
    {
        $value = trim((string) $value);
        $value = preg_replace('/\s+/', ' ', $value);

        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $maxLength, 'UTF-8');
        }

        return substr($value, 0, $maxLength);
    }

    protected function setOwnerProfileFlash($type, $message)
    {
        $_SESSION['owner_profile_flash'] = array(
            'type' => $type,
            'message' => $message,
        );
    }

    protected function pullOwnerProfileFlash()
    {
        if (empty($_SESSION['owner_profile_flash']) || !is_array($_SESSION['owner_profile_flash'])) {
            return null;
        }

        $flash = $_SESSION['owner_profile_flash'];
        unset($_SESSION['owner_profile_flash']);

        return $flash;
    }

    protected function ownerSettingsGroups()
    {
        return array(
            array(
                'title' => 'Pengaturan Akun',
                'items' => array(
                    array('label' => 'Informasi Akun', 'description' => 'Kelola informasi pribadi, email, dan nomor telepon', 'icon' => 'fa-user', 'url' => app_url('pemilik/profil')),
                    array('label' => 'Keamanan Akun', 'description' => 'Ubah password dan aktifkan verifikasi 2 langkah', 'icon' => 'fa-lock', 'url' => '#'),
                    array('label' => 'Notifikasi', 'description' => 'Atur preferensi notifikasi dan email', 'icon' => 'fa-bell', 'url' => '#'),
                ),
            ),
            array(
                'title' => 'Pengaturan Bisnis',
                'items' => array(
                    array('label' => 'Informasi Lapangan', 'description' => 'Kelola informasi usaha dan detail lapangan', 'icon' => 'fa-landmark', 'url' => app_url('pemilik/lapangan')),
                    array('label' => 'Jam Operasional', 'description' => 'Atur jam buka tutup dan hari operasional', 'icon' => 'fa-clock', 'url' => '#'),
                    array('label' => 'Pembayaran', 'description' => 'Kelola rekening, metode pembayaran, dan pencairan dana', 'icon' => 'fa-rupiah-sign', 'url' => app_url('pemilik/pendapatan')),
                    array('label' => 'Pajak & Dokumen', 'description' => 'Kelola NPWP dan dokumen bisnis', 'icon' => 'fa-file-lines', 'url' => '#'),
                ),
            ),
            array(
                'title' => 'Pengaturan Aplikasi',
                'items' => array(
                    array('label' => 'Tampilan Aplikasi', 'description' => 'Atur tema, bahasa, dan tampilan aplikasi', 'icon' => 'fa-palette', 'url' => '#'),
                    array('label' => 'Privasi & Data', 'description' => 'Kelola data, izin, dan preferensi privasi', 'icon' => 'fa-shield-halved', 'url' => '#'),
                    array('label' => 'Tentang Aplikasi', 'description' => 'Informasi versi aplikasi dan kebijakan', 'icon' => 'fa-circle-info', 'url' => '#'),
                ),
            ),
        );
    }

    protected function ownerHelpItems()
    {
        return array(
            array('label' => 'FAQ', 'icon' => 'fa-circle-question', 'url' => '#'),
            array('label' => 'Panduan Penggunaan', 'icon' => 'fa-book', 'url' => '#'),
            array('label' => 'Hubungi Kami', 'icon' => 'fa-headset', 'url' => '#'),
        );
    }

    protected function profileManagedFields()
    {
        return $this->ownerManagedFieldsFromDatabase();

        return array(
            array('name' => 'Arena Futsal A', 'type' => 'Futsal', 'location' => 'Parepare', 'price' => 'Rp80.000', 'status' => 'Aktif', 'image' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=360&auto=format&fit=crop'),
            array('name' => 'Arena Badminton 1', 'type' => 'Badminton', 'location' => 'Parepare', 'price' => 'Rp60.000', 'status' => 'Aktif', 'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=360&auto=format&fit=crop'),
            array('name' => 'Arena Futsal B', 'type' => 'Futsal', 'location' => 'Parepare', 'price' => 'Rp75.000', 'status' => 'Aktif', 'image' => 'https://images.unsplash.com/photo-1551958219-acbc608c6377?q=80&w=360&auto=format&fit=crop'),
        );
    }
}
