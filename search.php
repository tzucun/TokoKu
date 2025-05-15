<?php
session_start();
require 'classes/Database.php';
require 'classes/ProductManager.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (empty($query)) {
    $productManager = new ProductManager();
    $products = $productManager->getAllProducts();
    echo json_encode($products);
    exit();
}

$db = Database::getInstance()->getConnection();

$searchQuery = "SELECT * FROM products WHERE 
               name = ? OR 
               description LIKE ?";

$searchParam = "%{$query}%";
$stmt = $db->prepare($searchQuery);
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $productManager = new ProductManager();
    $row['formatted_price'] = $productManager->formatRupiah($row['price']);
    $products[] = $row;
}

echo json_encode($products);
?>