<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/login.css">
    <link rel="stylesheet" href="../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../public/assets/css/footer.css">
    <script src="login.js"></script>
    <title>FRAME SMILE | Login</title>
</head>
<body>

    <?php require_once __DIR__ . '/../components/navbar.php'; ?>

    <p id="notif" class="notif"></p>

    <main class="container">
        <div class="login-card">

            <h2>Welcome Back!</h2>
            
            <form class="login-form" action="../api/login.php" method="POST">
                <div class="input-group">
                    <input type="text" name="username" id="username" placeholder="Enter Your Username" required>
                </div>
                
                <div class="input-group password-group">
                    <input type="password" name="password" id="password" placeholder="Enter Your Password" required>
                    </div>
    
                <button type="submit" class="btn-signin">Sign In</button>
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
    
            <p class="register-link">Don't Have Account? <a href="sign-up.php">>> Register Now <<</a></p>
        </div>
    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>
    
</body>
</html>