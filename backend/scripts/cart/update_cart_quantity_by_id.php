<?php
require_once __DIR__ . '/../../rest/services/CartService.class.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

$input = file_get_contents("php://input");
$requestData = json_decode($input, true);

$cart_id = isset($requestData['cart_id']) ? $requestData['cart_id'] : null;
$new_quantity = isset($requestData['new_quantity']) ? $requestData['new_quantity'] : null;

if ($cart_id == null || $new_quantity === null) {
    http_response_code(400);
    echo json_encode(["error" => "Missing cart item ID or new quantity"]);
    exit;
}

$cart_service = new CartService();
$result = $cart_service->update_cart_quantity($cart_id, $new_quantity);

if ($result) {
    echo json_encode(["success" => "Cart quantity updated successfully"]);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Cart item not found or update failed"]);
}
