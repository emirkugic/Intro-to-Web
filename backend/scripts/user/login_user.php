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
$login_data = json_decode($input, true);
$user_service = new UserService();

if (empty($login_data) || empty($login_data['email']) || empty($login_data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Email and password are required"]);
    exit;
}

$user = $user_service->get_user_by_email($login_data['email']);
if ($user && password_verify($login_data['password'], $user['password'])) {
    unset($user['password']);
    echo json_encode(["success" => "Login successful", "user" => $user]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password"]);
}
