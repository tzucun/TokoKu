<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Admin can't access checkout
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true) {
    $_SESSION['error'] = true;
    $_SESSION['message'] = "Admin tidak dapat mengakses checkout";
    header("Location: index.php");
    exit();
}

// Redirect to cart if cart is empty
if (empty($_SESSION['cart'])) {
    $_SESSION['error'] = true;
    $_SESSION['message'] = "Keranjang kosong";
    header("Location: cart.php");
    exit();
}

require 'classes/Database.php';
require 'classes/ProductManager.php';
require 'utils/checkoutFunc.php';
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>">
<head>
    <title>Checkout - CunStore</title>
    <?php include 'utils/headerSet.php'; ?>
</head>

<body>
    <header class="main-header">
        <div class="header-content">
            <h1 class="logo">
                <i class="fas fa-box-open"></i>
                CunStore 
            </h1>
            <div class="controls">
                <a href="index.php" class="admin-btn">
                    <i class="fas fa-home"></i>
                    Kembali ke Beranda
                </a>
                <button class="theme-toggle">
                    <?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? '🌞' : '🌙' ?>
                </button>
                <a href="auth/logout.php" class="admin-btn logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </header>

    <main class="cart-container">
        <?php include 'utils/message.php'; ?>

        <div class="checkout-steps">
            <div class="step completed">
                <div class="step-number">1</div>
                <div class="step-label">Keranjang</div>
            </div>
            
            <div class="step-connector"></div>
            
            <div class="step active">
                <div class="step-number">2</div>
                <div class="step-label">Detail Pengiriman</div>
            </div>
            
            <div class="step-connector"></div>
            
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-label">Konfirmasi</div>
            </div>
        </div>
        
        <h2 class="cart-title"><i class="fas fa-shipping-fast"></i> Detail Pengiriman</h2>
        
        <div class="checkout-container">
            <div class="checkout-form">
                <form method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fullname">Nama Lengkap</label>
                            <input type="text" id="fullname" name="fullname" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Nomor Telepon</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Alamat Lengkap</label>
                            <textarea id="address" name="address" rows="3" required></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group payment-method-group">
                            <label>Metode Pembayaran</label>
                            <div class="payment-options">
                                <div class="payment-option">
                                    <input type="radio" id="payment-transfer" name="payment_method" value="bank_transfer" required>
                                    <label for="payment-transfer">
                                        <i class="fas fa-university"></i>
                                        Transfer Bank
                                    </label>
                                </div>
                                
                                <div class="payment-option">
                                    <input type="radio" id="payment-ewallet" name="payment_method" value="e_wallet" required>
                                    <label for="payment-ewallet">
                                        <i class="fas fa-wallet"></i>
                                        E-Wallet
                                    </label>
                                </div>
                                
                                <div class="payment-option">
                                    <input type="radio" id="payment-cod" name="payment_method" value="cod" required>
                                    <label for="payment-cod">
                                        <i class="fas fa-money-bill-wave"></i>
                                        COD (Bayar di Tempat)
                                    </label>
                                </div>
                                
                                <div class="payment-option">
                                    <input type="radio" id="payment-credit-card" name="payment_method" value="credit_card" required>
                                    <label for="payment-credit-card">
                                        <i class="fas fa-credit-card"></i>
                                        Kartu Kredit
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="submit_order" class="btn-primary">
                        <i class="fas fa-check"></i> Konfirmasi Pesanan
                    </button>
                </form>
            </div>
            
            <div class="order-summary">
                <div class="summary-section">
                    <h3>Ringkasan Pesanan</h3>
                    
                    <?php foreach($_SESSION['cart'] as $item): ?>
                    <div class="summary-item">
                        <span><?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] ?>x)</span>
                        <span><?= $productManager->formatRupiah($item['price'] * $item['quantity']) ?></span>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="summary-item summary-total">
                        <span>Total</span>
                        <span><?= $productManager->formatRupiah($total) ?></span>
                    </div>
                </div>
                
                <div class="summary-section">
                    <h3>Estimasi Pengiriman</h3>
                    <p><?= date('d F Y', strtotime('+3 days')) ?></p>
                </div>
                
                <a href="cart.php" class="continue-shopping">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Keranjang
                </a>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <p>&copy; 2025 CunStore.</p>
        <p>Nama: Stanley Gilbert Lionardi</p>
        <p>NIM: 09021282328042</p>
        <p>Kelas: TIBIL P2/2023</p>
    </footer>
</body>
</html>