<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/contact.css">
    <link rel="stylesheet" href="../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../public/assets/css/footer.css">
    <title>Frame Smile | Home</title>
</head>

<body>

    <!--Navbar-->
    <?php require_once __DIR__ . '/../components/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="text-container">
                <h1>Let's Get in Touch</h1>
                <p>Ingin mendapatkan jawaban atas pertanyaan Anda secara cepat?</p>
                <div class="social-contact-container">
                    <div class="social-contact">
                        <img src="../public/assets/contact-assets/images/email.svg" alt="">
                        <p>framesmile.id@gmail.com</p>
                    </div>
                    <div class="social-contact">
                        <img src="../public/assets/contact-assets/images/whatsapp.svg" alt="">
                        <p>0815xxxx7994</p>
                    </div>
                    <div class="social-contact">
                        <img src="../public/assets/img/instagram.svg" alt="">
                        <p>@frame.smile</p>
                    </div>
                    <div class="social-contact">
                        <img src="../public/assets/img/X.svg" alt="">
                        <p>@smilingyour.frame</p>
                    </div>
                </div>
            </div>

            <div class="form-container">
                <form action="">
                    <label for="name">Nama</label>
                    <input type="text" name="name" id="name" placeholder="Nama">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" placeholder="Email">
                    <label for="pesan">Pesan</label>
                    <textarea name="pesan" id="pesan" placeholder="Pesan"></textarea>
                    <button>Kirim</button>
                </form>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>
    
    <script src="/src/script.js"></script>
</body>

</html>