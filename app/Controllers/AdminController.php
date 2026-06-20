<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\ArenaData;

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

    protected function adminData()
    {
        return new ArenaData();
    }

    protected function adminRupiah($amount)
    {
        return 'Rp' . number_format(max(0, (int) $amount), 0, ',', '.');
    }

    protected function adminDate($date)
    {
        return $this->adminFormatDate($date);
    }

    protected function adminBookingRows()
    {
        return $this->adminData()->rows(
            "SELECT b.ID_Booking, b.Status AS booking_status, b.Total_harga, b.Waktu_transaksi,
                    u.Nama AS customer_name, u.Nomor_telepon,
                    j.Tanggal, j.Jam_Mulai, j.Jam_Selesai,
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
        foreach($this->adminBookingRows() as $row){$payload=$this->adminBookingPayload($row['booking_status'],isset($row['payment_status'])?$row['payment_status']:'');$bookings[]=array('code'=>$row['ID_Booking'],'field'=>$row['Nama_lapangan'],'user'=>$row['customer_name'],'date'=>$this->adminDate($row['Tanggal']),'time'=>substr($row['Jam_Mulai'],0,5).' - '.substr($row['Jam_Selesai'],0,5),'status'=>$payload['label'],'statusClass'=>$payload['class'],'total'=>$this->adminRupiah($row['Total_harga']));}
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
        foreach($rows as $row){$status=$row['Status'];$type=strtolower($row['Jenis_olahraga']);$icon=strpos($type,'badminton')!==false?'fa-table-tennis-paddle-ball':(strpos($type,'basket')!==false?'fa-basketball':'fa-futbol');$fields[]=array('name'=>$row['Nama_lapangan'],'type'=>$row['Jenis_olahraga'],'location'=>$row['Lokasi'],'price'=>$this->adminRupiah($row['Harga']).'/jam','bookings'=>(int)$row['bookings'],'rating'=>number_format((float)$row['rating'],1),'reviews'=>(int)$row['reviews'],'status'=>$status,'badge'=>strtolower($status)==='aktif'?'success':'warning','progress'=>min(100,(int)$row['bookings']*10),'icon'=>$icon,'accent'=>strtolower($status)==='aktif'?'lime':'gold');}
        return $fields;
    }

    protected function adminReviewsFromDatabase()
    {
        $rows=$this->adminData()->rows("SELECT r.Rating,r.Komentar,r.Balasan,r.created_at,u.Nama,l.Nama_lapangan FROM review r INNER JOIN users u ON u.ID_User=r.ID_User INNER JOIN lapangan l ON l.ID_Lapangan=r.ID_Lapangan ORDER BY r.created_at DESC");$result=array();
        foreach($rows as $row){$name=$row['Nama'];$parts=preg_split('/\s+/',trim($name));$initials='';foreach(array_slice($parts,0,2) as $part){$initials.=strtoupper(substr($part,0,1));}$responded=trim((string)$row['Balasan'])!=='';$result[]=array('initials'=>$initials,'user'=>$name,'field'=>$row['Nama_lapangan'],'rating'=>(float)$row['Rating'],'comment'=>$row['Komentar'],'date'=>$this->adminDate(substr($row['created_at'],0,10)),'status'=>$responded?'Ditanggapi':'Belum Ditanggapi','statusClass'=>$responded?'success':'warning','accent'=>'blue');}
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
        $result=array();foreach($this->adminBookingRows() as $row){if(empty($row['ID_Pembayaran'])){continue;}$payload=$this->adminBookingPayload($row['booking_status'],$row['payment_status']);$created=!empty($row['Waktu_pembayaran'])?$row['Waktu_pembayaran']:$row['payment_created_at'];$name=$row['customer_name'];$parts=preg_split('/\s+/',trim($name));$initials='';foreach(array_slice($parts,0,2) as $p){$initials.=strtoupper(substr($p,0,1));}$result[]=array('id'=>$row['ID_Pembayaran'],'booking'=>$row['ID_Booking'],'field'=>$row['Nama_lapangan'],'user'=>$name,'initials'=>$initials,'phone'=>$row['Nomor_telepon'],'method'=>$row['Metode'],'methodClass'=>$this->adminMethodClass($row['Metode']),'amount'=>$this->adminRupiah($row['Jumlah']),'status'=>$payload['label']==='Selesai'?'Berhasil':$payload['label'],'statusClass'=>$payload['class'],'date'=>$this->adminDate(substr($created,0,10)),'time'=>substr($created,11,5),'accent'=>'green');}return $result;
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

    protected function adminReportStatsFromDatabase()
    {
        $data=$this->adminData();$income=(int)$data->value("SELECT COALESCE(SUM(Jumlah),0) value FROM pembayaran WHERE LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid')");$bookings=(int)$data->value('SELECT COUNT(*) value FROM booking');$users=(int)$data->value("SELECT COUNT(*) value FROM users WHERE created_at >= DATE_FORMAT(CURDATE(),'%Y-%m-01')");$fields=(int)$data->value("SELECT COUNT(*) value FROM lapangan WHERE LOWER(Status)='aktif' AND deleted_at IS NULL");
        return array(array('label'=>'Total Pendapatan','value'=>$this->adminRupiah($income),'trend'=>'0%','note'=>'seluruh periode','icon'=>'fa-wallet','accent'=>'green'),array('label'=>'Total Booking','value'=>(string)$bookings,'trend'=>'0%','note'=>'seluruh periode','icon'=>'fa-calendar-check','accent'=>'purple'),array('label'=>'Total Pengguna Baru','value'=>(string)$users,'trend'=>'0%','note'=>'bulan ini','icon'=>'fa-user-plus','accent'=>'blue'),array('label'=>'Total Lapangan Aktif','value'=>(string)$fields,'trend'=>'0%','note'=>'saat ini','icon'=>'fa-table-cells-large','accent'=>'gold'));
    }

    protected function adminRevenueReportFromDatabase()
    {
        $rows=$this->adminData()->rows("SELECT DATE(COALESCE(Waktu_pembayaran,created_at)) report_date,SUM(Jumlah) amount FROM pembayaran WHERE LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid') AND COALESCE(Waktu_pembayaran,created_at)>=DATE_SUB(CURDATE(),INTERVAL 30 DAY) GROUP BY DATE(COALESCE(Waktu_pembayaran,created_at)) ORDER BY report_date");if(empty($rows)){return array(array('label'=>$this->adminDate(date('Y-m-d')),'amount'=>'Rp0','x'=>50,'y'=>92,'highlight'=>true));}$max=1;foreach($rows as $r){$max=max($max,(int)$r['amount']);}$count=count($rows);$result=array();$highlight=0;$best=-1;foreach($rows as $i=>$row){if((int)$row['amount']>$best){$best=(int)$row['amount'];$highlight=$i;}$result[]=array('label'=>$this->adminDate($row['report_date']),'amount'=>$this->adminRupiah($row['amount']),'x'=>$count>1?round(($i/($count-1))*100,2):50,'y'=>round(92-(((int)$row['amount']/$max)*82),2));}$result[$highlight]['highlight']=true;return $result;
    }

    protected function adminPaymentReportFromDatabase()
    {
        $rows=$this->adminData()->rows("SELECT Metode method,SUM(Jumlah) amount FROM pembayaran WHERE LOWER(Status) IN ('berhasil','dibayar','lunas','success','paid') GROUP BY Metode ORDER BY amount DESC");$total=0;foreach($rows as $r){$total+=(int)$r['amount'];}$colors=array('blue','purple','teal','orange','light');$result=array();foreach($rows as $i=>$r){$result[]=array('method'=>$r['method'],'amount'=>$this->adminRupiah($r['amount']),'percent'=>$total?round(((int)$r['amount']/$total)*100):0,'color'=>$colors[$i%count($colors)]);}return $result;
    }

    protected function adminFieldBookingReportFromDatabase()
    {
        $rows=$this->adminData()->rows("SELECT l.Nama_lapangan field,COUNT(b.ID_Booking) value FROM lapangan l LEFT JOIN jadwal j ON j.ID_Lapangan=l.ID_Lapangan LEFT JOIN booking b ON b.ID_Jadwal=j.ID_Jadwal WHERE l.deleted_at IS NULL GROUP BY l.ID_Lapangan ORDER BY value DESC LIMIT 6");$max=1;foreach($rows as $r){$max=max($max,(int)$r['value']);}$result=array();foreach($rows as $r){$result[]=array('field'=>$r['field'],'short'=>htmlspecialchars($r['field'],ENT_QUOTES,'UTF-8'),'value'=>(int)$r['value'],'height'=>round(((int)$r['value']/$max)*100));}return $result;
    }

    protected function adminPaymentMethodsFromDatabase()
    {
        $rows=$this->adminData()->rows('SELECT * FROM metode_pembayaran ORDER BY Nama');$result=array();foreach($rows as $row){$result[]=array('name'=>$row['Nama'],'description'=>'Metode '.$row['Tipe'].'; biaya admin '.$this->adminRupiah($row['Biaya_admin']),'mark'=>strtoupper(substr($row['Nama'],0,2)),'accent'=>$this->adminMethodClass($row['Nama']),'enabled'=>(bool)$row['Aktif']);}return $result;
    }

    protected function adminBankAccountsFromDatabase()
    {
        $rows=$this->adminData()->rows("SELECT rp.*,p.nama_usaha FROM rekening_pemilik rp INNER JOIN pemilik_lapangan p ON p.ID_Pemilik=rp.ID_Pemilik ORDER BY rp.Utama DESC,rp.created_at DESC");$result=array();foreach($rows as $row){$active=strtolower($row['Status'])==='aktif';$result[]=array('bank'=>$row['Nama_bank'],'account'=>$row['Nomor_rekening'],'owner'=>$row['Nama_pemilik'],'status'=>$row['Status'],'statusClass'=>$active?'success':'inactive','accent'=>$this->adminMethodClass($row['Nama_bank']));}return $result;
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

        return $this->view('Admin/booking', array(
            'title' => 'Manajemen Booking | Arena Sport',
            'activeMenu' => 'booking',
            'userName' => $userName,
            'recentBookings' => $this->recentBookings(),
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
        $sql = 'SELECT ' . implode(', ', $select) . ' FROM `' . $table . '` u' . $ownerJoin . ' ORDER BY u.`' . $orderColumn . '` ASC';
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

        return $this->view('Admin/laporan', array(
            'title' => 'Laporan | Arena Sport',
            'activeMenu' => 'laporan',
            'userName' => $userName,
            'userRole' => $role,
            'searchPlaceholder' => 'Cari laporan...',
            'reportStats' => $this->reportStats(),
            'revenueReportPoints' => $this->revenueReportPoints(),
            'paymentReport' => $this->paymentReport(),
            'fieldBookingReport' => $this->fieldBookingReport(),
            'reportDownloads' => $this->reportDownloads(),
        ), 'layouts/admin');
    }

    protected function reportStats()
    {
        return $this->adminReportStatsFromDatabase();

        return array(
            array('label' => 'Total Pendapatan', 'value' => 'Rp32.450.000', 'trend' => '15%', 'note' => 'dari periode sebelumnya', 'icon' => 'fa-wallet', 'accent' => 'green'),
            array('label' => 'Total Booking', 'value' => '362', 'trend' => '18%', 'note' => 'dari periode sebelumnya', 'icon' => 'fa-calendar-check', 'accent' => 'purple'),
            array('label' => 'Total Pengguna Baru', 'value' => '24', 'trend' => '20%', 'note' => 'dari periode sebelumnya', 'icon' => 'fa-user-plus', 'accent' => 'blue'),
            array('label' => 'Total Lapangan Aktif', 'value' => '24', 'trend' => '0%', 'note' => 'dari periode sebelumnya', 'icon' => 'fa-table-cells-large', 'accent' => 'gold'),
        );
    }

    protected function revenueReportPoints()
    {
        return $this->adminRevenueReportFromDatabase();

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

    protected function paymentReport()
    {
        return $this->adminPaymentReportFromDatabase();

        return array(
            array('method' => 'VA BCA', 'amount' => 'Rp14.602.500', 'percent' => 45, 'color' => 'blue'),
            array('method' => 'OVO', 'amount' => 'Rp8.112.500', 'percent' => 25, 'color' => 'purple'),
            array('method' => 'GoPay', 'amount' => 'Rp4.867.500', 'percent' => 15, 'color' => 'teal'),
            array('method' => 'DANA', 'amount' => 'Rp3.245.000', 'percent' => 10, 'color' => 'orange'),
            array('method' => 'Lainnya', 'amount' => 'Rp1.622.500', 'percent' => 5, 'color' => 'light'),
        );
    }

    protected function fieldBookingReport()
    {
        return $this->adminFieldBookingReportFromDatabase();

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
            array('title' => 'Laporan Pendapatan', 'description' => 'Ringkasan pendapatan dan transaksi', 'icon' => 'fa-file-invoice-dollar'),
            array('title' => 'Laporan Booking', 'description' => 'Ringkasan data booking', 'icon' => 'fa-table-cells-large'),
            array('title' => 'Laporan Pengguna', 'description' => 'Ringkasan data pengguna', 'icon' => 'fa-address-card'),
            array('title' => 'Laporan Lapangan', 'description' => 'Ringkasan data lapangan', 'icon' => 'fa-file-lines'),
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
        return array(
            array('label' => 'Maintenance Mode', 'description' => 'Aktifkan mode maintenance (aplikasi tidak dapat diakses user)', 'enabled' => false),
            array('label' => 'Registrasi User', 'description' => 'Izinkan user baru untuk mendaftar', 'enabled' => true),
            array('label' => 'Auto Approval Booking', 'description' => 'Booking akan otomatis disetujui oleh sistem', 'enabled' => true),
            array('label' => 'Email Notifikasi', 'description' => 'Kirim email notifikasi ke admin', 'enabled' => true),
            array('label' => 'Tema Gelap', 'description' => 'Aktifkan tampilan tema gelap', 'enabled' => true),
        );
    }

    protected function notificationChannels()
    {
        return array(
            array('label' => 'Notifikasi In-App', 'description' => 'Terima notifikasi melalui aplikasi Arena Sport.', 'icon' => 'fa-bell', 'accent' => 'green', 'enabled' => true),
            array('label' => 'Email', 'description' => 'Terima notifikasi melalui email yang terdaftar.', 'icon' => 'fa-envelope', 'accent' => 'green', 'enabled' => true),
        );
    }

    protected function notificationTypes()
    {
        return array(
            array('label' => 'Booking Baru', 'description' => 'Notifikasi ketika ada booking baru pada lapangan Anda.', 'enabled' => true),
            array('label' => 'Booking Dikonfirmasi', 'description' => 'Notifikasi ketika booking dikonfirmasi oleh admin.', 'enabled' => true),
            array('label' => 'Booking Dibatalkan', 'description' => 'Notifikasi ketika booking dibatalkan oleh pengguna.', 'enabled' => true),
            array('label' => 'Pengingat Booking', 'description' => 'Notifikasi pengingat sebelum waktu booking dimulai.', 'enabled' => true),
            array('label' => 'Ulasan & Rating Baru', 'description' => 'Notifikasi ketika ada ulasan atau rating baru diberikan.', 'enabled' => true),
            array('label' => 'Promo & Informasi', 'description' => 'Notifikasi tentang promo, fitur baru, dan informasi penting.', 'enabled' => false),
            array('label' => 'Sistem & Keamanan', 'description' => 'Notifikasi terkait keamanan akun dan aktivitas sistem.', 'enabled' => true),
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
        return array(
            array('label' => 'Batas Waktu Pembayaran', 'description' => 'Batas waktu maksimal pembayaran sebelum booking dibatalkan otomatis.', 'type' => 'select', 'value' => '60 Menit', 'options' => array('30 Menit', '60 Menit', '90 Menit')),
            array('label' => 'Biaya Admin (Persentase)', 'description' => 'Persentase biaya admin yang dikenakan pada setiap transaksi.', 'type' => 'select', 'value' => '2,5 %', 'options' => array('1 %', '2,5 %', '5 %')),
            array('label' => 'Minimal Pembayaran', 'description' => 'Nominal minimal pembayaran yang diperbolehkan.', 'type' => 'text', 'value' => 'Rp 10.000'),
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
        return array(
            array('label' => 'Two-Factor Authentication (2FA)', 'description' => 'Tambahkan lapisan keamanan ekstra saat login.', 'icon' => 'fa-shield-halved', 'accent' => 'green', 'status' => 'Aktif', 'type' => 'toggle', 'enabled' => true),
            array('label' => 'Sesi Aktif', 'description' => 'Kelola perangkat yang saat ini sedang login ke akun Anda.', 'icon' => 'fa-lock', 'accent' => 'blue', 'type' => 'button', 'button' => 'Kelola'),
            array('label' => 'Ubah Password', 'description' => 'Ubah password akun administrator secara berkala.', 'icon' => 'fa-key', 'accent' => 'purple', 'type' => 'button', 'button' => 'Ubah Password'),
            array('label' => 'Verifikasi Email', 'description' => 'Email Anda telah terverifikasi.', 'icon' => 'fa-envelope-circle-check', 'accent' => 'gold', 'status' => 'Aktif', 'type' => 'verified', 'email' => 'admin@arenasport.com'),
            array('label' => 'Notifikasi Keamanan', 'description' => 'Dapatkan notifikasi untuk aktivitas keamanan penting.', 'icon' => 'fa-shield', 'accent' => 'teal', 'type' => 'toggle', 'enabled' => true),
        );
    }

    protected function securityActivities()
    {
        return array(
            array('title' => 'Login Berhasil', 'description' => 'Windows • Chrome • 114.10.20.30', 'date' => '16 Juni 2024', 'time' => '17:54 WIB', 'icon' => 'fa-right-to-bracket', 'accent' => 'green'),
            array('title' => 'Password Diubah', 'description' => 'Windows • Chrome • 114.10.20.30', 'date' => '14 Juni 2024', 'time' => '10:21 WIB', 'icon' => 'fa-lock', 'accent' => 'blue'),
            array('title' => '2FA Diaktifkan', 'description' => 'Windows • Chrome • 114.10.20.30', 'date' => '10 Juni 2024', 'time' => '09:15 WIB', 'icon' => 'fa-key', 'accent' => 'gold'),
            array('title' => 'Logout', 'description' => 'Windows • Chrome • 114.10.20.30', 'date' => '10 Juni 2024', 'time' => '09:10 WIB', 'icon' => 'fa-arrow-right-from-bracket', 'accent' => 'purple'),
            array('title' => 'Login Gagal', 'description' => 'IP: 203.0.113.10', 'date' => '10 Juni 2024', 'time' => '08:45 WIB', 'icon' => 'fa-triangle-exclamation', 'accent' => 'red'),
        );
    }

    protected function activeSessions()
    {
        return array(
            array('device' => 'Windows 11', 'type' => '', 'browser' => 'Chrome 126.0', 'location' => 'Parepare, Indonesia', 'ip' => '114.10.20.30', 'lastActive' => '16 Juni 2024<br>17:54 WIB', 'status' => 'Aktif', 'current' => true, 'icon' => 'fa-desktop', 'accent' => 'green'),
            array('device' => 'iPhone 13', 'type' => 'Mobile', 'browser' => 'Safari 17.5', 'location' => 'Makassar, Indonesia', 'ip' => '36.80.15.42', 'lastActive' => '15 Juni 2024<br>21:30 WIB', 'status' => 'Aktif', 'current' => false, 'icon' => 'fa-mobile-screen-button', 'accent' => 'blue'),
            array('device' => 'MacBook Pro', 'type' => 'Laptop', 'browser' => 'Chrome 125.0', 'location' => 'Jakarta, Indonesia', 'ip' => '103.21.45.67', 'lastActive' => '14 Juni 2024<br>11:20 WIB', 'status' => 'Aktif', 'current' => false, 'icon' => 'fa-laptop', 'accent' => 'gold'),
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
        return array(
            array('label' => 'Notifikasi Login Baru', 'description' => 'Kirim notifikasi ketika ada login di perangkat baru', 'enabled' => true),
            array('label' => 'Autentikasi Dua Faktor (2FA)', 'description' => 'Tambahkan lapisan keamanan ekstra untuk akun', 'enabled' => false),
            array('label' => 'Logout Otomatis', 'description' => 'Logout otomatis jika tidak aktif selama 30 menit', 'enabled' => true),
            array('label' => 'Simpan Riwayat Login', 'description' => 'Simpan riwayat perangkat yang pernah login', 'enabled' => true),
        );
    }

    protected function adminLoginActivity()
    {
        return array(
            array('label' => 'Login Terakhir', 'value' => '16 Juni 2024 - 17:54 WIB'),
            array('label' => 'Browser', 'value' => 'Google Chrome 137'),
            array('label' => 'Sistem Operasi', 'value' => 'Windows 11'),
            array('label' => 'IP Address', 'value' => '192.168.1.25'),
            array('label' => 'Lokasi', 'value' => 'Makassar, Sulawesi Selatan, Indonesia'),
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
            array('device' => 'Windows - Chrome', 'ip' => '192.168.1.25', 'location' => 'Makassar, Indonesia', 'time' => '16 Juni 2024<br>17:54 WIB', 'icon' => 'fa-desktop', 'current' => true),
            array('device' => 'Android - Chrome Mobile', 'ip' => '192.168.1.33', 'location' => 'Makassar, Indonesia', 'time' => '15 Juni 2024<br>21:30 WIB', 'icon' => 'fa-mobile-screen-button', 'current' => false),
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
