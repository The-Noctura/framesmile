<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/product.css">
    <link rel="stylesheet" href="../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../public/assets/css/footer.css">
    <title>Frame Smile | Home</title>
</head>

<body>

    <!--Navbar-->
    <?php require_once __DIR__ . '/../components/navbar.php'; ?>

    <main class="product-page">
        <section class="product-hero">
            <h1>LET’S SEE OUR PRODUCT!</h1>

            <h2>Custom</h2>
            <div class="product-grid">
                <article class="product-card">
                    <img src="../public/assets/product-assets/images/products/product-1.png"
                        alt="Photostrip template with vintage style" loading="lazy">
                </article>
                <article class="product-card">
                    <img src="../public/assets/product-assets/images/products/product-2.png"
                        alt="Photostrip template with vintage style" loading="lazy">
                </article>
                <article class="product-card">
                    <img src="../public/assets/product-assets/images/products/product-3.png"
                        alt="Photostrip template with vintage style" loading="lazy">
                </article>
            </div>

            <h2>Template</h2>
            <div class="template-grid">
                <article class="product-card">
                    <img src="../public/assets/product-assets/images/templates/template-1.png"
                        alt="Photostrip template with vintage style" loading="lazy">
                </article>
                <article class="product-card">
                    <img src="../public/assets/product-assets/images/templates/template-2.png"
                        alt="Photostrip template with vintage style" loading="lazy">
                </article>
                <article class="product-card">
                    <img src="../public/assets/product-assets/images/templates/template-3.png"
                        alt="Photostrip template with vintage style" loading="lazy">
                </article>
                <article class="product-card">
                    <img src="../public/assets/product-assets/images/templates/template-4.png"
                        alt="Photostrip template with vintage style" loading="lazy">
                </article>
                <article class="product-card">
                    <img src="../public/assets/product-assets/images/templates/template-5.png"
                        alt="Photostrip template with vintage style" loading="lazy">
                </article>
            </div>
            <h3>And Other Templates</h3>
        </section>

        <section class="price-list">
            <h4>Get your custom photostrip IDR 15K
                (Editing photostrip + bebas custom foto
                sesuka hati + cetak foto online)</h4>
            <h4>Get your template photostrip IDR 10K
                (Editing photostrip+cetak foto online)</h4>
        </section>

        <a href="#" class="cta">Get Your Frame, Now</a>
    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>


    <script src="/src/script.js"></script>
</body>

</html>