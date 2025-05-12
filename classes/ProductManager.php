<?php
class ProductManager {
    private $db;

    public function __construct() {
        $dbInstance = Database::getInstance();
        $this->db = $dbInstance->getConnection();
    }

    public function formatRupiah($number) {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }

    public function uploadImage() {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $fileName = uniqid() . '-' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;

        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowedTypes)) {
            throw new Exception('Hanya file JPG, JPEG, PNG & GIF yang diizinkan');
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            return $fileName;
        }
        return null;
    }

    public function getAllProducts($sort = 'price_asc') {
        $order = ($sort === 'price_desc') ? 'DESC' : 'ASC';
        $stmt = $this->db->prepare("SELECT * FROM products ORDER BY price $order");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function addProduct(Product $product) {
        try {
            $stmt = $this->db->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdis", 
                $product->name,
                $product->description,
                $product->price,
                $product->stock,
                $product->image
            );
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            throw new Exception("Gagal menambahkan produk: " . $e->getMessage());
        }
    }

    public function updateProduct(Product $product) {
        try {
            $oldProduct = $this->getProductById($product->id);
            
            if ($product->image) {
                if ($oldProduct['image'] && file_exists("uploads/".$oldProduct['image'])) {
                    unlink("uploads/".$oldProduct['image']);
                }
                $stmt = $this->db->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, image=? WHERE id=?");
                $stmt->bind_param("ssdisi", 
                    $product->name,
                    $product->description,
                    $product->price,
                    $product->stock,
                    $product->image,
                    $product->id
                );
            } else {
                $stmt = $this->db->prepare("UPDATE products SET name=?, description=?, price=?, stock=? WHERE id=?");
                $stmt->bind_param("ssdii", 
                    $product->name,
                    $product->description,
                    $product->price,
                    $product->stock,
                    $product->id
                );
            }
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            throw new Exception("Gagal mengupdate produk: " . $e->getMessage());
        }
    }

    public function deleteProduct($id) {
        try {
            $product = $this->getProductById($id);
            if ($product['image'] && file_exists("uploads/".$product['image'])) {
                unlink("uploads/".$product['image']);
            }
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            throw new Exception("Gagal menghapus produk: " . $e->getMessage());
        }
    }
}
?>