<?php
session_start();
require 'classes/Database.php';
require 'classes/ProductManager.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get the search query from GET parameter
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (empty($query)) {
    // If query is empty, return all products
    $productManager = new ProductManager();
    $products = $productManager->getAllProducts();
    echo json_encode($products);
    exit();
}

// Connect to database
$db = Database::getInstance()->getConnection();

// Prepare the search query with LIKE for name and description
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
    // Format price for display
    $productManager = new ProductManager();
    $row['formatted_price'] = $productManager->formatRupiah($row['price']);
    $products[] = $row;
}

// Return the results as JSON
echo json_encode($products);
?>