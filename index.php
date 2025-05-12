<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'classes/Database.php';
require 'classes/ProductManager.php';

$productManager = new ProductManager();
$sort = $_GET['sort'] ?? 'price_asc';
$products = $productManager->getAllProducts($sort);
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>">
<head>
    <title>TokoKU Inventory</title>
    <?php include 'headerSet.php'; ?>
</head>

<body>
    <header class="main-header">
        <div class="header-content">
            <h1 class="logo">
                <i class="fas fa-box-open"></i>
                TokoKU Inventory
            </h1>
            <div class="controls">
                <!-- Search Bar -->
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
                <a href="manageProduct.php" class="admin-btn">
                    <i class="fas fa-tools"></i>
                    Manage Product
                </a>
                <a href="logout.php" class="admin-btn logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </header>

    <main class="product-container">
        <!-- Search results indicator -->
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
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <!-- No results message -->
        <div id="noResults" class="no-results" style="display: none;">
            <i class="fas fa-search"></i>
            <h3>Tidak ada produk yang ditemukan</h3>
            <p>Coba dengan kata kunci lain</p>
        </div>
        
        <!-- Loading spinner -->
        <div id="loadingSpinner" class="loading-spinner">
            <i class="fas fa-spinner"></i>
            <p>Memuat produk...</p>
        </div>
    </main>

    <footer class="main-footer">
        <p>&copy; 2025 TokoKU Inventory System.</p>
        <p>Nama: Stanley Gilbert Lionardi</p>
        <p>NIM: 09021282328042</p>
        <p>Kelas: TIBIL P2/2023</p>
    </footer>
</body>
</html>