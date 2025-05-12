<?php
session_start();
require 'classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance()->getConnection();
    $usernameOrEmail = $_POST['username_or_email'];
    $password = $_POST['password'];

    // Cari user berdasarkan username ATAU email
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = true;
        $_SESSION['message'] = '⛔ Username/Email atau password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TokoKU</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'message.php'; ?> <!-- Tempat alert akan muncul -->
    
    <div class="auth-container">
        <div class="auth-card">
            <h2>🔑 Login Akun</h2>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="username_or_email" placeholder="Username atau Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn-submit">🚀 Login</button>
            </form>
            <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
        </div>
    </div>

    <button class="theme-toggle" onclick="toggleTheme()">
        <?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? '🌞' : '🌙' )?>
    </button>

    <script>
    function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    // Update attribute dan cookie
    html.setAttribute('data-theme', newTheme);
    document.cookie = `theme=${newTheme}; path=/; max-age=${30 * 24 * 60 * 60}`; // 30 hari
    
    // Update tombol
    document.querySelector('.theme-toggle').textContent = 
        newTheme === 'dark' ? '🌞' : '🌙';
    }
    </script>
</body>
</html>