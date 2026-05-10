<?php 
include 'config/connection.php';
include 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <h2>Booking Lapangan Jadi Lebih Mudah</h2>
        <a href="#lapangan" class="btn-cta">Cari Lapangan</a>
    </div>
</section>

<section id="lapangan" class="container content">
    <div class="grid-container">
        <?php
        $query = mysqli_query($conn, "SELECT * FROM lapangan");
        while($row = mysqli_fetch_assoc($query)) {
        ?>
            <div class="card">
                <div class="card-detail">
                    <h4><?php echo $row['Nama_lapangan']; ?></h4>
                    <p>Jenis: <?php echo $row['Jenis_olahraga']; ?></p>
                    <p>Lokasi: <?php echo $row['Lokasi']; ?></p>
                    <a href="public/booking.php?id=<?php echo $row['ID_Lapangan']; ?>" class="btn-book">Pilih Jadwal</a>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>