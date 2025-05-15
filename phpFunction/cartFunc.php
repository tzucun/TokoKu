<?php
$productManager = new ProductManager();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['ajax_update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $response = ['success' => false, 'message' => '', 'new_quantity' => 0, 'new_subtotal' => 0, 'new_total' => 0];
    
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$product_id]);
        $response['success'] = true;
        $response['message'] = "Produk dihapus dari keranjang";
        $response['removed'] = true;
    } else {
        $product = $productManager->getProductById($product_id);
        
        if ($quantity > $product['stock']) {
            $response['success'] = false;
            $response['message'] = "Stok tidak mencukupi";
        } else {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            $response['success'] = true;
            $response['message'] = "Jumlah produk diperbarui";
            $response['new_quantity'] = $quantity;
            $response['new_subtotal'] = $product['price'] * $quantity;
            $response['new_subtotal_formatted'] = $productManager->formatRupiah($product['price'] * $quantity);
            
            $new_total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $new_total += $item['price'] * $item['quantity'];
            }
            $response['new_total'] = $new_total;
            $response['new_total_formatted'] = $productManager->formatRupiah($new_total);
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product = $productManager->getProductById($product_id);
    
    if ($product) {
        if (isset($_SESSION['cart'][$product_id])) {
            if ($_SESSION['cart'][$product_id]['quantity'] < $product['stock']) {
                $_SESSION['cart'][$product_id]['quantity']++;
                $_SESSION['message'] = "Jumlah {$product['name']} bertambah";
            } else {
                $_SESSION['error'] = true;
                $_SESSION['message'] = "Stok {$product['name']} tidak mencukupi";
            }
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => 1
            ];
            $_SESSION['message'] = "{$product['name']} ditambahkan ke keranjang";
        }
    }
    
    header("Location: cart.php");
    exit();
}

if (isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];
    
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['message'] = "Produk dihapus dari keranjang";
    }
    
    header("Location: cart.php");
    exit();
}

if (isset($_POST['proceed_to_checkout'])) {
    if (empty($_SESSION['cart'])) {
        $_SESSION['error'] = true;
        $_SESSION['message'] = "Keranjang kosong";
        header("Location: cart.php");
        exit();
    }
    
    $stockError = false;
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $product = $productManager->getProductById($product_id);
        if ($item['quantity'] > $product['stock']) {
            $stockError = true;
            $_SESSION['error'] = true;
            $_SESSION['message'] = "Stok {$product['name']} tidak mencukupi. Stok tersedia: {$product['stock']}";
            break;
        }
    }
    
    if ($stockError) {
        header("Location: cart.php");
        exit();
    }
    
    header("Location: checkout.php");
    exit();
}

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

?>