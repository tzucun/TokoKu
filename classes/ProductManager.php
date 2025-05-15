<?php

class ProductManager {
    private $db;
    private $uploadDir = 'uploads/';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllProducts($sort = 'price_asc') {
        $orderBy = 'price ASC'; // Default
        
        if ($sort === 'price_desc') {
            $orderBy = 'price DESC';
        }
        
        $query = "SELECT * FROM products ORDER BY $orderBy";
        $result = $this->db->query($query);
        
        $products = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        return $products;
    }
    
    public function getProductById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    public function addProduct(Product $product) {
        $stmt = $this->db->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $product->name, $product->description, $product->price, $product->stock, $product->image);
        
        return $stmt->execute();
    }

    public function updateProduct(Product $product) {
        if ($product->image) {
            $stmt = $this->db->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssdisi", $product->name, $product->description, $product->price, $product->stock, $product->image, $product->id);
        } else {
            $stmt = $this->db->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
            $stmt->bind_param("ssdii", $product->name, $product->description, $product->price, $product->stock, $product->id);
        }
        
        return $stmt->execute();
    }

    public function deleteProduct($id) {
        // Get image filename before deleting
        $stmt = $this->db->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image = $row['image'];
            
            // Delete image file if it exists
            if ($image && file_exists($this->uploadDir . $image)) {
                unlink($this->uploadDir . $image);
            }
        }

        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }

    public function uploadImage() {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error uploading file");
        }
        
        $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            throw new Exception("File is not an image");
        }

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            throw new Exception("Sorry, only JPG, JPEG, PNG & GIF files are allowed");
        }

        $fileName = uniqid() . "." . $imageFileType;
        $targetFile = $this->uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            throw new Exception("Error moving uploaded file");
        }
        
        return $fileName;
    }

    public function formatRupiah($price) {
        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    public function updateStock($productId, $quantity) {
        $stmt = $this->db->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        $stmt->bind_param("iii", $quantity, $productId, $quantity);
        return $stmt->execute();
    }

    public function searchProducts($keyword) {
        $searchTerm = "%$keyword%";
        $stmt = $this->db->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        return $products;
    }

    public function updateProductStock($productId, $newStock) {
    try {
        $newStock = max(0, $newStock);

        $sql = "UPDATE products SET stock = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("ii", $newStock, $productId);
        
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        error_log("Error updating product stock: " . $e->getMessage());
        return false;
    }
}

}