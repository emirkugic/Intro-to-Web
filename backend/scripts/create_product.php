<?php
require_once __DIR__ . '/../rest/services/ProductService.class.php';

$json_str = file_get_contents('php://input');
$payload = json_decode($json_str, true);

if (empty($payload['title']) || empty($payload['price']) || empty($payload['quantity']) || empty($payload['image_url'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Required fields are missing. Title, price, quantity, and image URL must be provided.']);
    exit();
}

$product_service = new ProductService();

$product = $product_service->add_product([
    'title' => $payload['title'],
    'price' => $payload['price'],
    'quantity' => $payload['quantity'],
    'image_url' => $payload['image_url']
]);

header('Content-Type: application/json');
echo json_encode($product);
