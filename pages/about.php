<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/about.css">
    <link rel="stylesheet" href="../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../public/assets/css/footer.css">
    <title>Frame Smile | About</title>
</head>
<body>

    <?php require_once __DIR__ . '/../components/navbar.php'; ?>

    <section class="about-hero">
        <div class="hero-content">
            <h1 class="hero-title">Membingkai senyum terbaikmu dengan sempurna</h1>
            <p class="hero-text">
                Frame Smile hadir untuk membuat semua momen semakin berkesan, kami menyediakan berbagai template "photobooth unik, modern dan siap dipersonalisasi". 
            </p>
        </div>
        <div class="hero-image">
            <div class="image-circle">
                <img src="../public/assets/img/hero-img.png" alt="Photo Booth Collage" class="main-image">
            </div>
        </div>
    </section>

    <section class="choice-reason">
        <h2>Menjadi pilihan utama untuk </h2>
        <h2>"Photobooth kreatif dan memoreable"</h2>
        <div class="choice-cards">
            <div class="card">
                <div class="icon-box">💡</div> <h3>Kreatif</h3>
                <p>Template unik dari desainer</p>
            </div>
            <div class="card">
                <div class="icon-box">🧑&zwj;💻</div> <h3>Personal</h3>
                <p>Bisa disesuaikan dengan gayamu</p>
            </div>
            <div class="card">
                <div class="icon-box">🤍</div> <h3>Memoreable</h3>
                <p>Foto jadi kenangan berharga</p>
            </div>
        </div>
    </section>

    <section class="why-choose-us">
        <h2>Kenapa Pilih Kami?</h2>
        <div class="choose-cards">
            <div class="card">
                <div class="icon-box pink-icon">📱</div>
                <h3>Desain Modern</h3>
            </div>
            <div class="card">
                <div class="icon-box pink-icon">📢</div>
                <h3>Cocok untuk event</h3>
            </div>
            <div class="card">
                <div class="icon-box pink-icon">💖</div>
                <h3>Kenangan Berkesan</h3>
            </div>
            <div class="card">
                <div class="icon-box pink-icon">⚡</div>
                <h3>Mudah & cepat</h3>
            </div>
        </div>
    </section>

    <section class="cta-banner">
        <h2>Siap membuat momenmu tak terlupakan?</h2>
        <a href="#" class="cta-button">Get Your Frame, Now</a>
    </section>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>

    <script src="/src/script.js"></script>
</body>
</html>