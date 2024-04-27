<?php

require_once __DIR__ . '/../services/CartService.class.php';
require_once __DIR__ . '/../../middleware.php';

Flight::group('/carts', function () {


    Flight::route('GET /', function () {
        authorize("ADMIN");
        $cart_service = new CartService();
        $order = Flight::request()->query['order'] ?? '-id';
        $carts = $cart_service->get_all_carts($order);
        Flight::json($carts);
    });

    Flight::route('GET /@cart_id', function ($cart_id) {
        $user = Flight::get('user');
        $cart_service = new CartService();
        $cart = $cart_service->get_cart_by_id($cart_id);
        if (!$cart) {
            Flight::halt(404, 'Cart not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $cart['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        Flight::json($cart);
    });

    Flight::route('POST /', function () {
        $cart_service = new CartService();
        $cart = Flight::request()->data->getData();
        $result = $cart_service->add_cart($cart);
        if ($result) {
            Flight::json($result, 201);
        } else {
            Flight::halt(400, 'Failed to add cart');
        }
    });

    Flight::route('PUT /@cart_id', function ($cart_id) {
        $user = Flight::get('user');
        $cart_service = new CartService();
        $cart = Flight::request()->data->getData();
        $existing_cart = $cart_service->get_cart_by_id($cart_id);
        if (!$existing_cart) {
            Flight::halt(404, 'Cart not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $existing_cart['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        $updated_cart = $cart_service->update_cart($cart_id, $cart);
        if ($updated_cart) {
            Flight::json($updated_cart);
        } else {
            Flight::halt(400, 'Failed to update cart');
        }
    });

    Flight::route('DELETE /@cart_id', function ($cart_id) {
        $user = Flight::get('user');
        $cart_service = new CartService();
        $cart = $cart_service->get_cart_by_id($cart_id);
        if (!$cart) {
            Flight::halt(404, 'Cart not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $cart['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        $success = $cart_service->delete_cart_by_id($cart_id);
        if ($success) {
            Flight::json(['message' => 'Cart successfully deleted'], 200);
        } else {
            Flight::halt(400, 'Failed to delete cart');
        }
    });

    Flight::route('GET /user/@user_id', function ($user_id) {
        $user = Flight::get('user');
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $user_id) {
            Flight::halt(403, 'Access Denied');
        }
        $cart_service = new CartService();
        $carts = $cart_service->get_carts_by_user($user_id);
        Flight::json($carts);
    });

    Flight::route('PUT /quantity/@cart_id', function ($cart_id) {
        $user = Flight::get('user');
        $quantity = Flight::request()->data->getData()['quantity'];
        $cart_service = new CartService();
        $cart = $cart_service->get_cart_by_id($cart_id);
        if (!$cart) {
            Flight::halt(404, 'Cart not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $cart['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        $result = $cart_service->update_cart_quantity($cart_id, $quantity);
        if ($result) {
            Flight::json($result);
        } else {
            Flight::halt(400, 'Failed to update cart quantity');
        }
    });
});
