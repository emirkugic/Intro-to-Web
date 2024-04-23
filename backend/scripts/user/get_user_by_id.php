<?php

require_once __DIR__ . '/../../rest/services/UserService.class.php';
header('Content-Type: application/json');

$user_service = new UserService();
$user_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($user_id === null) {
    http_response_code(400);
    echo json_encode(["error" => "Missing user ID"]);
    exit;
}

$user = $user_service->get_user_by_id($user_id);
if ($user) {
    echo json_encode($user);
} else {
    http_response_code(404);
    echo json_encode(["error" => "User not found"]);
}
