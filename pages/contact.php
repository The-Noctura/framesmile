<?php
// pages/contact.php
session_start();
require_once __DIR__ . '/../includes/db.php';

// ── Buat tabel contacts kalau belum ada ──
mysqli_query($koneksi, "
    CREATE TABLE IF NOT EXISTS `contacts` (
        `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `name`       VARCHAR(100) NOT NULL,
        `email`      VARCHAR(100) NOT NULL,
        `message`    TEXT NOT NULL,
        `ip_address` VARCHAR(45),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

// ── Config email admin ──
// Ganti dengan email admin dan kredensial Gmail SMTP kamu
define('ADMIN_EMAIL',    'ujangzombie130@gmail.com');
define('GMAIL_USER',     'mochalgani597@gmail.com');    // ← email Gmail pengirim
define('GMAIL_PASSWORD', 'wzuy qaey jrsd ufpt');        // ← App Password Gmail (bukan password biasa!)
// Cara buat App Password: myaccount.google.com → Security → 2-Step Verification → App Passwords

$status  = '';   // 'success' | 'error'
$message = '';
$oldInput = ['name' => '', 'email' => '', 'pesan' => ''];
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    $oldInput = ['name' => $name, 'email' => $email, 'pesan' => $pesan];

    // ── VALIDASI ──
    if (!$name)                        $errors['name']  = 'Nama wajib diisi.';
    elseif (strlen($name) < 2)         $errors['name']  = 'Nama minimal 2 karakter.';

    if (!$email)                       $errors['email'] = 'Email wajib diisi.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Format email tidak valid.';

    if (!$pesan)                       $errors['pesan'] = 'Pesan wajib diisi.';
    elseif (strlen($pesan) < 10)       $errors['pesan'] = 'Pesan minimal 10 karakter.';

    if (empty($errors)) {
        // ── SIMPAN KE DATABASE ──
        $nameSafe  = mysqli_real_escape_string($koneksi, $name);
        $emailSafe = mysqli_real_escape_string($koneksi, $email);
        $pesanSafe = mysqli_real_escape_string($koneksi, $pesan);
        $ip        = mysqli_real_escape_string($koneksi, $_SERVER['REMOTE_ADDR'] ?? '');

        $saved = mysqli_query($koneksi, "
            INSERT INTO contacts (name, email, message, ip_address)
            VALUES ('$nameSafe', '$emailSafe', '$pesanSafe', '$ip')
        ");

        // ── KIRIM EMAIL VIA PHPMailer ──
        $emailSent = false;
        if ($saved) {
            $emailSent = sendContactEmail($name, $email, $pesan);
        }

        if ($saved) {
            $status  = 'success';
            $message = $emailSent
                ? 'Pesan berhasil dikirim! Kami akan segera menghubungi kamu.'
                : 'Pesan berhasil disimpan! (Email notifikasi admin mungkin tertunda.)';
            $oldInput = ['name' => '', 'email' => '', 'pesan' => ''];
        } else {
            $status  = 'error';
            $message = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}

// ── FUNGSI KIRIM EMAIL ──
function sendContactEmail($name, $email, $pesan) {
    // Cek apakah PHPMailer tersedia
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoload)) return sendWithMailFunction($name, $email, $pesan);

    require_once $autoload;

    // Cek class ada
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return sendWithMailFunction($name, $email, $pesan);
    }

    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = GMAIL_USER;
        $mail->Password   = GMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(GMAIL_USER, 'FrameSmile Website');
        $mail->addAddress(ADMIN_EMAIL, 'Admin FrameSmile');
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = '[FrameSmile] Pesan Baru dari ' . $name;
        $mail->Body    = emailTemplate($name, $email, $pesan);
        $mail->AltBody = "Pesan baru dari: $name\nEmail: $email\n\nPesan:\n$pesan";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('PHPMailer error: ' . $e->getMessage());
        return false;
    }
}

// Fallback: pakai mail() bawaan PHP
function sendWithMailFunction($name, $email, $pesan) {
    $subject = '[FrameSmile] Pesan Baru dari ' . $name;
    $headers = "From: " . GMAIL_USER . "\r\n"
             . "Reply-To: $email\r\n"
             . "Content-Type: text/html; charset=UTF-8\r\n";
    return @mail(ADMIN_EMAIL, $subject, emailTemplate($name, $email, $pesan), $headers);
}

function emailTemplate($name, $email, $pesan) {
    $pesanHtml = nl2br(htmlspecialchars($pesan));
    $date      = date('d M Y, H:i');
    return "
    <div style='font-family:Poppins,Arial,sans-serif;max-width:560px;margin:0 auto;background:#f4f4f4;border-radius:8px;overflow:hidden;'>
      <div style='background:#111;padding:24px 28px;'>
        <span style='font-family:Montserrat,Arial,sans-serif;font-size:20px;font-weight:800;color:#fff;'>
          Frame<span style='color:#FF7979;'>Smile</span>
        </span>
        <p style='color:#999;font-size:12px;margin:4px 0 0;'>Pesan baru masuk dari website</p>
      </div>
      <div style='background:#fff;padding:28px;border:1px solid #e8e8e8;'>
        <table style='width:100%;border-collapse:collapse;font-size:13px;'>
          <tr><td style='padding:8px 0;color:#888;width:90px;'>Nama</td><td style='padding:8px 0;font-weight:600;'>".htmlspecialchars($name)."</td></tr>
          <tr><td style='padding:8px 0;color:#888;'>Email</td><td style='padding:8px 0;'><a href='mailto:".htmlspecialchars($email)."' style='color:#FF7979;'>".htmlspecialchars($email)."</a></td></tr>
          <tr><td style='padding:8px 0;color:#888;'>Waktu</td><td style='padding:8px 0;'>$date WIB</td></tr>
        </table>
        <div style='margin-top:16px;padding:16px;background:#f9f9f9;border-left:3px solid #FF7979;border-radius:4px;font-size:13px;line-height:1.7;color:#333;'>
          $pesanHtml
        </div>
        <div style='margin-top:20px;'>
          <a href='mailto:".htmlspecialchars($email)."' style='display:inline-block;padding:10px 22px;background:#FF7979;color:#fff;border-radius:6px;font-weight:700;font-family:Montserrat,Arial,sans-serif;font-size:12px;text-decoration:none;'>
            Balas Pesan →
          </a>
        </div>
      </div>
      <div style='padding:14px 28px;text-align:center;font-size:11px;color:#999;'>
        FrameSmile · framesmile.id@gmail.com
      </div>
    </div>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/contact.css">
    <link rel="stylesheet" href="../public/assets/css/navbar.css">
    <link rel="stylesheet" href="../public/assets/css/footer.css">
    <title>Frame Smile | Contact</title>
    <style>
        /* ── Notifikasi ── */
        .fs-alert {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 13px 16px; border-radius: 6px;
            font-size: 13px; line-height: 1.5;
            margin-bottom: 18px; animation: slideDown .3s ease;
        }
        .fs-alert.success { background: #f0fdf4; border: 1px solid #86efac; color: #15803d; }
        .fs-alert.error   { background: #fff0f0; border: 1px solid #fca5a5; color: #dc2626; }
        .fs-alert-icon    { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .fs-alert-close   { margin-left: auto; background: none; border: none; cursor: pointer; font-size: 16px; color: inherit; opacity: .6; }
        .fs-alert-close:hover { opacity: 1; }

        /* ── Field error ── */
        .field-error {
            display: block; font-size: 11px; color: #dc2626;
            margin-top: 4px; font-family: 'Poppins', sans-serif;
        }
        .input-error { border-color: #fca5a5 !important; background: #fff5f5 !important; }

        /* ── Loading state ── */
        .btn-loading { opacity: .7; pointer-events: none; }
        .btn-loading::after { content: ' ⏳'; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <?php require_once __DIR__ . '/../components/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="text-container">
                <h1>Let's Get in Touch</h1>
                <p>Ingin mendapatkan jawaban atas pertanyaan Anda secara cepat?</p>
                <div class="social-contact-container">
                    <div class="social-contact">
                        <img src="../public/assets/contact-assets/images/email.svg" alt="Email">
                        <p>framesmile.id@gmail.com</p>
                    </div>
                    <div class="social-contact">
                        <img src="../public/assets/contact-assets/images/whatsapp.svg" alt="WhatsApp">
                        <p>0815xxxx7994</p>
                    </div>
                    <div class="social-contact">
                        <img src="../public/assets/img/instagram.svg" alt="Instagram">
                        <p>@frame.smile</p>
                    </div>
                    <div class="social-contact">
                        <img src="../public/assets/img/X.svg" alt="X (Twitter)">
                        <p>@smilingyour.frame</p>
                    </div>
                </div>
            </div>

            <div class="form-container">

                <!-- Notifikasi sukses/gagal -->
                <?php if ($status === 'success'): ?>
                <div class="fs-alert success" id="fsAlert">
                    <span class="fs-alert-icon">✓</span>
                    <span><?= htmlspecialchars($message) ?></span>
                    <button class="fs-alert-close" onclick="dismissAlert()">✕</button>
                </div>
                <?php elseif ($status === 'error'): ?>
                <div class="fs-alert error" id="fsAlert">
                    <span class="fs-alert-icon">⚠</span>
                    <span><?= htmlspecialchars($message) ?></span>
                    <button class="fs-alert-close" onclick="dismissAlert()">✕</button>
                </div>
                <?php endif; ?>

                <form action="" method="POST" id="contactForm" novalidate>

                    <label for="name">Nama</label>
                    <input type="text" name="name" id="name"
                           placeholder="Nama lengkap kamu"
                           value="<?= htmlspecialchars($oldInput['name']) ?>"
                           class="<?= isset($errors['name']) ? 'input-error' : '' ?>">
                    <?php if (isset($errors['name'])): ?>
                        <span class="field-error"><?= htmlspecialchars($errors['name']) ?></span>
                    <?php endif; ?>

                    <label for="email">Email</label>
                    <input type="email" name="email" id="email"
                           placeholder="email@kamu.com"
                           value="<?= htmlspecialchars($oldInput['email']) ?>"
                           class="<?= isset($errors['email']) ? 'input-error' : '' ?>">
                    <?php if (isset($errors['email'])): ?>
                        <span class="field-error"><?= htmlspecialchars($errors['email']) ?></span>
                    <?php endif; ?>

                    <label for="pesan">Pesan</label>
                    <textarea name="pesan" id="pesan"
                              placeholder="Tulis pesanmu di sini..."
                              class="<?= isset($errors['pesan']) ? 'input-error' : '' ?>"><?= htmlspecialchars($oldInput['pesan']) ?></textarea>
                    <?php if (isset($errors['pesan'])): ?>
                        <span class="field-error"><?= htmlspecialchars($errors['pesan']) ?></span>
                    <?php endif; ?>

                    <button type="submit" id="submitBtn">Kirim</button>
                </form>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>

    <script>
    // ── Dismiss alert ──
    function dismissAlert() {
        const el = document.getElementById('fsAlert');
        if (el) { el.style.opacity = '0'; el.style.transform = 'translateY(-6px)'; el.style.transition = '.25s'; setTimeout(() => el.remove(), 250); }
    }

    // ── Auto dismiss success setelah 5 detik ──
    <?php if ($status === 'success'): ?>
    setTimeout(dismissAlert, 5000);
    <?php endif; ?>

    // ── Client-side validasi real-time ──
    const form    = document.getElementById('contactForm');
    const nameEl  = document.getElementById('name');
    const emailEl = document.getElementById('email');
    const pesanEl = document.getElementById('pesan');
    const btnEl   = document.getElementById('submitBtn');

    function showErr(input, msg) {
        input.classList.add('input-error');
        let span = input.nextElementSibling;
        if (!span || !span.classList.contains('field-error')) {
            span = document.createElement('span');
            span.className = 'field-error';
            input.after(span);
        }
        span.textContent = msg;
    }
    function clearErr(input) {
        input.classList.remove('input-error');
        const span = input.nextElementSibling;
        if (span && span.classList.contains('field-error')) span.remove();
    }

    nameEl.addEventListener('blur', () => {
        const v = nameEl.value.trim();
        if (!v) showErr(nameEl, 'Nama wajib diisi.');
        else if (v.length < 2) showErr(nameEl, 'Nama minimal 2 karakter.');
        else clearErr(nameEl);
    });

    emailEl.addEventListener('blur', () => {
        const v = emailEl.value.trim();
        if (!v) showErr(emailEl, 'Email wajib diisi.');
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) showErr(emailEl, 'Format email tidak valid.');
        else clearErr(emailEl);
    });

    pesanEl.addEventListener('blur', () => {
        const v = pesanEl.value.trim();
        if (!v) showErr(pesanEl, 'Pesan wajib diisi.');
        else if (v.length < 10) showErr(pesanEl, 'Pesan minimal 10 karakter.');
        else clearErr(pesanEl);
    });

    // ── Submit: loading state ──
    form.addEventListener('submit', e => {
        // Final validasi sebelum submit
        let valid = true;
        const name  = nameEl.value.trim();
        const email = emailEl.value.trim();
        const pesan = pesanEl.value.trim();

        if (!name || name.length < 2)  { showErr(nameEl, !name ? 'Nama wajib diisi.' : 'Nama minimal 2 karakter.'); valid = false; }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showErr(emailEl, !email ? 'Email wajib diisi.' : 'Format email tidak valid.'); valid = false; }
        if (!pesan || pesan.length < 10) { showErr(pesanEl, !pesan ? 'Pesan wajib diisi.' : 'Pesan minimal 10 karakter.'); valid = false; }

        if (!valid) { e.preventDefault(); return; }

        btnEl.textContent = 'Mengirim...';
        btnEl.classList.add('btn-loading');
    });
    </script>

</body>
</html>
