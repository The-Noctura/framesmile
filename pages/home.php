<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/home.css">
    <title>Frame Smile | Home</title>
</head>

<body>

    <!--Navbar-->
    <?php require_once __DIR__ . '/../components/navbar.php'; ?>

    <!-- hero -->
    <section class="hero">
        <div class="hero-content">
            <h1>WELCOME TO</h1>
            <h2>Bingkai senyummu dengan sempurna</h2>
            <p>Koleksi template edit photobooth yang siap membuat setiap foto menjadi istimewa.</p>
            <a href="../pages/product.php" class="cta">Get Your Frame, Now</a>
        </div>

        <div class="hero-img">
            <img src="../public/assets/img/hero-img.png" alt="Sebuah foto berbentuk lingkaran hasil foto hasil photobooth dua wanita">
        </div>
    </section>

    <section class="faq">

        <h2 class="faq_title">FAQ?</h2>
        <p class="faq_subtitle">Pertanyaan yang sering di ajukan pelanggan kami</p>

        <details class="faq_item">
            <summary>Berapa lama proses editing photobooth?</summary>
            <p>Proses editing standar membutuhkan waktu <strong>1–3 hari kerja</strong> setelah file diterima. Untuk layanan <em>express</em>, hasil editing dapat selesai dalam <strong>24 jam</strong> dengan biaya tambahan. Waktu dapat bervariasi tergantung jumlah foto dan tingkat kerumitan editing.</p>
        </details>

        <details class="faq_item">
            <summary>Apa saja file yang perlu saya kirimkan?</summary>
            <p>Silakan kirimkan file foto dalam format <strong>JPG atau PNG</strong> dengan resolusi minimal <strong>1500 × 1500 piksel</strong> agar hasil cetak tetap tajam. Jika ada referensi desain atau warna tertentu yang diinginkan, sertakan juga contoh gambarnya. File dapat dikirim melalui WhatsApp, Google Drive, atau email.</p>
        </details>

        <details class="faq_item">
            <summary>Apa bedanya template dan custom?</summary>
            <p><strong>Template</strong> menggunakan desain yang sudah tersedia, sehingga prosesnya lebih cepat dan harganya lebih terjangkau. <strong>Custom</strong> berarti desain dibuat dari nol sesuai permintaan Anda — mulai dari warna, layout, hingga elemen grafis — sehingga hasilnya lebih personal namun membutuhkan waktu dan biaya lebih.</p>
        </details>

        <details class="faq_item">
            <summary>Apakah bisa cetak foto tanpa editing?</summary>
            <p>Bisa. Kami menerima layanan <strong>cetak langsung</strong> tanpa proses editing jika foto sudah siap cetak. Pastikan file yang dikirim memiliki resolusi dan ukuran yang sesuai dengan format cetak yang dipilih. Tim kami akan mengonfirmasi kesesuaian file sebelum proses cetak dimulai.</p>
        </details>

        <details class="faq_item">
            <summary>Bagaimana cara pengiriman hasil editing / cetak?</summary>
            <p>Hasil <strong>editing digital</strong> akan dikirimkan melalui WhatsApp atau Google Drive dalam format JPG/PNG. Untuk hasil <strong>cetak fisik</strong>, pengiriman dilakukan via jasa ekspedisi (JNE, J&T, SiCepat, dll.) ke seluruh Indonesia, atau bisa diambil langsung di toko kami. Ongkos kirim ditanggung oleh pelanggan.</p>
        </details>

        <details class="faq_item">
            <summary>Metode pembayaran apa saja yang tersedia?</summary>
            <p>Kami menerima berbagai metode pembayaran, antara lain <strong>transfer bank</strong> (BCA, Mandiri, BRI), <strong>dompet digital</strong> (GoPay, OVO, DANA, ShopeePay), serta pembayaran tunai untuk pelanggan yang datang langsung. Pembayaran dilakukan di muka sebelum proses dikerjakan.</p>
        </details>
    </section>

    <!-- footer -->
    <?php require_once __DIR__ . '/../components/footer.php'; ?>

    <!-- Script hamburger menu untuk mobile -->
    <script src="../public/assets/js/hamburger.js"></script>

</body>

</html>