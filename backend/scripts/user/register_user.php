<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../rest/services/UserService.class.php';

$input = file_get_contents("php://input");
$user_data = json_decode($input, true);
$user_service = new UserService();

if (empty($user_data)) {
    http_response_code(400);
    echo json_encode(["error" => "User data is required"]);
    exit;
}

$user_data['password'] = password_hash($user_data['password'], PASSWORD_DEFAULT);

$result = $user_service->add_user($user_data);
if ($result) {
    echo json_encode(["success" => "User registered successfully", "user" => $result]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to register user"]);
}
