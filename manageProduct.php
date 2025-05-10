<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'Database.php';
require 'Product.php';
require 'ProductManager.php';

$productManager = new ProductManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['delete'])) {
            $result = $productManager->deleteProduct($_POST['id']);
            $_SESSION['message'] = $result ? 'Produk berhasil dihapus' : 'Gagal menghapus produk';
        } else {
            $data = [
                'name' => htmlspecialchars($_POST['name']),
                'description' => htmlspecialchars($_POST['description']),
                'price' => (float)$_POST['price'],
                'stock' => (int)$_POST['stock'],
                'image' => null
            ];
            
            if (!empty($_FILES['image']['name'])) {
                $data['image'] = $productManager->uploadImage();
            }
            
            $product = new Product($data);
            
            if (isset($_POST['id'])) {
                $product->id = $_POST['id'];
                $result = $productManager->updateProduct($product);
                $_SESSION['message'] = $result ? 'Produk berhasil diupdate' : 'Gagal mengupdate produk';
            } else {
                $result = $productManager->addProduct($product);
                $_SESSION['message'] = $result ? 'Produk berhasil ditambahkan' : 'Gagal menambahkan produk';
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    header("Location: manageProduct.php");
    exit();
}

$products = $productManager->getAllProducts();
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>">
<head>
    <title>Manage Products</title>
    <?php include 'headerSet.php'; ?>
</head>
<body>
    <?php include 'message.php'; ?>
    <button class="theme-toggle" onclick="toggleTheme()">
        <?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? '🌞' : '🌙' ?>
    </button>
    <header class="admin-header">
        <h1>🛠️ Manage Products</h1>
        <!-- Search Bar -->
        <div class="search-container admin-search">
                <input type="text" id="adminSearchInput" placeholder="Cari produk..." autocomplete="off">
                <i class="fas fa-search search-icon"></i>
            </div>
        <div class="admin-controls">
            <a href="index.php" class="btn-back">← Kembali ke Beranda</a>
        </div>
    </header>

    <div class="manage-container">
        <div class="product-form">
            <h2>➕ Tambah Produk Baru</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Nama Produk" required>
                </div>
                <div class="form-group">
                    <textarea name="description" placeholder="Deskripsi Produk"></textarea>
                </div>
                <div class="form-group">
                    <input type="number" name="price" step="0.01" placeholder="Harga (Rp)" required>
                </div>
                <div class="form-group">
                    <input type="number" name="stock" placeholder="Stok" required>
                </div>
                <div class="form-group">
                    <label class="file-upload">
                        <input type="file" name="image" accept="image/*" required>
                        📁 Pilih Gambar
                    </label>
                    <small>Format: JPG,PNG,GIF</small>
                </div>
                <button type="submit" class="btn-submit">💾 Simpan Produk</button>
            </form>
        </div>

        <div class="product-list">
            <!-- Search Status -->
            <div id="adminSearchStatus" class="search-status admin-search-status" style="display: none;">
                <span>Menampilkan hasil pencarian untuk: "<span id="adminSearchQuery"></span>"</span>
                <button id="adminClearSearch" class="clear-search">
                    <i class="fas fa-times"></i> Hapus pencarian
                </button>
            </div>
            
            <!-- Product List Container -->
            <div id="productListContainer">
                <?php foreach($products as $product): ?>
                <div class="manage-product-card" data-id="<?= $product['id'] ?>">
                    <img src="uploads/<?= $product['image'] ?>" class="product-thumb" alt="<?= $product['name'] ?>">
                    <div class="product-info">
                        <h3><?= $product['name'] ?></h3>
                        <p><?= $productManager->formatRupiah($product['price']) ?></p>
                        <small>Stok: <?= $product['stock'] ?></small>
                    </div>
                    <div class="product-actions">
                        <button class="btn-edit" onclick="openEditModal(<?= $product['id'] ?>)">✏️ Edit</button>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $product['id'] ?>">
                            <button type="submit" name="delete" class="btn-delete" onclick="return confirm('Hapus produk ini?')">🗑️ Hapus</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- No Results Message -->
            <div id="adminNoResults" class="no-results admin-no-results" style="display: none;">
                <i class="fas fa-search"></i>
                <h3>Tidak ada produk yang ditemukan</h3>
                <p>Coba dengan kata kunci lain</p>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content" style="background: var(--card-bg); color: var(--text);">
            <span class="close">&times;</span>
            <h2>✏️ Edit Produk</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editId">
                <p>Nama Produk:</p>
                <div class="form-group">
                    <input type="text" name="name" id="editName" required>
                </div>
                <p>Deskripsi Produk: </p>
                <div class="form-group">
                    <textarea name="description" id="editDescription"></textarea>
                </div>
                <p>Harga (Rp): </p>
                <div class="form-group">
                    <input type="number" name="price" id="editPrice" step="0.01" required>
                </div>
                <p>Stok: </p>
                <div class="form-group">
                    <input type="number" name="stock" id="editStock" required>
                </div>
                <div class="form-group">
                    <label class="file-upload">
                        <input type="file" name="image" accept="image/*">
                        🔄 Ganti Gambar
                    </label>
                    <small>Biarkan kosong jika tidak ingin mengubah</small>
                </div>
                <button type="submit" class="btn-submit">💾 Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <footer class="main-footer">
        <p>&copy; 2025 TokoKU Inventory System.</p>
        <p>Nama: Stanley Gilbert Lionardi</p>
        <p>NIM: 09021282328042</p>
        <p>Kelas: TIBIL P2/2023</p>
    </footer>
</body>
</html>