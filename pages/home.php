<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/home.css">
    <link rel="stylesheet" href="../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../public/assets/css/footer.css">
    <link rel="stylesheet" href="../public/assets/css/faq.css">
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
            <p>Koleksi template edit photobooth yang siap membuat setiap foto menjadi istimew.</p>
            <a href="#" class="cta">Get Your Frame, Now</a>
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
            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Officia, ipsa!</p>
        </details>
        <details class="faq_item">
            <summary>Apa saja file yang perlu saya kirimkan?</summary>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorem, sed.</p>
        </details>
        <details class="faq_item">
            <summary>Apa bedanya template dan custom?</summary>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ipsam, voluptatum.</p>
        </details>
        <details class="faq_item">
            <summary>Apakah bisa cetak foto tanpa editing?</summary>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusantium, eos!</p>
        </details>
        <details class="faq_item">
            <summary>Bagaimana cara pengiriman hasil editing / cetak?</summary>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Cumque, eos!</p>
        </details>
        <details class="faq_item">
            <summary>Metode pembayaran apa saja yang tersedia?</summary>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Culpa, possimus.</p>
        </details>
    </section>

    <!-- footer -->
    <?php require_once __DIR__ . '/../components/footer.php'; ?>
    
    <script src="/src/script.js"></script>
</body>

</html>