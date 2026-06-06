# Arena Sport

Arena Sport adalah sistem booking lapangan olahraga berbasis PHP dan MySQL.

## Menjalankan di XAMPP

1. Simpan folder ini di `htdocs`.
2. Buat database MySQL bernama `arena sport`.
3. Jalankan SQL di `database/migrations`.
4. Jika butuh data contoh, jalankan SQL di `database/seeders`.
5. Buka URL sesuai posisi folder di `htdocs`, misalnya `http://localhost/Project%20Web/arena_sport/`.

## Struktur framework

Entry point aplikasi ada di `index.php`, route ada di `routes/web.php`, controller ada di `app/Controllers`, model ada di `app/Models`, dan view ada di `app/Views`.

Halaman lama di `public` tetap dipertahankan agar fitur login, register, booking, dan logout tidak langsung rusak saat migrasi bertahap.
