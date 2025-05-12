<?php
require 'classes/Database.php';
require 'classes/ProductManager.php';

header('Content-Type: application/json');

if(isset($_GET['id'])) {
    $productManager = new ProductManager();
    $product = $productManager->getProductById($_GET['id']);
    echo json_encode($product);
}
?>