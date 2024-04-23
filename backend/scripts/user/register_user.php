<?php

require_once __DIR__ . '/../../rest/services/UserService.class.php';
header('Content-Type: application/json');

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
