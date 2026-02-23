<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/sign-up.css">
    <link rel="stylesheet" href="../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../public/assets/css/footer.css">
    <title>FRAME SMILE | Sign-up</title>
</head>
<body>

    <?php require_once __DIR__ . '/../components/navbar.php'; ?>

    <main class="container">
        <div class="login-card">

            <h2>Sign Up</h2>
            <?php
                if (isset($_GET['error'])) {
                    echo '<p class="error-message">' . htmlspecialchars($_GET['error']) . '</p>';
                }
            ?>
            
            <form class="Sign-up-form" action="../api/register.php" method="POST">
                <div class="input-group">
                    <label for="nama-depan">Nama Depan</label>
                    <input type="text" name="first_name" id="nama-depan" placeholder="Masukan Nama Depan" required>
                </div>

                <div class="input-group">
                    <label for="nama-belakang">Nama Belakang</label>
                    <input type="text" name="last_name" id="nama-belakang" placeholder="Masukan Nama Belakang" required>
                </div>  
                
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter Your Username" required>
                </div>
                
                <div class="input-group password-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter Your Password" required>
                </div>

                <div class="input-group Email">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Enter Your Email" required>
                </div>
    
                <button type="submit" class="btn-signin">Sign Up</button>
            </form>
    
            <p class="or-text">Or Log in with</p>
            
            <div class="social-login">
                
                <button class="social-btn google-btn">
                    <img src="../public/assets/login-assets/google-logo.png" alt="Google Logo" class="social-img">
                    Google
                </button>
    
                <button class="social-btn apple-btn">
                    <img src="../public/assets/login-assets/apple-logo.png" alt="Apple Logo" class="social-img">
                    Apple
                </button>
            </div>
    
            <p class="register-link">do you Have Account? <a href="login.php">>> Login Now <<</a></p>
        </div>
    </main>
    
    <?php require_once __DIR__ . '/../components/footer.php'; ?>

    <script src='../public/assets/js/sign-up-validation.js'></script>
</body>
</html>