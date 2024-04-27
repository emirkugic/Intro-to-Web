<?php

require 'vendor/autoload.php';
require 'config.php';

Flight::before('start', function (&$params, &$output) {
    $headers = getallheaders();
    $path = Flight::request()->url;

    $excluded_paths = [
        '/auth/login',
        '/auth/register'
    ];

    if (!in_array($path, $excluded_paths)) {
        $jwt = $headers['Authorization'] ?? '';
        $jwt = str_replace('Bearer ', '', $jwt);

        if ($jwt) {
            try {
                $decoded = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key(JWT_SECRET_KEY, 'HS256'));
                Flight::set('user', (array) $decoded);
            } catch (\Exception $e) {
                Flight::halt(401, 'Unauthorized: ' . $e->getMessage());
                return;
            }
        } else {
            Flight::halt(401, 'Unauthorized: Token not provided');
            return;
        }
    }
});

require 'rest/routes/AuthRoutes.php';
require 'rest/routes/UserRoutes.php';
require 'rest/routes/ProductRoutes.php';
require 'rest/routes/CartRoutes.php';

// TODO swagger, subscription for users and credit card API with Stripe duplex communication and deployment

Flight::start();
