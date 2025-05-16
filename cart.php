<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true) {
    $_SESSION['error'] = true;
    $_SESSION['message'] = "Admin tidak dapat mengakses keranjang";
    header("Location: index.php");
    exit();
}

require 'classes/Database.php';
require 'classes/ProductManager.php';
require 'utils/cartFunc.php';

?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>">
<head>
    <title>Keranjang Belanja - CunStore</title>
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
            <div class="step active">
                <div class="step-number">1</div>
                <div class="step-label">Keranjang</div>
            </div>
            
            <div class="step-connector"></div>
            
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-label">Detail Pengiriman</div>
            </div>
            
            <div class="step-connector"></div>
            
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-label">Konfirmasi</div>
            </div>
        </div>

        <h2 class="cart-title"><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h2>
        
        <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h3>Keranjang belanja kosong</h3>
            <p>Silakan tambahkan produk ke keranjang belanja.</p>
            <a href="index.php" class="back-to-shop">Kembali Belanja</a>
        </div>
        <?php else: ?>
        
        <div class="cart-items">
            <?php foreach($_SESSION['cart'] as $item): ?>
            <?php 
                $currentProduct = $productManager->getProductById($item['id']);
                $currentStock = $currentProduct['stock'];
                $exceedsStock = $item['quantity'] > $currentStock;
            ?>
            <div class="cart-item <?= $exceedsStock ? 'stock-warning' : '' ?>" id="cart-item-<?= $item['id'] ?>">
                <div class="item-image">
                    <img src="uploads/<?= $item['image'] ?: 'placeholder.jpg' ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                </div>
                <div class="item-details">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <p class="item-price"><?= $productManager->formatRupiah($item['price']) ?></p>
                    <?php if ($exceedsStock): ?>
                    <p class="stock-warning-text">Stok tersedia: <?= $currentStock ?></p>
                    <?php endif; ?>
                </div>
                <div class="item-quantity">
                    <div class="quantity-form">
                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" 
                               max="<?= $currentStock ?>" class="quantity-input <?= $exceedsStock ? 'input-error' : '' ?>" 
                               data-product-id="<?= $item['id'] ?>" data-product-price="<?= $item['price'] ?>">
                        <div class="update-status"></div>
                    </div>
                </div>
                <div class="item-subtotal" id="subtotal-<?= $item['id'] ?>">
                    <?= $productManager->formatRupiah($item['price'] * $item['quantity']) ?>
                </div>
                <div class="item-actions">
                    <button class="remove-btn" data-product-id="<?= $item['id'] ?>">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-summary">
            <div class="summary-row">
                <span class="summary-label">Total:</span>
                <span class="summary-value" id="cart-total"><?= $productManager->formatRupiah($total) ?></span>
            </div>
            
            <form method="post" class="checkout-form">
                <button type="submit" name="proceed_to_checkout" class="checkout-btn">
                    <i class="fas fa-shopping-bag"></i>
                    Lanjutkan ke Pembayaran
                </button>
            </form>
            
            <a href="index.php" class="continue-shopping">
                <i class="fas fa-arrow-left"></i>
                Lanjutkan Belanja
            </a>
        </div>
        <?php endif; ?>
    </main>

    <footer class="main-footer">
        <p>&copy; 2025 CunStore.</p>
        <p>Nama: Stanley Gilbert Lionardi</p>
        <p>NIM: 09021282328042</p>
        <p>Kelas: TIBIL P2/2023</p>
    </footer>

    <script>
    $(document).ready(function() {
        $('.quantity-input').on('change', function() {
            const productId = $(this).data('product-id');
            const quantity = $(this).val();
            const updateStatus = $(this).siblings('.update-status');
            
            updateStatus.html('<i class="fas fa-spinner fa-spin"></i>');
            
            $.ajax({
                url: 'cart.php',
                type: 'POST',
                data: {
                    ajax_update_quantity: true,
                    product_id: productId,
                    quantity: quantity
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateStatus.html('<i class="fas fa-check text-success"></i>');
                        
                        if (response.removed) {
                            $(`#cart-item-${productId}`).fadeOut(300, function() {
                                $(this).remove();
                                
                                if ($('.cart-item').length === 0) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            $(`#subtotal-${productId}`).text(response.new_subtotal_formatted);
                            
                            $('#cart-total').text(response.new_total_formatted);
                            
                            $(`#cart-item-${productId}`).removeClass('stock-warning');
                            $(`#cart-item-${productId} .quantity-input`).removeClass('input-error');
                            $(`#cart-item-${productId} .stock-warning-text`).remove();
                        }
                        
                        setTimeout(function() {
                            updateStatus.html('');
                        }, 1500);
                    } else {
                        updateStatus.html('<i class="fas fa-times text-danger"></i>');
                        alert(response.message);
                        
                        $(this).val($(this).prop('defaultValue'));
                        
                        setTimeout(function() {
                            updateStatus.html('');
                        }, 1500);
                    }
                },
                error: function() {
                    updateStatus.html('<i class="fas fa-times text-danger"></i>');
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                    
                    setTimeout(function() {
                        updateStatus.html('');
                    }, 1500);
                }
            });
        });

        $('.remove-btn').on('click', function() {
            const productId = $(this).data('product-id');
            
            $.ajax({
                url: 'cart.php',
                type: 'POST',
                data: {
                    ajax_update_quantity: true,
                    product_id: productId,
                    quantity: 0
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $(`#cart-item-${productId}`).fadeOut(300, function() {
                            $(this).remove();
                            
                            $('#cart-total').text(response.new_total_formatted);
                            
                            if ($('.cart-item').length === 0) {
                                window.location.reload();
                            }
                        });
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        });
    });
    </script>
</body>
</html>