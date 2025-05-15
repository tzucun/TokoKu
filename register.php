<?php
session_start();
require 'classes/Database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $error = '';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = true;
        $_SESSION['message'] = '⛔ Semua kolom harus diisi';
        header("Location: register.php");
        exit();
    } 
    
    if ($password !== $confirm_password) {
        $_SESSION['error'] = true;
        $_SESSION['message'] = '⛔ Password tidak cocok';
        header("Location: register.php");
        exit();
    } 
    
    if (strlen($password) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $_SESSION['error'] = true;
        $_SESSION['message'] = '⛔ Password harus minimal 8 karakter dan mengandung minimal 1 simbol!';
        header("Location: register.php");
        exit();
    }

    if ($email === 'admin@gmail.com') {
        $_SESSION['error'] = true;
        $_SESSION['message'] = '⛔ Email ini tidak dapat digunakan';
        header("Location: register.php");
        exit();
    }
 
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = true;
        $_SESSION['message'] = '⛔ Username atau email sudah terdaftar!';
        header("Location: register.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        $_SESSION['error'] = false;
        $_SESSION['message'] = '🎉 Registrasi berhasil! Silakan login.';
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = true;
        $_SESSION['message'] = '💥 Registrasi gagal! Silakan coba lagi.';
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CunStore</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'message.php'; ?>
    
    <div class="auth-container">
        <div class="auth-card">
            <h2>🔐 Daftar Akun</h2>
            <form method="POST">
                <div class="form-group">
                    <input type="text" 
                           name="username" 
                           placeholder="Username" 
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" 
                           required>
                </div>
                <div class="form-group">
                    <input type="email" 
                           name="email" 
                           placeholder="Email" 
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                           required>
                </div>
                <div class="form-group">
                    <input type="password" 
                           name="password" 
                           placeholder="Password" 
                           required
                           pattern="(?=.*[!@#$%^&*(),.?\:{}|<>]).{8,}"
                           title="Minimal 8 karakter dengan 1 simbol">
                </div>
                <button type="submit" class="btn-submit">📝 Daftar</button>
            </form>
            <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
        </div>
    </div>

    <button class="theme-toggle" onclick="toggleTheme()">
        <?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? '🌞' : '🌙' ) ?>
    </button>

    <script>
    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        html.setAttribute('data-theme', newTheme);
        document.cookie = `theme=${newTheme}; path=/; max-age=${30 * 24 * 60 * 60}`;
        document.querySelector('.theme-toggle').textContent = 
            newTheme === 'dark' ? '🌞' : '🌙';
    }
    </script>
</body>
</html>