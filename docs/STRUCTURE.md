# Struktur Arena Sport

Proyek ini sudah ditata seperti framework MVC ringan agar lebih mudah dikembangkan di XAMPP.

## Folder utama

- `app/Core` berisi engine kecil: router, controller dasar, view renderer, dan database connector.
- `app/Controllers` berisi pengatur request halaman.
- `app/Models` berisi akses data database.
- `app/Views` berisi template tampilan.
- `bootstrap` berisi autoload dan inisialisasi aplikasi.
- `routes` berisi daftar route URL.
- `config` berisi konfigurasi aplikasi dan database.
- `database/migrations` berisi SQL struktur tabel.
- `database/seeders` berisi data contoh.
- `storage` disiapkan untuk log dan upload.
- `tests` disiapkan untuk pengujian.
- `public` masih menyimpan halaman lama seperti login, register, booking, dan logout.

## Alur request baru

1. `index.php` memuat `bootstrap/app.php`.
2. `routes/web.php` mendaftarkan route.
3. `App\Core\Router` mencocokkan URL.
4. Controller mengambil data dari model.
5. View ditampilkan melalui layout utama.

## Catatan

Struktur ini belum memaksa migrasi penuh ke Laravel atau CodeIgniter. Tujuannya membuat fondasi framework dulu tanpa merusak halaman lama.
