<?php
require_once __DIR__ . '/../../rest/services/CartService.class.php';
require_once __DIR__ . '/../../rest/services/ProductService.class.php';
require_once __DIR__ . '/../../rest/dao/ProductDao.class.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

$input = file_get_contents("php://input");
$requestData = json_decode($input, true);

$user_id = isset($requestData['user_id']) ? $requestData['user_id'] : null;

if ($user_id == null) {
    http_response_code(400);
    echo json_encode(["error" => "You must provide a user ID!"]);
    exit;
}

$cart_service = new CartService();
$product_dao = new ProductService();

$carts = $cart_service->get_carts_by_user($user_id);

$items = [];
foreach ($carts as $cart) {
    $product = $product_dao->get_product_by_id($cart['product_id']);
    if ($product) {
        $items[] = [
            "id" => $product['id'],
            "name" => $product['title'],
            "price" => (float)$product['price'],
            "image" => $product['image_url'],
            "quantity" => (int)$cart['quantity']
        ];
    }
}

echo json_encode(["items" => $items]);
