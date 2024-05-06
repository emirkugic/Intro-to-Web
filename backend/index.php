<?php

require 'vendor/autoload.php';
require 'config.php';

Flight::route('OPTIONS /*', function () {
    error_log('OPTIONS request received');

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Max-Age: 86400");
    http_response_code(200);
    exit();
});

Flight::before('start', function (&$params, &$output) {
    $headers = getallheaders();
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestUri = $_SERVER['REQUEST_URI'];
    error_log("Request method: $requestMethod, URI: $requestUri, Headers: " . json_encode($headers));

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");

    $headers = getallheaders();
    $jwt = $headers['Authorization'] ?? '';
    $jwt = str_replace('Bearer ', '', $jwt);

    if ($jwt) {
        try {
            $decoded = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key(JWT_SECRET_KEY, 'HS256'));
            Flight::set('user', (array) $decoded);
        } catch (\Exception $e) {
            // You can choose to log the error instead of halting the application
            error_log('Unauthorized: ' . $e->getMessage());
            // If you want to halt when the token is invalid uncomment the line below:
            // Flight::halt(401, 'Unauthorized: ' . $e->getMessage());
            // To let the user pass anyway, do not halt and do not return.
        }
    }
    // If no token was provided, we don't halt the application, just continue without setting the user.
});

require 'rest/routes/AuthRoutes.php';
require 'rest/routes/UserRoutes.php';
require 'rest/routes/ProductRoutes.php';
require 'rest/routes/CartRoutes.php';
require 'rest/routes/ShippingAddressRoutes.php';

// TODO: Implement save address button, mailer for subscriptions for users, credit card API with Stripe duplex communication, and deployment

Flight::start();






/*

<?php

require 'vendor/autoload.php';
require 'config.php';


Flight::route('OPTIONS /*', function () {
    error_log('OPTIONS request received');

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Max-Age: 86400");
    http_response_code(200);
    exit();
});



Flight::before('start', function (&$params, &$output) {

    $headers = getallheaders();
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestUri = $_SERVER['REQUEST_URI'];
    error_log("Request method: $requestMethod, URI: $requestUri, Headers: " . json_encode($headers));

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");

    $headers = getallheaders();
    $path = Flight::request()->url;

    $excluded_paths = [
        '/auth/login',
        '/auth/register',
        '/products/all',
        '/products/popular',
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

// TODO swagger, CORS,  mailer for subscription for users and credit card API with Stripe duplex communication and deployment

Flight::start();


*/