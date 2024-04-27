<?php

require_once __DIR__ . '/../../rest/services/ShippingAddressService.class.php';

header('Content-Type: application/json');

$shipping_address_service = new ShippingAddressService();

$input = file_get_contents("php://input");
$address_data = json_decode($input, true);

if (empty($address_data)) {
    http_response_code(400);
    echo json_encode(["error" => "Missing address data"]);
    exit;
}

$result = $shipping_address_service->add_address($address_data);
if ($result) {
    echo json_encode(["success" => "Address created successfully", "address" => $result]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to create address"]);
}
