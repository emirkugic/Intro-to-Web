<?php

require_once __DIR__ . '/../services/ShippingAddressService.class.php';
require_once __DIR__ . '/../../middleware.php';

Flight::group('/shipping-addresses', function () {
    Flight::route('GET /user/@user_id', function ($user_id) {
        $user = Flight::get('user');
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $user_id) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        $shipping_address_service = new ShippingAddressService();
        $addresses = $shipping_address_service->get_all_addresses($user_id);
        Flight::json($addresses);
    });

    Flight::route('GET /@id', function ($id) {
        $user = Flight::get('user');
        $shipping_address_service = new ShippingAddressService();
        $address = $shipping_address_service->get_address_by_id($id);
        if (!$address) {
            Flight::halt(404, 'Address not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $address['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        Flight::json($address);
    });

    Flight::route('POST /', function () {
        $user = Flight::get('user');
        $address = Flight::request()->data->getData();
        $address['user_id'] = $user['userId'];
        $shipping_address_service = new ShippingAddressService();
        $result = $shipping_address_service->add_address($address);
        if ($result) {
            Flight::json($result, 201);
        } else {
            Flight::halt(400, 'Failed to add address');
        }
    });

    Flight::route('PUT /@id', function ($id) {
        $user = Flight::get('user');
        $shipping_address_service = new ShippingAddressService();
        $address = Flight::request()->data->getData();
        $existing_address = $shipping_address_service->get_address_by_id($id);
        if (!$existing_address) {
            Flight::halt(404, 'Address not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $existing_address['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        $updated_address = $shipping_address_service->update_address($id, $address);
        if ($updated_address) {
            Flight::json($updated_address);
        } else {
            Flight::halt(400, 'Failed to update address');
        }
    });

    Flight::route('DELETE /@id', function ($id) {
        $user = Flight::get('user');
        $shipping_address_service = new ShippingAddressService();
        $address = $shipping_address_service->get_address_by_id($id);
        if (!$address) {
            Flight::halt(404, 'Address not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $address['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        $success = $shipping_address_service->delete_address_by_id($id);
        if ($success) {
            Flight::json(['message' => 'Address successfully deleted'], 200);
        } else {
            Flight::halt(400, 'Failed to delete address');
        }
    });
});
