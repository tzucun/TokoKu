<?php
$productManager = new ProductManager();

if (!isset($_SESSION['checkout_step'])) {
    $_SESSION['checkout_step'] = 'customer_details';
}

if (isset($_POST['submit_order'])) {
    // Validate form fields
    $required_fields = ['fullname', 'email', 'phone', 'address', 'payment_method'];
    $error = false;
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $error = true;
            break;
        }
    }
    
    if ($error) {
        $_SESSION['error'] = true;
        $_SESSION['message'] = "Semua field harus diisi";
        header("Location: checkout.php");
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
    
    $_SESSION['order'] = [
        'customer' => [
            'fullname' => $_POST['fullname'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address']
        ],
        'payment_method' => $_POST['payment_method'],
        'items' => $_SESSION['cart'],
        'date' => date('Y-m-d H:i:s'),
        'delivery_estimate' => date('Y-m-d', strtotime('+3 days'))
    ];
    
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    $_SESSION['order']['total'] = $total;
    
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $product = $productManager->getProductById($product_id);
        $new_stock = $product['stock'] - $item['quantity'];
        $productManager->updateProductStock($product_id, $new_stock);
    }
    
    $_SESSION['checkout_step'] = 'payment_success';
    header("Location: payment_success.php");
    exit();
}

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>