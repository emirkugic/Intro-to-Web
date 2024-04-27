<?php


require_once __DIR__ . '/../../rest/services/ShippingAddressService.class.php';
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$shipping_address_service = new ShippingAddressService();

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if ($user_id === null) {
    http_response_code(400);
    echo json_encode(["error" => "Missing user ID"]);
    exit;
}

$addresses = $shipping_address_service->get_all_addresses($user_id);
echo json_encode(["addresses" => $addresses]);
