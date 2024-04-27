<?php

require_once __DIR__ .  '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function authorize($role = "USER")
{
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        Flight::halt(401, 'Authorization header is required');
    }
    $jwt = str_replace('Bearer ', '', $headers['Authorization']);
    try {
        $decoded = JWT::decode($jwt, new Key(JWT_SECRET_KEY, 'HS256'));
        if ($decoded->role !== $role) {
            Flight::halt(403, 'Access denied');
        }
    } catch (Exception $e) {
        Flight::halt(401, 'Unauthorized: ' . $e->getMessage());
    }
}
