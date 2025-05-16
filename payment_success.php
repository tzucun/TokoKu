<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true) {
    $_SESSION['error'] = true;
    $_SESSION['message'] = "Admin tidak dapat mengakses halaman pembayaran";
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['order']) || !isset($_SESSION['checkout_step']) || $_SESSION['checkout_step'] !== 'payment_success') {
    header("Location: cart.php");
    exit();
}

require 'classes/Database.php';
require 'classes/ProductManager.php';

$productManager = new ProductManager();

if (isset($_POST['back_to_home'])) {
    // Clear cart and order data
    $_SESSION['cart'] = [];
    unset($_SESSION['order']);
    unset($_SESSION['checkout_step']);
    
    $_SESSION['message'] = "Terima kasih atas pesanan Anda!";
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>">
<head>
    <title>Pembayaran Berhasil - CunStore</title>
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
            
            <div class="step-connector completed"></div>
            
            <div class="step completed">
                <div class="step-number">2</div>
                <div class="step-label">Detail Pengiriman</div>
            </div>
            
            <div class="step-connector completed"></div>
            
            <div class="step completed">
                <div class="step-number">3</div>
                <div class="step-label">Konfirmasi</div>
            </div>
        </div>

        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Pembayaran Berhasil!</h2>
            <p>Pesanan Anda telah dikonfirmasi dan sedang diproses.</p>
            
            <div class="success-details">
                <div class="summary-item">
                    <span>ID Pesanan:</span>
                    <span class="success-order-id"><?= strtoupper(substr(md5(time()), 0, 10)) ?></span>
                </div>
                <div class="summary-item">
                    <span>Tanggal Pemesanan:</span>
                    <span><?= date('d F Y', strtotime($_SESSION['order']['date'])) ?></span>
                </div>
                <div class="summary-item">
                    <span>Estimasi Pengiriman:</span>
                    <span><?= date('d F Y', strtotime($_SESSION['order']['delivery_estimate'])) ?></span>
                </div>
                <div class="summary-item">
                    <span>Total Pembayaran:</span>
                    <span><?= $productManager->formatRupiah($_SESSION['order']['total']) ?></span>
                </div>
                <div class="summary-item">
                    <span>Metode Pembayaran:</span>
                    <span>
                        <?php 
                        $payment_method = $_SESSION['order']['payment_method'];
                        $payment_icon = '';
                        $payment_label = '';
                        
                        switch ($payment_method) {
                            case 'bank_transfer':
                                $payment_icon = 'fa-university';
                                $payment_label = 'Transfer Bank';
                                break;
                            case 'e_wallet':
                                $payment_icon = 'fa-wallet';
                                $payment_label = 'E-Wallet';
                                break;
                            case 'cod':
                                $payment_icon = 'fa-money-bill-wave';
                                $payment_label = 'COD (Bayar di Tempat)';
                                break;
                            case 'credit_card':
                                $payment_icon = 'fa-credit-card';
                                $payment_label = 'Kartu Kredit';
                                break;
                            default:
                                $payment_icon = 'fa-credit-card';
                                $payment_label = 'Unknown';
                        }
                        ?>
                        <i class="fas <?= $payment_icon ?>"></i> <?= $payment_label ?>
                    </span>
                </div>
            </div>
            
            <div class="delivery-address">
                <h3>Alamat Pengiriman</h3>
                <div class="address-details">
                    <p><strong><?= htmlspecialchars($_SESSION['order']['customer']['fullname']) ?></strong></p>
                    <p><?= htmlspecialchars($_SESSION['order']['customer']['phone']) ?></p>
                    <p><?= nl2br(htmlspecialchars($_SESSION['order']['customer']['address'])) ?></p>
                </div>
            </div>
            
            <div class="order-items">
                <h3>Detail Pesanan</h3>
                <?php foreach($_SESSION['order']['items'] as $item): ?>
                <div class="order-item">
                    <div class="item-name-qty">
                        <span><?= htmlspecialchars($item['name']) ?></span>
                        <span class="item-qty">x<?= $item['quantity'] ?></span>
                    </div>
                    <span class="item-price"><?= $productManager->formatRupiah($item['price'] * $item['quantity']) ?></span>
                </div>
                <?php endforeach; ?>
                <div class="order-total">
                    <span>Total:</span>
                    <span><?= $productManager->formatRupiah($_SESSION['order']['total']) ?></span>
                </div>
            </div>
            
            <p>Terima kasih telah berbelanja di CunStore!</p>
            <p>Rincian pesanan telah dikirim ke email: <strong><?= htmlspecialchars($_SESSION['order']['customer']['email']) ?></strong></p>
            
            <form method="post" class="mt-4">
                <button type="submit" name="back_to_home" class="btn-primary">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </button>
            </form>
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