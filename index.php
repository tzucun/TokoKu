<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

require 'classes/Database.php';
require 'classes/ProductManager.php';

$productManager = new ProductManager();
$sort = $_GET['sort'] ?? 'price_asc';
$products = $productManager->getAllProducts($sort);

$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true;

?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>">
<head>
    <title>CunStore</title>
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
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Cari produk..." autocomplete="off">
                    <i class="fas fa-search search-icon"></i>
                </div>
                
                <button class="theme-toggle">
                    <?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? '🌞' : '🌙' ?>
                </button>
                <form class="sort-form">
                    <select name="sort" class="sort-select" onchange="this.form.submit()">
                        <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Harga Terendah</option>
                        <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Harga Tertinggi</option>
                    </select>
                </form>
                <?php include 'utils/message.php'; ?>
                
                <?php if (!$isAdmin): ?>
                <a href="cart.php" class="admin-btn">
                    <i class="fas fa-shopping-cart"></i>
                    Keranjang
                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                    <span class="cart-badge"><?= count($_SESSION['cart']) ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>

                <?php if ($isAdmin): ?>
                <a href="manageProduct.php" class="admin-btn">
                    <i class="fas fa-tools"></i>
                    Manage Product
                </a>
                <?php endif; ?>

                <a href="auth/logout.php" class="admin-btn logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </header>

    <main class="product-container">
        <div id="searchStatus" class="search-status" style="display: none;">
            <span id="searchText">Menampilkan hasil pencarian untuk: "<span id="searchQuery"></span>"</span>
            <button id="clearSearch" class="clear-search">
                <i class="fas fa-times"></i> Hapus pencarian
            </button>
        </div>
        
        <div class="product-grid" id="productGrid">
            <?php foreach($products as $product): ?>
            <article class="product-card" data-id="<?= $product['id'] ?>">
                <div class="card-image">
                    <img src="uploads/<?= $product['image'] ?: 'placeholder.jpg' ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         loading="lazy">
                </div>
                <div class="card-body">
                    <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="product-desc"><?= htmlspecialchars($product['description']) ?></p>
                    <div class="product-meta">
                        <div class="price-tag">
                            <?= $productManager->formatRupiah($product['price']) ?>
                        </div>
                        <div class="stock-status <?= $product['stock'] > 0 ? 'in-stock' : 'out-stock' ?>">
                            <i class="fas fa-cubes"></i>
                            <?= $product['stock'] > 0 ? "Stok: {$product['stock']}" : "Stok Habis" ?>
                        </div>
                    </div>
                    <?php if (!$isAdmin && $product['stock'] > 0): ?>
                    <form method="post" action="cart.php">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" name="add_to_cart" class="buy-button">
                            <i class="fas fa-shopping-cart"></i>
                            Beli Sekarang
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <div id="noResults" class="no-results" style="display: none;">
            <i class="fas fa-search"></i>
            <h3>Tidak ada produk yang ditemukan</h3>
            <p>Coba dengan kata kunci lain</p>
        </div>
        
        <div id="loadingSpinner" class="loading-spinner">
            <i class="fas fa-spinner"></i>
            <p>Memuat produk...</p>
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